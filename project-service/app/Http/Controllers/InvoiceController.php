<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices for a specific project.
     */
    public function index(Request $request, $projectId)
    {
        $userId = $request->attributes->get('user_id');
        $role = $request->attributes->get('role');

        $project = Project::find($projectId);

        if (!$project) {
            Log::warning("Pengambilan invoice untuk proyek ID {$projectId} oleh User ID {$userId} gagal: proyek tidak ditemukan.");
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        }

        if ($role !== 'Admin' && $project->user_id !== $userId) {
            Log::warning("Akses ilegal terdeteksi: User ID {$userId} mencoba melihat daftar invoice proyek ID {$projectId} milik orang lain.");
            return response()->json([
                'success' => false,
                'message' => 'Access forbidden'
            ], 403);
        }

        Log::info("User ID {$userId} mengambil daftar invoice untuk proyek ID {$projectId}.");

        $invoices = Invoice::where('project_id', $projectId)->get();

        return response()->json([
            'success' => true,
            'invoices' => $invoices
        ]);
    }

    /**
     * Store a newly created invoice (automatic generator helper).
     */
    public function store(Request $request, $projectId)
    {
        $userId = $request->attributes->get('user_id');
        $role = $request->attributes->get('role');

        $project = Project::find($projectId);

        if (!$project) {
            Log::warning("Pembuatan invoice untuk proyek ID {$projectId} oleh User ID {$userId} gagal: proyek tidak ditemukan.");
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        }

        if ($role !== 'Admin' && $project->user_id !== $userId) {
            Log::warning("Akses ilegal terdeteksi: User ID {$userId} mencoba menambahkan invoice untuk proyek ID {$projectId} milik orang lain.");
            return response()->json([
                'success' => false,
                'message' => 'Access forbidden'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
            'status' => 'nullable|string|in:unpaid,partially paid,paid',
            'due_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            Log::warning("Gagal membuat invoice untuk proyek ID {$projectId} karena kesalahan validasi oleh User ID {$userId}: ", $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $invoice = Invoice::create([
            'project_id' => $projectId,
            'amount' => $request->amount,
            'status' => $request->status ?? 'unpaid',
            'due_date' => $request->due_date,
            'issued_at' => now(),
        ]);

        Log::info("Invoice baru berhasil dibuat: ID {$invoice->id} senilai {$invoice->amount} untuk Proyek ID {$projectId} oleh User ID {$userId}");

        return response()->json([
            'success' => true,
            'message' => 'Invoice created successfully',
            'invoice' => $invoice
        ], 201);
    }

    /**
     * Display the specified invoice.
     */
    public function show(Request $request, $id)
    {
        $userId = $request->attributes->get('user_id');
        $role = $request->attributes->get('role');

        $invoice = Invoice::with(['project.client'])->find($id);

        if (!$invoice) {
            Log::warning("Pencarian invoice ID {$id} oleh User ID {$userId} gagal: tidak ditemukan.");
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found'
            ], 404);
        }

        if ($role !== 'Admin' && $invoice->project->user_id !== $userId) {
            Log::warning("Akses ilegal terdeteksi: User ID {$userId} mencoba melihat detail invoice ID {$id} milik orang lain.");
            return response()->json([
                'success' => false,
                'message' => 'Access forbidden'
            ], 403);
        }

        Log::info("User ID {$userId} melihat detail invoice ID {$id}.");

        return response()->json([
            'success' => true,
            'invoice' => $invoice
        ]);
    }

    /**
     * Download or view printable HTML for the specified invoice.
     */
    public function download(Request $request, $id)
    {
        $userId = $request->attributes->get('user_id');
        $role = $request->attributes->get('role');

        $invoice = Invoice::with(['project.client'])->find($id);

        if (!$invoice) {
            Log::warning("Pencetakan/unduhan invoice ID {$id} oleh User ID {$userId} gagal: tidak ditemukan.");
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found'
            ], 404);
        }

        if ($role !== 'Admin' && $invoice->project->user_id !== $userId) {
            Log::warning("Akses ilegal terdeteksi: User ID {$userId} mencoba mencetak/mengunduh invoice ID {$id} milik orang lain.");
            return response()->json([
                'success' => false,
                'message' => 'Access forbidden'
            ], 403);
        }

        Log::info("User ID {$userId} mencetak/mengunduh dokumen invoice ID {$id}.");

        $project = $invoice->project;
        $client = $project->client;

        // Return a clean, printable HTML layout of the invoice
        $html = "
        <html>
        <head>
            <title>Invoice #INV-{$invoice->id}</title>
            <style>
                body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; margin: 40px; }
                .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, .15); font-size: 16px; line-height: 24px; }
                .invoice-box table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; }
                .invoice-box table td { padding: 5px; vertical-align: top; }
                .invoice-box table tr td:nth-child(2) { text-align: right; }
                .invoice-box table tr.top table td { padding-bottom: 20px; }
                .invoice-box table tr.top table td.title { font-size: 45px; line-height: 45px; color: #333; font-weight: bold; }
                .invoice-box table tr.information table td { padding-bottom: 40px; }
                .invoice-box table tr.heading td { background: #eee; border-bottom: 1px solid #ddd; font-weight: bold; }
                .invoice-box table tr.details td { padding-bottom: 20px; }
                .invoice-box table tr.item td { border-bottom: 1px solid #eee; }
                .invoice-box table tr.item.last td { border-bottom: none; }
                .invoice-box table tr.total td:nth-child(2) { border-top: 2px solid #eee; font-weight: bold; }
                .status-badge { display: inline-block; padding: 5px 10px; border-radius: 4px; font-weight: bold; text-transform: uppercase; font-size: 12px; }
                .paid { background-color: #d4edda; color: #155724; }
                .partially-paid { background-color: #fff3cd; color: #856404; }
                .unpaid { background-color: #f8d7da; color: #721c24; }
            </style>
        </head>
        <body>
            <div class='invoice-box'>
                <table>
                    <tr class='top'>
                        <td colspan='2'>
                            <table>
                                <tr>
                                    <td class='title'>TUBES CLOUD COMPUTING</td>
                                    <td>
                                        Invoice #: INV-{$invoice->id}<br>
                                        Created: " . $invoice->issued_at . "<br>
                                        Due: " . ($invoice->due_date ?? 'N/A') . "
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr class='information'>
                        <td colspan='2'>
                            <table>
                                <tr>
                                    <td>
                                        <strong>Freelance Developer</strong><br>
                                        User ID: {$project->user_id}
                                    </td>
                                    <td>
                                        <strong>Client Details</strong><br>
                                        Name: {$client->name}<br>
                                        Email: {$client->email}<br>
                                        Company: " . ($client->company ?? 'N/A') . "
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr class='heading'>
                        <td>Payment Method / Status</td>
                        <td>Status</td>
                    </tr>
                    <tr class='details'>
                        <td>Postgres / Digital Billing</td>
                        <td>
                            <span class='status-badge {$invoice->status}'>{$invoice->status}</span>
                        </td>
                    </tr>
                    <tr class='heading'>
                        <td>Item / Description</td>
                        <td>Price</td>
                    </tr>
                    <tr class='item last'>
                        <td>Project: {$project->title}<br><small>{$project->description}</small></td>
                        <td>$" . number_format($invoice->amount, 2) . "</td>
                    </tr>
                    <tr class='total'>
                        <td></td>
                        <td>Total: $" . number_format($invoice->amount, 2) . "</td>
                    </tr>
                </table>
            </div>
        </body>
        </html>
        ";

        return response($html, 200)->header('Content-Type', 'text/html');
    }

    /**
     * Update the specified invoice.
     */
    public function update(Request $request, $id)
    {
        $userId = $request->attributes->get('user_id');
        $role = $request->attributes->get('role');

        $invoice = Invoice::with('project')->find($id);

        if (!$invoice) {
            Log::warning("Pembaruan invoice ID {$id} oleh User ID {$userId} gagal: tidak ditemukan.");
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found'
            ], 404);
        }

        if ($role !== 'Admin' && $invoice->project->user_id !== $userId) {
            Log::warning("Akses ilegal terdeteksi: User ID {$userId} mencoba memperbarui invoice ID {$id} milik orang lain.");
            return response()->json([
                'success' => false,
                'message' => 'Access forbidden'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|string|in:unpaid,partially paid,paid',
            'due_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            Log::warning("Gagal memperbarui invoice ID {$id} karena kesalahan validasi oleh User ID {$userId}: ", $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $invoice->update($request->only('amount', 'status', 'due_date'));

        Log::info("Invoice ID {$id} berhasil diperbarui oleh User ID {$userId}.");

        return response()->json([
            'success' => true,
            'message' => 'Invoice updated successfully',
            'invoice' => $invoice
        ]);
    }

    /**
     * Remove the specified invoice from storage.
     */
    public function destroy(Request $request, $id)
    {
        $userId = $request->attributes->get('user_id');
        $role = $request->attributes->get('role');

        $invoice = Invoice::with('project')->find($id);

        if (!$invoice) {
            Log::warning("Penghapusan invoice ID {$id} oleh User ID {$userId} gagal: tidak ditemukan.");
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found'
            ], 404);
        }

        if ($role !== 'Admin' && $invoice->project->user_id !== $userId) {
            Log::warning("Akses ilegal terdeteksi: User ID {$userId} mencoba menghapus invoice ID {$id} milik orang lain.");
            return response()->json([
                'success' => false,
                'message' => 'Access forbidden'
            ], 403);
        }

        $invoice->delete();

        Log::info("Invoice ID {$id} berhasil dihapus oleh User ID {$userId}.");

        return response()->json([
            'success' => true,
            'message' => 'Invoice deleted successfully'
        ]);
    }
}
