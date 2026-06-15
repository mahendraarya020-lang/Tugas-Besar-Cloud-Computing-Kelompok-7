<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    private $projectServiceUrl;

    public function __construct()
    {
        $this->projectServiceUrl = config('services.project.url', 'http://project-service:8000/api');
    }

    /**
     * Helper to get HTTP request instance pre-configured with bearer token.
     */
    private function client(Request $request)
    {
        $token = $request->cookie('token');
        if (!$token) {
            abort(401);
        }
        return Http::withToken($token);
    }

    /**
     * Show freelance project management dashboard.
     */
    public function index(Request $request)
    {
        $token = $request->cookie('token');
        if (!$token) {
            return redirect()->route('login');
        }

        try {
            // Fetch projects and clients from Project Service
            $projectsRes = $this->client($request)->get("{$this->projectServiceUrl}/projects");
            $clientsRes = $this->client($request)->get("{$this->projectServiceUrl}/clients");

            if ($projectsRes->status() == 401 || $clientsRes->status() == 401) {
                return redirect()->route('logout');
            }

            $projects = $projectsRes->json('projects') ?? [];
            $clients = $clientsRes->json('clients') ?? [];

            // Decode current user info from token (we can fetch /me from auth service if needed)
            // But let's fetch /me from Auth Service to display the logged in user name and role!
            $authServiceUrl = config('services.auth.url', 'http://auth-service:8000/api');
            $authRes = Http::withToken($token)->get("{$authServiceUrl}/me");
            $user = $authRes->json('user');

            return view('dashboard', compact('projects', 'clients', 'user'));
        } catch (\Exception $e) {
            return view('dashboard', [
                'projects' => [],
                'clients' => [],
                'user' => ['name' => 'Unknown', 'role' => 'User'],
                'error' => 'Could not connect to services: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Store new client.
     */
    public function storeClient(Request $request)
    {
        $response = $this->client($request)->post("{$this->projectServiceUrl}/clients", $request->all());

        if ($response->failed()) {
            return back()->withErrors($response->json('errors') ?? ['name' => 'Failed to create client']);
        }

        return redirect()->route('dashboard')->with('success', 'Client created successfully!');
    }

    /**
     * Store new project.
     */
    public function storeProject(Request $request)
    {
        $response = $this->client($request)->post("{$this->projectServiceUrl}/projects", $request->all());

        if ($response->failed()) {
            return back()->withErrors($response->json('errors') ?? ['title' => 'Failed to create project']);
        }

        return redirect()->route('dashboard')->with('success', 'Project created successfully!');
    }

    /**
     * Delete project.
     */
    public function deleteProject(Request $request, $id)
    {
        $response = $this->client($request)->delete("{$this->projectServiceUrl}/projects/{$id}");

        if ($response->failed()) {
            return back()->with('error', $response->json('message') ?? 'Failed to delete project');
        }

        return redirect()->route('dashboard')->with('success', 'Project deleted successfully!');
    }

    /**
     * Store milestone.
     */
    public function storeMilestone(Request $request, $projectId)
    {
        $response = $this->client($request)->post("{$this->projectServiceUrl}/projects/{$projectId}/milestones", $request->all());

        if ($response->failed()) {
            return back()->withErrors($response->json('errors') ?? ['title' => 'Failed to create milestone']);
        }

        return redirect()->route('dashboard')->with('success', 'Milestone added successfully!');
    }

    /**
     * Store invoice.
     */
    public function storeInvoice(Request $request, $projectId)
    {
        $response = $this->client($request)->post("{$this->projectServiceUrl}/projects/{$projectId}/invoices", $request->all());

        if ($response->failed()) {
            return back()->withErrors($response->json('errors') ?? ['amount' => 'Failed to create invoice']);
        }

        return redirect()->route('dashboard')->with('success', 'Invoice generated successfully!');
    }

    /**
     * Update project status.
     */
    public function updateProjectStatus(Request $request, $id)
    {
        $response = $this->client($request)->put("{$this->projectServiceUrl}/projects/{$id}", [
            'status' => $request->status
        ]);

        if ($response->failed()) {
            return back()->with('error', $response->json('message') ?? 'Failed to update project status');
        }

        return redirect()->route('dashboard')->with('success', 'Project status updated!');
    }

    /**
     * Download or view printable HTML for the specified invoice via gateway proxy.
     */
    public function downloadInvoice(Request $request, $id)
    {
        $response = $this->client($request)->get("{$this->projectServiceUrl}/invoices/{$id}/download");

        if ($response->failed()) {
            return back()->with('error', $response->json('message') ?? 'Failed to download invoice');
        }

        return response($response->body(), $response->status(), [
            'Content-Type' => $response->header('Content-Type') ?: 'text/html',
        ]);
    }
}
