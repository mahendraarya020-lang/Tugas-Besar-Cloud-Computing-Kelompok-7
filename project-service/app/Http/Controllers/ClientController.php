<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    /**
     * Display a listing of clients.
     */
    public function index(Request $request)
    {
        $userId = $request->attributes->get('user_id');
        $role = $request->attributes->get('role');

        Log::info("User ID {$userId} (Role: {$role}) mengambil data daftar klien.");

        if ($role !== 'Admin') {
            $this->checkAndSeedDummyData($userId);
        }

        if ($role === 'Admin') {
            $clients = Client::all();
        } else {
            $clients = Client::where('user_id', $userId)->get();
        }

        return response()->json([
            'success' => true,
            'clients' => $clients
        ]);
    }

    /**
     * Store a newly created client.
     */
    public function store(Request $request)
    {
        $userId = $request->attributes->get('user_id');

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clients',
            'phone' => 'nullable|string',
            'company' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::warning("Gagal membuat klien oleh User ID {$userId} karena kesalahan validasi: ", $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $client = Client::create([
            'user_id' => $userId,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company' => $request->company,
        ]);

        Log::info("Klien baru berhasil dibuat: {$client->name} (ID: {$client->id}, dibuat oleh User ID: {$userId})");

        return response()->json([
            'success' => true,
            'message' => 'Client created successfully',
            'client' => $client
        ], 201);
    }

    /**
     * Display the specified client.
     */
    public function show(Request $request, $id)
    {
        $userId = $request->attributes->get('user_id');
        $role = $request->attributes->get('role');

        $client = Client::find($id);

        if (!$client) {
            Log::warning("Pencarian klien ID {$id} oleh User ID {$userId} gagal: tidak ditemukan.");
            return response()->json([
                'success' => false,
                'message' => 'Client not found'
            ], 404);
        }

        if ($role !== 'Admin' && $client->user_id !== $userId) {
            Log::warning("Akses ilegal terdeteksi: User ID {$userId} mencoba melihat detail klien ID {$id} milik orang lain.");
            return response()->json([
                'success' => false,
                'message' => 'Access forbidden'
            ], 403);
        }

        Log::info("User ID {$userId} melihat detail klien ID {$id}.");

        return response()->json([
            'success' => true,
            'client' => $client
        ]);
    }

    /**
     * Update the specified client.
     */
    public function update(Request $request, $id)
    {
        $userId = $request->attributes->get('user_id');
        $role = $request->attributes->get('role');

        $client = Client::find($id);

        if (!$client) {
            Log::warning("Pembaruan klien ID {$id} oleh User ID {$userId} gagal: tidak ditemukan.");
            return response()->json([
                'success' => false,
                'message' => 'Client not found'
            ], 404);
        }

        if ($role !== 'Admin' && $client->user_id !== $userId) {
            Log::warning("Akses ilegal terdeteksi: User ID {$userId} mencoba memperbarui klien ID {$id} milik orang lain.");
            return response()->json([
                'success' => false,
                'message' => 'Access forbidden'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:clients,email,' . $id,
            'phone' => 'nullable|string',
            'company' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::warning("Gagal memperbarui klien ID {$id} karena kesalahan validasi oleh User ID {$userId}: ", $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $client->update($request->only('name', 'email', 'phone', 'company'));

        Log::info("Klien ID {$id} berhasil diperbarui oleh User ID {$userId}.");

        return response()->json([
            'success' => true,
            'message' => 'Client updated successfully',
            'client' => $client
        ]);
    }

    /**
     * Remove the specified client from storage.
     */
    public function destroy(Request $request, $id)
    {
        $userId = $request->attributes->get('user_id');
        $role = $request->attributes->get('role');

        $client = Client::find($id);

        if (!$client) {
            Log::warning("Penghapusan klien ID {$id} oleh User ID {$userId} gagal: tidak ditemukan.");
            return response()->json([
                'success' => false,
                'message' => 'Client not found'
            ], 404);
        }

        if ($role !== 'Admin' && $client->user_id !== $userId) {
            Log::warning("Akses ilegal terdeteksi: User ID {$userId} mencoba menghapus klien ID {$id} milik orang lain.");
            return response()->json([
                'success' => false,
                'message' => 'Access forbidden'
            ], 403);
        }

        $client->delete();

        Log::info("Klien ID {$id} berhasil dihapus oleh User ID {$userId}.");

        return response()->json([
            'success' => true,
            'message' => 'Client deleted successfully'
        ]);
    }
}
