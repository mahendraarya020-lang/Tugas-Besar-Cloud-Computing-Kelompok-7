<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects.
     */
    public function index(Request $request)
    {
        $userId = $request->attributes->get('user_id');
        $role = $request->attributes->get('role');

        Log::info("User ID {$userId} (Role: {$role}) mengambil data daftar proyek.");

        if ($role !== 'Admin') {
            $this->checkAndSeedDummyData($userId);
        }

        if ($role === 'Admin') {
            $projects = Project::with('client')->get();
        } else {
            $projects = Project::with('client')->where('user_id', $userId)->get();
        }

        return response()->json([
            'success' => true,
            'projects' => $projects
        ]);
    }

    /**
     * Store a newly created project.
     */
    public function store(Request $request)
    {
        $userId = $request->attributes->get('user_id');
        $role = $request->attributes->get('role');

        $validator = Validator::make($request->all(), [
            'client_id' => 'required|integer|exists:clients,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:Pitching,In Progress,Review,Completed',
            'budget' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            Log::warning("Gagal membuat proyek oleh User ID {$userId} karena kesalahan validasi: ", $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate client ownership for non-Admin users
        if ($role !== 'Admin') {
            $client = Client::find($request->client_id);
            if ($client->user_id !== $userId) {
                Log::warning("Akses ilegal terdeteksi: User ID {$userId} mencoba membuat proyek untuk klien ID {$request->client_id} milik orang lain.");
                return response()->json([
                    'success' => false,
                    'message' => 'Access forbidden: Client does not belong to you'
                ], 403);
            }
        }

        $project = Project::create([
            'client_id' => $request->client_id,
            'user_id' => $userId,
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status ?? 'Pitching',
            'budget' => $request->budget,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        Log::info("Proyek baru berhasil dibuat: {$project->title} (ID: {$project->id}, dibuat oleh User ID: {$userId})");

        return response()->json([
            'success' => true,
            'message' => 'Project created successfully',
            'project' => $project
        ], 201);
    }

    /**
     * Display the specified project.
     */
    public function show(Request $request, $id)
    {
        $userId = $request->attributes->get('user_id');
        $role = $request->attributes->get('role');

        $project = Project::with(['client', 'milestones', 'invoices'])->find($id);

        if (!$project) {
            Log::warning("Pencarian proyek ID {$id} oleh User ID {$userId} gagal: tidak ditemukan.");
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        }

        if ($role !== 'Admin' && $project->user_id !== $userId) {
            Log::warning("Akses ilegal terdeteksi: User ID {$userId} mencoba mengakses proyek ID {$id} milik orang lain.");
            return response()->json([
                'success' => false,
                'message' => 'Access forbidden'
            ], 403);
        }

        Log::info("User ID {$userId} melihat detail proyek ID {$id}.");

        return response()->json([
            'success' => true,
            'project' => $project
        ]);
    }

    /**
     * Update the specified project.
     */
    public function update(Request $request, $id)
    {
        $userId = $request->attributes->get('user_id');
        $role = $request->attributes->get('role');

        $project = Project::find($id);

        if (!$project) {
            Log::warning("Pembaruan proyek ID {$id} oleh User ID {$userId} gagal: tidak ditemukan.");
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        }

        if ($role !== 'Admin' && $project->user_id !== $userId) {
            Log::warning("Akses ilegal terdeteksi: User ID {$userId} mencoba memperbarui proyek ID {$id} milik orang lain.");
            return response()->json([
                'success' => false,
                'message' => 'Access forbidden'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'client_id' => 'sometimes|required|integer|exists:clients,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|string|in:Pitching,In Progress,Review,Completed',
            'budget' => 'sometimes|required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            Log::warning("Gagal memperbarui proyek ID {$id} karena kesalahan validasi oleh User ID {$userId}: ", $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate client ownership if client is being updated
        if ($request->has('client_id') && $role !== 'Admin') {
            $client = Client::find($request->client_id);
            if ($client->user_id !== $userId) {
                Log::warning("Akses ilegal terdeteksi: User ID {$userId} mencoba memindahkan proyek ID {$id} ke klien ID {$request->client_id} milik orang lain.");
                return response()->json([
                    'success' => false,
                    'message' => 'Access forbidden: Client does not belong to you'
                ], 403);
            }
        }

        $project->update($request->only('client_id', 'title', 'description', 'status', 'budget', 'start_date', 'end_date'));

        Log::info("Proyek ID {$id} berhasil diperbarui oleh User ID {$userId}.");

        return response()->json([
            'success' => true,
            'message' => 'Project updated successfully',
            'project' => $project
        ]);
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(Request $request, $id)
    {
        $userId = $request->attributes->get('user_id');
        $role = $request->attributes->get('role');

        $project = Project::find($id);

        if (!$project) {
            Log::warning("Penghapusan proyek ID {$id} oleh User ID {$userId} gagal: tidak ditemukan.");
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        }

        if ($role !== 'Admin' && $project->user_id !== $userId) {
            Log::warning("Akses ilegal terdeteksi: User ID {$userId} mencoba menghapus proyek ID {$id} milik orang lain.");
            return response()->json([
                'success' => false,
                'message' => 'Access forbidden'
            ], 403);
        }

        $project->delete();

        Log::info("Proyek ID {$id} berhasil dihapus oleh User ID {$userId}.");

        return response()->json([
            'success' => true,
            'message' => 'Project deleted successfully'
        ]);
    }
}
