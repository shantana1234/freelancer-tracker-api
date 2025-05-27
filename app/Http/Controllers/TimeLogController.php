<?php

namespace App\Http\Controllers;

use App\Http\Resources\TimeLogResource;
use App\Models\DailyNotification;
use App\Models\Project;
use App\Models\TimeLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use App\Notifications\LoggedEightHours;

class TimeLogController extends Controller
{
   
    public function index(Request $request)
    {
        $user = $request->user();

        $logs = TimeLog::whereHas('project.client', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->latest()->get();

        // return response()->json(['time_logs' => $logs]);
        return TimeLogResource::collection($logs);      
    }

  
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'description' => 'nullable|string',
            'tag' => 'nullable|in:billable,non-billable',
        ], [
            'project_id.required' => 'Project is required.',
            'project_id.exists' => 'The selected project does not exist.',
            'start_time.required' => 'Start time is required.',
            'start_time.date' => 'Start time must be a valid date.',
            'end_time.required' => 'End time is required.',
            'end_time.date' => 'End time must be a valid date.',
            'end_time.after' => 'End time must be after start time.',
            'tag.in' => 'Tag must be either billable or non-billable.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $project = Project::with('client')->find($validated['project_id']);

        if ($project->client->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized to add log to this project'], 403);
        }

        $hours = round((strtotime($validated['end_time']) - strtotime($validated['start_time'])) / 3600, 2);
        if($hours > 8) {
            $tag = 'billable';
        }else{
            $tag = 'non-billable';
        }
        $log = TimeLog::create(array_merge($validated, ['hours' => $hours], ['tag' => $tag]));


        //sending maild
        $day = Carbon::parse($validated['start_time'])->format('Y-m-d');

        $dayTotal = TimeLog::whereHas('project.client', fn($q) => $q->where('user_id', $request->user()->id))
            ->whereDate('start_time', $day)
            ->sum('hours');

        $alreadySent = DailyNotification::where('user_id', $request->user()->id)
            ->where('date', $day)
            ->where('type', '8hr_log')
            ->exists();

        if ($dayTotal >= 8 && !$alreadySent) {
            // Send the email
            $request->user()->notify(new LoggedEightHours($day, $dayTotal));

            // notew that it was sent
            DailyNotification::create([
                'user_id' => $request->user()->id,
                'date' => $day,
                'type' => '8hr_log',
            ]);
        }

        return (new TimeLogResource($log->load('project.client')))
        ->additional(['message' => 'Time log created'])
        ->response()
        ->setStatusCode(201);
    }

