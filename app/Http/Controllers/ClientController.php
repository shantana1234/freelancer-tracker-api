<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

use App\Http\Resources\ClientResource;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = $request->user()->clients()->with('projects')->latest()->get();

        return ClientResource::collection($clients);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'contact_person' => ['nullable', 'string'],
        ]);

        $client = $request->user()->clients()->create($validated);

        return (new ClientResource($client->load('projects')))
            ->additional(['message' => 'Client created successfully.']);
    }

    public function show(Request $request, Client $client = null)
    {
        if (!$client) {
            return response()->json(['error' => 'Client not found'], 404);
        }

        if ($client->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return new ClientResource($client->load('projects'));
    }

    public function update(Request $request, Client $client)
    {
        if ($client->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'contact_person' => ['nullable', 'string'],
        ]);

        $client->update($validated);

        return (new ClientResource($client->load('projects')))
            ->additional(['message' => 'Client updated successfully.']);
    }

    public function destroy(Request $request, Client $client)
    {
        if ($client->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $client->delete();

        return response()->json(['message' => 'Client deleted successfully.']);
    }
}
