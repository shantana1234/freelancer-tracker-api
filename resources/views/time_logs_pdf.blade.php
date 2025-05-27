<!DOCTYPE html>
<html>
<head>
    <title>Time Log Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #999; padding: 6px; text-align: left; }
    </style>
</head>
<body>
    <h2>Time Log Report</h2>
    <p>Generated at: {{ now()->format('Y-m-d H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Project</th>
                <th>Client</th>
                <th>Start</th>
                <th>End</th>
                <th>Hours</th>
                <th>Tag</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $log)
            <tr>
                <td>{{ \Carbon\Carbon::parse($log->start_time)->format('Y-m-d') }}</td>
                <td>{{ $log->project->title }}</td>
                <td>{{ $log->project->client->name }}</td>
                <td>{{ \Carbon\Carbon::parse($log->start_time)->format('H:i') }}</td>
                <td>{{ \Carbon\Carbon::parse($log->end_time)->format('H:i') }}</td>
                <td>{{ $log->hours }}</td>
                <td>{{ $log->tag }}</td>
                <td>{{ $log->description }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