    public function show(Request $request, TimeLog $timeLog)
    {
        if ($timeLog->project->client->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // return response()->json(['log' => $timeLog]);
        return new TimeLogResource($timeLog);
    }

    public function update(Request $request, TimeLog $timeLog)
    {
        if ($timeLog->project->client->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'start_time' => 'sometimes|date',
            'end_time' => 'sometimes|date|after:start_time',
            'description' => 'nullable|string',
            'tag' => 'nullable|in:billable,non-billable',
        ], [
            'start_time.date' => 'Start time must be a valid date.',
            'end_time.date' => 'End time must be a valid date.',
            'end_time.after' => 'End time must be after start time.',
            'tag.in' => 'Tag must be either billable or non-billable.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $start = $validated['start_time'] ?? $timeLog->start_time;
        $end = $validated['end_time'] ?? $timeLog->end_time;

        if ($start && $end) {
            $validated['hours'] = round((strtotime($end) - strtotime($start)) / 3600, 2);
            if($validated['hours'] > 8) {
                $tag = 'billable';
            }else{
                $tag = 'non-billable';
            }
            $validated['tag'] = $tag;
        }

        $timeLog->update($validated);

        // return response()->json(['message' => 'Time log updated', 'log' => $timeLog]);
         return (new TimeLogResource($timeLog->load('project.client')))
        ->additional(['message' => 'Time log updated'])
        ->response()
        ->setStatusCode(201);
    }

    public function destroy(Request $request, TimeLog $timeLog)
    {
        if ($timeLog->project->client->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $timeLog->delete();

        return response()->json(['message' => 'Time log deleted']);
    }


    public function grouped(Request $request)
    {
        $user = $request->user();
        $date     = $request->query('date');
        $from     = $request->query('from');
        $to       = $request->query('to');
        $groupBy  = $request->query('group_by', 'day');    

        $validator = Validator::make($request->all(), [
            'date' => ['nullable', 'date_format:Y-m-d'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to'   => ['nullable', 'date_format:Y-m-d'],
            'group_by' => ['in:day,week'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $query = TimeLog::whereHas('project.client', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });

        if ($date) {
            $query->whereDate('start_time', '=', $date);
        } else {
            if ($from) {
                $query->whereDate('start_time', '>=', $from);
            }
            if ($to) {
                $query->whereDate('start_time', '<=', $to);
            }
        }
        $logs = $query->get()->groupBy(function ($log) use ($groupBy) {
            return $groupBy === 'week'
                ? Carbon::parse($log->start_time)->startOfWeek()->format('Y-m-d') . ' to ' . Carbon::parse($log->start_time)->endOfWeek()->format('Y-m-d')
                : Carbon::parse($log->start_time)->format('Y-m-d');
        });

       
        $formatted = $logs->map(function ($logsInGroup, $key) {
            return [
                'period' => $key,
                'total_hours' => round($logsInGroup->sum('hours'), 2),
                'logs' => $logsInGroup
            ];
        })->values();

        return response()->json(['grouped_logs' => $formatted]);
    }

    public function report(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'client_id' => 'nullable|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'from' => 'nullable|date_format:Y-m-d',
            'to' => 'nullable|date_format:Y-m-d',
            'tag' => 'nullable|in:billable,non-billable',
            'group_by' => 'nullable|in:day,week,project'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        // return $request;

        $query = TimeLog::with('project.client')
            ->whereHas('project.client', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });

        if ($request->filled('client_id')) {
            $query->whereHas('project', fn($q) => $q->where('client_id', $request->client_id));
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('tag')) {
            $query->where('tag', $request->tag);
        }

        if ($request->filled('from')) {
            $query->whereDate('start_time', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('start_time', '<=', $request->to);
        }

        $groupBy = $request->query('group_by', 'day');
        $logs = $query->get();

        $grouped = match ($groupBy) {
            'week' => $logs->groupBy(fn($log) => Carbon::parse($log->start_time)->startOfWeek()->format('Y-m-d') . ' to ' . Carbon::parse($log->start_time)->endOfWeek()->format('Y-m-d')),
            'project' => $logs->groupBy(fn($log) => $log->project->title),
            default => $logs->groupBy(fn($log) => Carbon::parse($log->start_time)->format('Y-m-d')),
        };

        $totalHours = $grouped->flatten()->sum('hours'); // Total hours across all groups

        $response = $grouped->map(function ($group, $key) {
            return [
                'date' => $key,
                'total_hours' => round($group->sum('hours'), 2),
                // 'entries' => $group
            ];
        })->values();

        // return response()->json(['report' => $response]);
        return response()->json([
            'overall_total_hours' => round($totalHours, 2),
            'grouped_logs' => $response
        ]);

    }
    public function exportPdf(Request $request)
    {
        $user = $request->user();
        // return $user;
        $validator = Validator::make($request->all(), [
            'client_id' => 'nullable|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'from' => 'nullable|date_format:Y-m-d',
            'to' => 'nullable|date_format:Y-m-d',
            'tag' => 'nullable|in:billable,non-billable',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $query = TimeLog::with(['project.client'])
            ->whereHas('project.client', fn($q) => $q->where('user_id', $user->id));

        if ($request->filled('client_id')) {
            $query->whereHas('project', fn($q) => $q->where('client_id', $request->client_id));
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('tag')) {
            $query->where('tag', $request->tag);
        }

        if ($request->filled('from')) {
            $query->whereDate('start_time', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('start_time', '<=', $request->to);
        }

        $logs = $query->get();

        $pdf = Pdf::loadView('time_logs_pdf', ['logs' => $logs]);

        $filename = 'time_logs_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
        // return $logs;
    }

}

