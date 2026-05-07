<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DOST KM Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --dost-primary: #1d799d;
            --dost-primary-dark: #155e7a;
            --dost-secondary: #4a5568;
            --dost-gray: #6c757d;
            --dost-light: #f8f9fa;
            --dost-border: #e2e8f0;
        }

        body {
            background-color: #f1f5f9;
            font-family: 'Segoe UI', 'Arial', sans-serif;
        }

        /* Navbar */
        .navbar-custom {
            background-color: white !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border-bottom: 1px solid #e2e8f0;
        }

        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link {
            color: #1d799d !important;
        }

        .navbar-custom .nav-link:hover {
            background-color: rgba(29, 121, 157, 0.1);
            border-radius: 6px;
        }

        .navbar-custom .dropdown-toggle {
            color: #1d799d !important;
        }

        /* Cards */
        .card {
            border: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        /* Buttons */
        .btn-primary {
            background-color: var(--dost-primary);
            border: none;
        }

        .btn-primary:hover {
            background-color: var(--dost-primary-dark);
        }

        .btn-outline-primary {
            border-color: var(--dost-primary);
            color: var(--dost-primary);
        }

        .btn-outline-primary:hover {
            background-color: var(--dost-primary);
            color: white;
        }

        .btn-secondary {
            background-color: var(--dost-gray);
            border: none;
        }

        /* Badges - All Gray */
        .badge {
            font-weight: 500;
            background-color: var(--dost-gray) !important;
            color: white;
        }

        /* Tables */
        .table th {
            background-color: var(--dost-light);
            color: var(--dost-secondary);
            font-weight: 500;
            border-bottom: 1px solid var(--dost-border);
        }

        /* Footer */
        .footer {
            background-color: white;
            border-top: 1px solid var(--dost-border);
            color: var(--dost-gray);
            font-size: 0.75rem;
        }

        .navbar-brand img {
            background-color: transparent;
        }

        /* Pending count badge */
        .pending-badge {
            background-color: #dc3545;
            color: white;
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 10px;
            margin-left: 5px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ route('dashboard') }}">
                <img src="{{ asset('images/dost-logo.png') }}" alt="DOST" height="32" class="me-2"
                    style="mix-blend-mode: multiply;">
                <span>DOST KM Portal</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Dashboard - Everyone -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>

                    <!-- Upload - Everyone can upload now -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('upload.form') }}">
                            <i class="fas fa-upload"></i> Upload
                        </a>
                    </li>

                    <!-- My Uploads - Everyone -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('documents.my-uploads') }}">
                            <i class="fas fa-folder-open"></i> My Uploads
                        </a>
                    </li>

                    <!-- Pending Approvals - Only Admin and KM Champion -->
                    @if (auth()->user()->isAdmin() || auth()->user()->isKmChampion())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.documents.pending') }}">
                                <i class="fas fa-clock"></i> Pending Approvals
                                @php $pendingCount = \App\Models\Document::where('approval_status', 'pending')->count(); @endphp
                                @if ($pendingCount > 0)
                                    <span class="pending-badge">{{ $pendingCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endif

                    <!-- Manage Users - Only Admin -->
                    @if (auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.users') }}">
                                <i class="fas fa-users"></i> Users
                            </a>
                        </li>
                    @endif

                    <!-- User Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> {{ auth()->user()->user_first_name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item text-muted" href="#">
                                    <i class="fas fa-id-card"></i> Role: {{ ucfirst(auth()->user()->user_role) }}
                                </a></li>
                            <li><a class="dropdown-item text-muted" href="#">
                                    <i class="fas fa-shield-alt"></i> Clearance:
                                    {{ auth()->user()->security_clearance }}
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-3">
        <div class="container-fluid px-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show py-2">
                    {{ session('success') }}
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show py-2">
                    {{ session('error') }}
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show py-2">
                    {{ session('warning') }}
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </div>
    </main>

    <footer class="footer text-center py-2 mt-4">
        &copy; {{ date('Y') }} Department of Science and Technology - Knowledge Management Portal
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
