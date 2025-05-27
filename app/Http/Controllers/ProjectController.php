<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Resources\ProjectResource;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $projects = Project::with('client')
            ->whereHas('client', fn($q) => $q->where('user_id', $user->id))
            ->latest()
            ->get();
        return ProjectResource::collection($projects);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => ['required', 'exists:clients,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,completed'],
            'deadline' => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $client = Client::find($validated['client_id']);

        if ($client->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized to create project for this client'], 403);
        }

        $project = $client->projects()->create($validated);
        $project->load('client');

        return (new ProjectResource($project))
            ->additional(['message' => 'Project created successfully.']);
    }

    public function show(Request $request, Project $project)
    {
        if ($project->client->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $project->load('client');
        return new ProjectResource($project);
    }

    public function update(Request $request, Project $project)
    {
        if ($project->client->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:active,completed'],
            'deadline' => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $project->update($validator->validated());
        $project->load('client');

        return (new ProjectResource($project))
            ->additional(['message' => 'Project updated successfully.']);
    }

    public function destroy(Request $request, Project $project)
    {
        if ($project->client->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $project->delete();

        return response()->json(['message' => 'Project deleted successfully.']);
    }
}
