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
            --dost-blue: #000847;
            --dost-cyan: #00AEEF;
            --dost-black: #000000;
            --dost-white: #FFFFFF;
            --dost-gray: #F5F5F5;
        }

        body {
            background-color: var(--dost-gray);
            font-family: 'Arial', 'Segoe UI', sans-serif;
        }

        /* Header */
        .dost-header {
            background-color: var(--dost-white);
            border-bottom: 2px solid var(--dost-cyan);
            padding: 10px 0;
        }

        .dost-logo {
            height: 50px;
            margin-right: 15px;
        }

        .dost-logo-text {
            font-family: 'Arial Black', sans-serif;
            font-size: 1rem;
            color: var(--dost-black);
            margin: 0;
        }

        .dost-logo-sub {
            font-size: 0.6rem;
            color: var(--dost-blue);
            margin: 0;
        }

        /* Navigation Bar - Hidden on Dashboard by default */
        .nav-bar {
            background-color: var(--dost-blue);
            padding: 8px 0;
        }

        .nav-bar .nav-link {
            color: white;
            font-size: 0.75rem;
            padding: 8px 16px;
            transition: all 0.3s ease;
        }

        .nav-bar .nav-link:hover {
            background-color: #001c5a;
            border-radius: 6px;
        }

        .nav-bar .nav-link.active {
            background-color: #00AEEF;
            border-radius: 6px;
            color: var(--dost-black);
        }

        .user-dropdown {
            background-color: transparent;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            font-size: 0.75rem;
            padding: 6px 12px;
        }

        .user-dropdown:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .btn-dost {
            background-color: var(--dost-blue);
            color: white;
            border: none;
        }

        .btn-dost:hover {
            background-color: #001c5a;
            color: white;
        }

        .card-box {
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border: none;
        }

        .action-card {
            text-align: center;
            padding: 15px;
            height: 80px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .action-card i {
            font-size: 1.3rem;
            color: var(--dost-blue);
            margin-bottom: 5px;
        }

        .action-card h6 {
            font-size: 0.7rem;
            margin: 0;
            color: var(--dost-blue);
        }

        .table thead th {
            background-color: var(--dost-blue);
            color: white;
            font-weight: 500;
            font-size: 0.7rem;
            padding: 8px;
        }

        .table td {
            font-size: 0.7rem;
            padding: 6px;
            vertical-align: middle;
        }

        footer {
            text-align: center;
            padding: 15px;
            font-size: 0.65rem;
            color: #666;
            border-top: 1px solid #ddd;
            margin-top: 30px;
        }

        @media (max-width: 768px) {
            .nav-bar .nav-link {
                padding: 6px 10px;
                font-size: 0.65rem;
            }
        }
    </style>
</head>

<body>
    <!-- Header with Logo -->
    <header class="dost-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('images/dost-logo.png') }}" alt="DOST Logo" class="dost-logo"
                        onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/6/6e/Department_of_Science_and_Technology_%28DOST%29.svg/1200px-Department_of_Science_and_Technology_%28DOST%29.svg.png'">
                    <div>
                        <h1 class="dost-logo-text">DEPARTMENT OF SCIENCE AND TECHNOLOGY</h1>
                        <p class="dost-logo-sub">Knowledge Management Portal | Single Source of Truth</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation Bar - Only show on NON-dashboard pages -->
    @auth
        @if (!request()->routeIs('dashboard'))
            <div class="nav-bar">
                <div class="container">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="d-flex flex-wrap">
                            <a href="{{ route('dashboard') }}" class="nav-link">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                            <a href="{{ route('upload.form') }}"
                                class="nav-link {{ request()->routeIs('upload*') ? 'active' : '' }}">
                                <i class="fas fa-upload"></i> Upload Document
                            </a>
                            @if (auth()->user()->user_role == 'admin')
                                <a href="{{ route('admin.users') }}"
                                    class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                                    <i class="fas fa-users"></i> Manage Users
                                </a>
                                <a href="{{ route('register') }}"
                                    class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}">
                                    <i class="fas fa-user-plus"></i> Register User
                                </a>
                            @endif
                        </div>
                        <div class="dropdown">
                            <button class="btn user-dropdown dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> {{ auth()->user()->user_first_name }}
                                <span class="badge bg-info ms-1"
                                    style="font-size: 0.6rem;">{{ ucfirst(auth()->user()->user_role) }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i
                                            class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('upload.form') }}"><i
                                            class="fas fa-upload"></i> Upload</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger" href="{{ route('logout') }}"><i
                                            class="fas fa-sign-out-alt"></i> Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endauth

    <!-- Main Content -->
    <main class="container mt-3">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" style="font-size: 0.7rem; padding: 8px 12px;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" style="font-size: 0.7rem; padding: 8px 12px;">
                <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer>
        &copy; {{ date('Y') }} Department of Science and Technology. All rights reserved.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
