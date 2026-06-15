<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TUBES CLOUD COMPUTING</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            primary: '#24389c',
                            secondary: '#505f76',
                            background: '#f7f9fb',
                            outline: '#e2e8f0',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f9fb;
            color: #191c1e;
        }
        .card-shadow {
            box-shadow: 0px 1px 3px rgba(0,0,0,0.06), 0px 1px 2px rgba(0,0,0,0.04);
        }
        .glass-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0px 1px 3px rgba(0,0,0,0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            box-shadow: 0px 10px 15px -3px rgba(0,0,0,0.05), 0px 4px 6px -2px rgba(0,0,0,0.03);
            border-color: rgba(36, 56, 156, 0.2);
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="min-h-screen pb-12">
    <!-- Navigation Topbar -->
    <nav class="bg-white border-b border-brand-outline sticky top-0 z-40 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-[#24389c] to-[#3f51b5] text-white font-black text-sm shadow-sm">
                        FO
                    </div>
                    <span class="text-xl font-bold tracking-tight text-[#191c1e]">
                        TUBES CLOUD COMPUTING
                    </span>
                </div>
                <!-- User Info & Logout -->
                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold text-[#191c1e]">{{ $user['name'] ?? 'Freelancer' }}</p>
                        <p class="text-[10px] text-brand-secondary font-medium tracking-wide">
                            ROLE: 
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase {{ ($user['role'] ?? 'User') === 'Admin' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                                {{ $user['role'] ?? 'User' }}
                            </span>
                        </p>
                    </div>
                    <form action="/logout" method="POST">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-brand-secondary hover:text-[#191c1e] text-xs font-semibold border border-brand-outline transition-all duration-200">
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Grid -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        
        <!-- Admin Notice -->
        @if(($user['role'] ?? 'User') === 'Admin')
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 flex items-start gap-3">
                <span class="text-amber-500 text-lg">⚠️</span>
                <div>
                    <h3 class="text-amber-800 font-bold text-sm">Administrator Workspace</h3>
                    <p class="text-amber-700 text-xs mt-0.5">You have full system credentials. You are viewing and managing all projects and clients registered across the platform.</p>
                </div>
            </div>
        @endif

        <!-- Notifications -->
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl p-4 mb-6 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-700 rounded-xl p-4 mb-6 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <!-- Grid of Action buttons and Stats -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-extrabold text-[#191c1e] tracking-tight">Project Dashboard</h1>
                <p class="text-brand-secondary text-xs mt-0.5">Manage and track your active clients, milestones, and billing.</p>
            </div>
            <div class="flex gap-2">
                <button onclick="toggleModal('modal-client')" class="px-4 py-2 rounded-lg bg-white border border-brand-outline hover:bg-slate-50 text-brand-secondary hover:text-[#191c1e] text-xs font-bold transition-all duration-200">
                    + Add Client
                </button>
                <button onclick="toggleModal('modal-project')" class="px-4 py-2 rounded-lg bg-[#24389c] hover:bg-[#1a2c80] text-white text-xs font-bold shadow-sm transition-all duration-200">
                    + Create Project
                </button>
            </div>
        </div>

        <!-- Dashboard Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Side: Projects (2 Columns Wide) -->
            <div class="lg:col-span-2 space-y-6">
                <h2 class="text-lg font-bold text-[#191c1e] flex items-center gap-2">
                    📂 Active Projects
                    <span class="px-2 py-0.5 text-xs bg-slate-200 text-brand-secondary rounded-full font-normal">{{ count($projects) }}</span>
                </h2>

                @if(count($projects) == 0)
                    <div class="bg-white border border-brand-outline rounded-2xl p-12 text-center card-shadow">
                        <p class="text-brand-secondary text-sm">No projects found. Get started by creating your first project!</p>
                    </div>
                @else
                    @foreach($projects as $project)
                        <div class="glass-card rounded-2xl p-6 space-y-4 relative overflow-hidden"
                             style="border-top-width: 4px; border-top-color: 
                                {{ $project['status'] == 'Pitching' ? '#ff9800' : '' }}
                                {{ $project['status'] == 'In Progress' ? '#2196f3' : '' }}
                                {{ $project['status'] == 'Review' ? '#9c27b0' : '' }}
                                {{ $project['status'] == 'Completed' ? '#4caf50' : '' }}
                             ;">
                            <!-- Project Top Info -->
                            <div class="flex flex-wrap justify-between items-start gap-3">
                                <div>
                                    <h3 class="text-base font-bold text-[#191c1e]">{{ $project['title'] }}</h3>
                                    <p class="text-xs text-brand-secondary mt-0.5">
                                        Client: <span class="text-[#24389c] font-semibold">{{ $project['client']['name'] ?? 'Unknown Client' }}</span>
                                        @if(isset($project['client']['company']))
                                            <span class="text-slate-400">({{ $project['client']['company'] }})</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <!-- Status Chip -->
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                        {{ $project['status'] == 'Pitching' ? 'bg-orange-50 text-orange-700 border border-orange-200' : '' }}
                                        {{ $project['status'] == 'In Progress' ? 'bg-blue-50 text-blue-700 border border-blue-200' : '' }}
                                        {{ $project['status'] == 'Review' ? 'bg-purple-50 text-purple-700 border border-purple-200' : '' }}
                                        {{ $project['status'] == 'Completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                                    ">
                                        {{ $project['status'] }}
                                    </span>
                                    
                                    <!-- Update Status Dropdown Form -->
                                    <form action="/projects/{{ $project['id'] }}/status" method="POST" class="inline">
                                        @csrf
                                        <select name="status" onchange="this.form.submit()" class="bg-white border border-[#cbd5e1] text-[10px] rounded px-1.5 py-0.5 text-brand-secondary focus:outline-none">
                                            <option value="" disabled selected>Change Status</option>
                                            <option value="Pitching">Pitching</option>
                                            <option value="In Progress">In Progress</option>
                                            <option value="Review">Review</option>
                                            <option value="Completed">Completed</option>
                                        </select>
                                    </form>
                                </div>
                            </div>

                            <!-- Description -->
                            <p class="text-xs text-[#454652] leading-relaxed">{{ $project['description'] ?? 'No description provided.' }}</p>

                            <!-- Budget & Timeline -->
                            <div class="grid grid-cols-3 gap-4 border-t border-b border-[#eceef0] py-3 text-[11px]">
                                <div>
                                    <p class="text-slate-450 font-medium">Budget</p>
                                    <p class="text-emerald-700 font-bold mt-0.5">${{ number_format($project['budget'], 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-slate-450 font-medium">Start Date</p>
                                    <p class="text-[#191c1e] font-semibold mt-0.5">{{ $project['start_date'] ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-slate-450 font-medium">End Date</p>
                                    <p class="text-[#191c1e] font-semibold mt-0.5">{{ $project['end_date'] ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <!-- Nested Milestones & Invoices Section -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                                <!-- Milestones Subpanel -->
                                <div class="bg-[#fcfdfd] rounded-xl p-4 border border-[#eceef0]">
                                    <div class="flex justify-between items-center mb-2">
                                        <h4 class="text-[10px] font-bold uppercase text-[#505f76] tracking-wider">Milestones</h4>
                                        <button onclick="openMilestoneModal({{ $project['id'] }}, '{{ $project['title'] }}')" class="text-[10px] text-[#24389c] hover:underline font-bold">
                                            + Add
                                        </button>
                                    </div>
                                    @if(empty($project['milestones']))
                                        <p class="text-[10px] text-slate-400 italic">No milestones defined.</p>
                                    @else
                                        <ul class="space-y-1.5 max-h-32 overflow-y-auto pr-1">
                                            @foreach($project['milestones'] as $milestone)
                                                <li class="flex items-center justify-between text-xs py-1 border-b border-slate-100 last:border-b-0">
                                                    <span class="text-[#191c1e] font-medium truncate max-w-[120px]">{{ $milestone['title'] }}</span>
                                                    <span class="px-1.5 py-0.5 rounded text-[8px] font-semibold uppercase 
                                                        {{ $milestone['status'] == 'completed' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}
                                                    ">
                                                        {{ $milestone['status'] }}
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>

                                <!-- Invoices Subpanel -->
                                <div class="bg-[#fcfdfd] rounded-xl p-4 border border-[#eceef0]">
                                    <div class="flex justify-between items-center mb-2">
                                        <h4 class="text-[10px] font-bold uppercase text-[#505f76] tracking-wider">Invoices</h4>
                                        <button onclick="openInvoiceModal({{ $project['id'] }}, '{{ $project['title'] }}')" class="text-[10px] text-[#24389c] hover:underline font-bold">
                                            + Generate
                                        </button>
                                    </div>
                                    @if(empty($project['invoices']))
                                        <p class="text-[10px] text-slate-400 italic">No invoices generated.</p>
                                    @else
                                        <ul class="space-y-1.5 max-h-32 overflow-y-auto pr-1">
                                            @foreach($project['invoices'] as $invoice)
                                                <li class="flex items-center justify-between text-xs py-1 border-b border-slate-100 last:border-b-0">
                                                    <div class="flex flex-col">
                                                        <span class="text-[#191c1e] font-semibold">${{ number_format($invoice['amount'], 2) }}</span>
                                                        <span class="text-[8px] text-slate-400">INV-{{ $invoice['id'] }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="px-1.5 py-0.5 rounded text-[8px] font-semibold uppercase 
                                                            {{ $invoice['status'] == 'paid' ? 'bg-emerald-50 text-emerald-700' : '' }}
                                                            {{ $invoice['status'] == 'partially paid' ? 'bg-amber-50 text-amber-700' : '' }}
                                                            {{ $invoice['status'] == 'unpaid' ? 'bg-rose-50 text-rose-700' : '' }}
                                                        ">
                                                            {{ $invoice['status'] }}
                                                        </span>
                                                        <a href="{{ route('invoices.download', $invoice['id']) }}" target="_blank" class="text-xs hover:scale-105" title="Print Invoice">
                                                            🖨️
                                                        </a>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>

                            <!-- Project Footer Actions -->
                            <div class="flex justify-end pt-2">
                                <form action="/projects/{{ $project['id'] }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this project?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-rose-600 hover:text-rose-500 hover:underline">
                                        Delete Project
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Right Side: Clients (1 Column Wide) -->
            <div class="space-y-6">
                <h2 class="text-lg font-bold text-[#191c1e] flex items-center gap-2">
                    👥 Clients Registry
                    <span class="px-2 py-0.5 text-xs bg-slate-200 text-brand-secondary rounded-full font-normal">{{ count($clients) }}</span>
                </h2>

                <div class="bg-white border border-brand-outline rounded-2xl p-6 card-shadow">
                    @if(count($clients) == 0)
                        <p class="text-xs text-brand-secondary italic text-center py-4">No clients registered.</p>
                    @else
                        <div class="divide-y divide-slate-100 max-h-[500px] overflow-y-auto pr-1">
                            @foreach($clients as $client)
                                <div class="py-3.5 first:pt-0 last:pb-0 space-y-0.5">
                                    <h3 class="text-sm font-bold text-[#191c1e]">{{ $client['name'] }}</h3>
                                    <p class="text-xs text-brand-secondary">{{ $client['email'] }}</p>
                                    @if(isset($client['company']))
                                        <p class="text-[10px] text-[#24389c] font-semibold">Company: {{ $client['company'] }}</p>
                                    @endif
                                    @if(isset($client['phone']))
                                        <p class="text-[10px] text-slate-400">Phone: {{ $client['phone'] }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <!-- ================= MODALS ================= -->

    <!-- Add Client Modal -->
    <div id="modal-client" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-brand-outline">
            <div class="flex justify-between items-center border-b border-[#eceef0] pb-3">
                <h3 class="text-base font-bold text-[#191c1e]">Add New Client</h3>
                <button onclick="toggleModal('modal-client')" class="text-slate-400 hover:text-[#191c1e]">✕</button>
            </div>
            <form action="/clients" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-brand-secondary mb-1">Client Name</label>
                    <input type="text" name="name" required class="w-full bg-white border border-[#cbd5e1] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:outline-none focus:ring-1 focus:ring-[#24389c] focus:border-[#24389c]" placeholder="John Doe">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-brand-secondary mb-1">Email Address</label>
                    <input type="email" name="email" required class="w-full bg-white border border-[#cbd5e1] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:outline-none focus:ring-1 focus:ring-[#24389c] focus:border-[#24389c]" placeholder="john@client.com">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-brand-secondary mb-1">Phone Number (Optional)</label>
                    <input type="text" name="phone" class="w-full bg-white border border-[#cbd5e1] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:outline-none focus:ring-1 focus:ring-[#24389c] focus:border-[#24389c]" placeholder="+12345678">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-brand-secondary mb-1">Company Name (Optional)</label>
                    <input type="text" name="company" class="w-full bg-white border border-[#cbd5e1] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:outline-none focus:ring-1 focus:ring-[#24389c] focus:border-[#24389c]" placeholder="Acme Corp">
                </div>
                <button type="submit" class="w-full py-2 rounded-lg bg-[#24389c] hover:bg-[#1a2c80] text-white font-bold text-sm transition-all duration-200 shadow-sm">
                    Save Client
                </button>
            </form>
        </div>
    </div>

    <!-- Create Project Modal -->
    <div id="modal-project" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-brand-outline">
            <div class="flex justify-between items-center border-b border-[#eceef0] pb-3">
                <h3 class="text-base font-bold text-[#191c1e]">Create New Project</h3>
                <button onclick="toggleModal('modal-project')" class="text-slate-400 hover:text-[#191c1e]">✕</button>
            </div>
            <form action="/projects" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-brand-secondary mb-1">Select Client</label>
                    <select name="client_id" required class="w-full bg-white border border-[#cbd5e1] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:outline-none">
                        <option value="" disabled selected>Choose a client...</option>
                        @foreach($clients as $client)
                            <option value="{{ $client['id'] }}">{{ $client['name'] }} ({{ $client['company'] ?? 'No Company' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-brand-secondary mb-1">Project Title</label>
                    <input type="text" name="title" required class="w-full bg-white border border-[#cbd5e1] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:outline-none" placeholder="Redesign Landing Page">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-brand-secondary mb-1">Description</label>
                    <textarea name="description" class="w-full bg-white border border-[#cbd5e1] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:outline-none h-20" placeholder="Brief outline..."></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-brand-secondary mb-1">Budget ($)</label>
                        <input type="number" step="0.01" name="budget" required class="w-full bg-white border border-[#cbd5e1] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:outline-none" placeholder="1500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-secondary mb-1">Status</label>
                        <select name="status" class="w-full bg-white border border-[#cbd5e1] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:outline-none">
                            <option value="Pitching">Pitching</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Review">Review</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-brand-secondary mb-1">Start Date</label>
                        <input type="date" name="start_date" class="w-full bg-white border border-[#cbd5e1] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-secondary mb-1">End Date</label>
                        <input type="date" name="end_date" class="w-full bg-white border border-[#cbd5e1] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:outline-none">
                    </div>
                </div>
                <button type="submit" class="w-full py-2 rounded-lg bg-[#24389c] hover:bg-[#1a2c80] text-white font-bold text-sm transition-all duration-200 shadow-sm">
                    Create Project
                </button>
            </form>
        </div>
    </div>

    <!-- Add Milestone Modal -->
    <div id="modal-milestone" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-brand-outline">
            <div class="flex justify-between items-center border-b border-[#eceef0] pb-3">
                <h3 class="text-base font-bold text-[#191c1e]">Add Milestone for <span id="milestone-project-title" class="text-[#24389c]"></span></h3>
                <button onclick="toggleModal('modal-milestone')" class="text-slate-400 hover:text-[#191c1e]">✕</button>
            </div>
            <form id="form-milestone" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-brand-secondary mb-1">Milestone Title</label>
                    <input type="text" name="title" required class="w-full bg-white border border-[#cbd5e1] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:outline-none" placeholder="Wireframes Approval">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-brand-secondary mb-1">Description</label>
                    <textarea name="description" class="w-full bg-white border border-[#cbd5e1] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:outline-none h-20" placeholder="Details..."></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-brand-secondary mb-1">Due Date</label>
                        <input type="date" name="due_date" class="w-full bg-white border border-[#cbd5e1] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-secondary mb-1">Status</label>
                        <select name="status" class="w-full bg-white border border-[#cbd5e1] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:outline-none">
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="w-full py-2 rounded-lg bg-[#24389c] hover:bg-[#1a2c80] text-white font-bold text-sm transition-all duration-200 shadow-sm">
                    Save Milestone
                </button>
            </form>
        </div>
    </div>

    <!-- Generate Invoice Modal -->
    <div id="modal-invoice" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-brand-outline">
            <div class="flex justify-between items-center border-b border-[#eceef0] pb-3">
                <h3 class="text-base font-bold text-[#191c1e]">Generate Invoice for <span id="invoice-project-title" class="text-[#24389c]"></span></h3>
                <button onclick="toggleModal('modal-invoice')" class="text-slate-400 hover:text-[#191c1e]">✕</button>
            </div>
            <form id="form-invoice" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-brand-secondary mb-1">Invoice Amount ($)</label>
                    <input type="number" step="0.01" name="amount" required class="w-full bg-white border border-[#cbd5e1] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:outline-none" placeholder="500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-brand-secondary mb-1">Due Date</label>
                        <input type="date" name="due_date" class="w-full bg-white border border-[#cbd5e1] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-secondary mb-1">Payment Status</label>
                        <select name="status" class="w-full bg-white border border-[#cbd5e1] rounded-lg px-3 py-2 text-sm text-[#191c1e] focus:outline-none">
                            <option value="unpaid">Unpaid</option>
                            <option value="partially paid">Partially Paid</option>
                            <option value="paid">Paid</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="w-full py-2 rounded-lg bg-[#24389c] hover:bg-[#1a2c80] text-white font-bold text-sm transition-all duration-200 shadow-sm">
                    Generate Invoice
                </button>
            </form>
        </div>
    </div>


    <script>
        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.toggle('hidden');
        }

        function openMilestoneModal(projectId, projectTitle) {
            document.getElementById('milestone-project-title').innerText = projectTitle;
            document.getElementById('form-milestone').action = '/projects/' + projectId + '/milestones';
            toggleModal('modal-milestone');
        }

        function openInvoiceModal(projectId, projectTitle) {
            document.getElementById('invoice-project-title').innerText = projectTitle;
            document.getElementById('form-invoice').action = '/projects/' + projectId + '/invoices';
            toggleModal('modal-invoice');
        }
    </script>
</body>
</html>
