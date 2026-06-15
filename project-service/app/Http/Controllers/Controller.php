<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function checkAndSeedDummyData($userId)
    {
        if (!$userId) {
            return;
        }

        // Check if this user has any clients to prevent duplicate seeding
        if (\App\Models\Client::where('user_id', $userId)->exists()) {
            return;
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($userId) {
                // Client 1
                $client1 = \App\Models\Client::create([
                    'user_id' => $userId,
                    'name' => 'Budi Santoso',
                    'email' => 'budi.santoso+user' . $userId . '@startupcorp.id',
                    'phone' => '081234567890',
                    'company' => 'PT Startup Mandiri',
                ]);

                // Client 2
                $client2 = \App\Models\Client::create([
                    'user_id' => $userId,
                    'name' => 'Sarah Wijaya',
                    'email' => 'sarah.w+user' . $userId . '@creativeagency.com',
                    'phone' => '087766554433',
                    'company' => 'Creative Design Studio',
                ]);

                // Project 1 (In Progress)
                $project1 = \App\Models\Project::create([
                    'client_id' => $client1->id,
                    'user_id' => $userId,
                    'title' => 'E-Commerce Platform Development',
                    'description' => 'Membangun website e-commerce modular dengan Laravel dan Postgres untuk sistem pembayaran nasional.',
                    'status' => 'In Progress',
                    'budget' => 15000000.00,
                    'start_date' => '2026-06-01',
                    'end_date' => '2026-08-31',
                ]);

                // Project 2 (Completed)
                $project2 = \App\Models\Project::create([
                    'client_id' => $client1->id,
                    'user_id' => $userId,
                    'title' => 'Company Profile Website',
                    'description' => 'Redesain landing page utama dan optimasi SEO.',
                    'status' => 'Completed',
                    'budget' => 5000000.00,
                    'start_date' => '2026-05-01',
                    'end_date' => '2026-05-25',
                ]);

                // Project 3 (Review)
                $project3 = \App\Models\Project::create([
                    'client_id' => $client2->id,
                    'user_id' => $userId,
                    'title' => 'Mobile Apps Interface Design',
                    'description' => 'Mendesain wireframe dan UI/UX mobile app untuk aplikasi logistik.',
                    'status' => 'Review',
                    'budget' => 8000000.00,
                    'start_date' => '2026-05-15',
                    'end_date' => '2026-06-30',
                ]);

                // Milestones for Project 1
                \App\Models\Milestone::create([
                    'project_id' => $project1->id,
                    'title' => 'Database & Auth Setup',
                    'description' => 'Setup database skema relasional dan autentikasi JWT.',
                    'due_date' => '2026-06-15',
                    'status' => 'completed',
                ]);

                \App\Models\Milestone::create([
                    'project_id' => $project1->id,
                    'title' => 'Payment Gateway Integration',
                    'description' => 'Integrasi Midtrans API sandbox.',
                    'due_date' => '2026-07-10',
                    'status' => 'pending',
                ]);

                // Invoices for Project 1
                \App\Models\Invoice::create([
                    'project_id' => $project1->id,
                    'amount' => 5000000.00,
                    'status' => 'paid',
                    'due_date' => '2026-06-10',
                    'issued_at' => now(),
                ]);

                \App\Models\Invoice::create([
                    'project_id' => $project1->id,
                    'amount' => 10000000.00,
                    'status' => 'unpaid',
                    'due_date' => '2026-08-30',
                    'issued_at' => now(),
                ]);
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Seeding dummy data race condition or error: " . $e->getMessage());
        }
    }
}
