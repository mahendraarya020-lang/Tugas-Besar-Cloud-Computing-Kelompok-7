<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Milestone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class MilestoneController extends Controller
{
    /**
     * Display a listing of milestones for a specific project.
     */
    public function index(Request $request, $projectId)
    {
        $userId = $request->attributes->get('user_id');
        $role = $request->attributes->get('role');

        $project = Project::find($projectId);

        if (!$project) {
            Log::warning("Pengambilan milestone untuk proyek ID {$projectId} oleh User ID {$userId} gagal: proyek tidak ditemukan.");
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        }

        if ($role !== 'Admin' && $project->user_id !== $userId) {
            Log::warning("Akses ilegal terdeteksi: User ID {$userId} mencoba melihat milestone proyek ID {$projectId} milik orang lain.");
            return response()->json([
                'success' => false,
                'message' => 'Access forbidden'
            ], 403);
        }

        Log::info("User ID {$userId} mengambil data milestone untuk proyek ID {$projectId}.");

        $milestones = Milestone::where('project_id', $projectId)->get();

        return response()->json([
            'success' => true,
            'milestones' => $milestones
        ]);
    }

    /**
     * Store a newly created milestone.
     */
    public function store(Request $request, $projectId)
    {
        $userId = $request->attributes->get('user_id');
        $role = $request->attributes->get('role');

        $project = Project::find($projectId);

        if (!$project) {
            Log::warning("Gagal membuat milestone untuk proyek ID {$projectId} oleh User ID {$userId}: proyek tidak ditemukan.");
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        }

        if ($role !== 'Admin' && $project->user_id !== $userId) {
            Log::warning("Akses ilegal terdeteksi: User ID {$userId} mencoba menambahkan milestone untuk proyek ID {$projectId} milik orang lain.");
            return response()->json([
                'success' => false,
                'message' => 'Access forbidden'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'nullable|string|in:pending,completed',
        ]);

        if ($validator->fails()) {
            Log::warning("Gagal membuat milestone untuk proyek ID {$projectId} karena kesalahan validasi oleh User ID {$userId}: ", $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $milestone = Milestone::create([
            'project_id' => $projectId,
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'status' => $request->status ?? 'pending',
        ]);

        Log::info("Milestone baru berhasil dibuat: {$milestone->title} (ID: {$milestone->id}, Proyek ID: {$projectId}, dibuat oleh User ID: {$userId})");

        return response()->json([
            'success' => true,
            'message' => 'Milestone created successfully',
            'milestone' => $milestone
        ], 201);
    }

    /**
     * Display the specified milestone.
     */
    public function show(Request $request, $id)
    {
        $userId = $request->attributes->get('user_id');
        $role = $request->attributes->get('role');

        $milestone = Milestone::with('project')->find($id);

        if (!$milestone) {
            Log::warning("Pencarian milestone ID {$id} oleh User ID {$userId} gagal: tidak ditemukan.");
            return response()->json([
                'success' => false,
                'message' => 'Milestone not found'
            ], 404);
        }

        if ($role !== 'Admin' && $milestone->project->user_id !== $userId) {
            Log::warning("Akses ilegal terdeteksi: User ID {$userId} mencoba melihat detail milestone ID {$id} milik orang lain.");
            return response()->json([
                'success' => false,
                'message' => 'Access forbidden'
            ], 403);
        }

        Log::info("User ID {$userId} melihat detail milestone ID {$id}.");

        return response()->json([
            'success' => true,
            'milestone' => $milestone
        ]);
    }

    /**
     * Update the specified milestone.
     */
    public function update(Request $request, $id)
    {
        $userId = $request->attributes->get('user_id');
        $role = $request->attributes->get('role');

        $milestone = Milestone::with('project')->find($id);

        if (!$milestone) {
            Log::warning("Pembaruan milestone ID {$id} oleh User ID {$userId} gagal: tidak ditemukan.");
            return response()->json([
                'success' => false,
                'message' => 'Milestone not found'
            ], 404);
        }

        if ($role !== 'Admin' && $milestone->project->user_id !== $userId) {
            Log::warning("Akses ilegal terdeteksi: User ID {$userId} mencoba memperbarui milestone ID {$id} milik orang lain.");
            return response()->json([
                'success' => false,
                'message' => 'Access forbidden'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'sometimes|required|string|in:pending,completed',
        ]);

        if ($validator->fails()) {
            Log::warning("Gagal memperbarui milestone ID {$id} karena kesalahan validasi oleh User ID {$userId}: ", $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $milestone->update($request->only('title', 'description', 'due_date', 'status'));

        Log::info("Milestone ID {$id} berhasil diperbarui oleh User ID {$userId}.");

        return response()->json([
            'success' => true,
            'message' => 'Milestone updated successfully',
            'milestone' => $milestone
        ]);
    }

    /**
     * Remove the specified milestone from storage.
     */
    public function destroy(Request $request, $id)
    {
        $userId = $request->attributes->get('user_id');
        $role = $request->attributes->get('role');

        $milestone = Milestone::with('project')->find($id);

        if (!$milestone) {
            Log::warning("Penghapusan milestone ID {$id} oleh User ID {$userId} gagal: tidak ditemukan.");
            return response()->json([
                'success' => false,
                'message' => 'Milestone not found'
            ], 404);
        }

        if ($role !== 'Admin' && $milestone->project->user_id !== $userId) {
            Log::warning("Akses ilegal terdeteksi: User ID {$userId} mencoba menghapus milestone ID {$id} milik orang lain.");
            return response()->json([
                'success' => false,
                'message' => 'Access forbidden'
            ], 403);
        }

        $milestone->delete();

        Log::info("Milestone ID {$id} berhasil dihapus oleh User ID {$userId}.");

        return response()->json([
            'success' => true,
            'message' => 'Milestone deleted successfully'
        ]);
    }
}
