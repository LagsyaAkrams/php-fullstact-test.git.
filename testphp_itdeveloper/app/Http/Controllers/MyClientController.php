<?php
// Created by Lagsya Akrama for IT Fullstack Laravel Test

namespace App\Http\Controllers;

use App\Models\MyClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class MyClientController extends Controller
{
    public function index()
    {
        $clients = MyClient::whereNull('deleted_at')->get();
        return response()->json($clients);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:250',
            'slug' => 'required|string|max:100|unique:my_client',
            'is_project' => 'in:0,1',
            'self_capture' => 'in:0,1',
            'client_prefix' => 'required|string|max:4',
            'client_logo' => 'nullable|file|image|max:2048',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:50',
        ]);

        if ($request->hasFile('client_logo')) {
            $path = $request->file('client_logo')->store('client_logo', 's3');
            $validated['client_logo'] = Storage::disk('s3')->url($path);
        }

        $client = MyClient::create($validated);

        Redis::set($client->slug, json_encode($client));
        Redis::persist($client->slug);

        return response()->json(['message' => 'Client created', 'data' => $client], 201);
    }

    public function show($id)
    {
        $client = MyClient::find($id);
        if (!$client || $client->deleted_at) {
            return response()->json(['message' => 'Client not found'], 404);
        }

        return response()->json($client);
    }

    public function update(Request $request, $id)
    {
        $client = MyClient::find($id);
        if (!$client) {
            return response()->json(['message' => 'Client not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:250',
            'slug' => 'nullable|string|max:100|unique:my_client,slug,' . $client->id,
            'is_project' => 'in:0,1',
            'self_capture' => 'in:0,1',
            'client_prefix' => 'nullable|string|max:4',
            'client_logo' => 'nullable|file|image|max:2048',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:50',
        ]);

        if ($request->hasFile('client_logo')) {
            $path = $request->file('client_logo')->store('client_logo', 's3');
            $validated['client_logo'] = Storage::disk('s3')->url($path);
        }

        $client->update($validated);

        Redis::del($client->slug);
        Redis::set($client->slug, json_encode($client));
        Redis::persist($client->slug);

        return response()->json(['message' => 'Client updated', 'data' => $client]);
    }

    public function destroy($id)
    {
        $client = MyClient::find($id);
        if (!$client) {
            return response()->json(['message' => 'Client not found'], 404);
        }

        $client->update(['deleted_at' => Carbon::now()]);
        Redis::del($client->slug);

        return response()->json(['message' => 'Client soft deleted']);
    }
}
