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
            --dost-sidebar-width: 260px;
            --dost-sidebar-collapsed-width: 70px;
        }

        body {
            background-color: #f1f5f9;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: var(--dost-sidebar-width);
            background-color: white;
            border-right: 1px solid #e2e8f0;
            transition: all 0.3s;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar.collapsed {
            width: var(--dost-sidebar-collapsed-width);
        }

        .sidebar.collapsed .sidebar-link span,
        .sidebar.collapsed .sidebar-heading {
            display: none;
        }

        .sidebar.collapsed .sidebar-link i {
            margin-right: 0;
        }

        .sidebar-header {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar-header .toggle-btn {
            background: none;
            border: none;
            color: var(--dost-primary);
            font-size: 1.25rem;
            cursor: pointer;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.7rem 1rem;
            color: #4a5568;
            text-decoration: none;
            transition: all 0.2s;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background-color: rgba(29, 121, 157, 0.1);
            color: var(--dost-primary);
        }

        .sidebar-link i {
            width: 1.75rem;
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }

        .sidebar-heading {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            padding: 0.5rem 1rem;
            margin-top: 0.5rem;
        }

        /* Main content */
        .main-content {
            margin-left: var(--dost-sidebar-width);
            transition: margin-left 0.3s;
            min-height: 100vh;
        }

        .main-content.expanded {
            margin-left: var(--dost-sidebar-collapsed-width);
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: none;
            }

            .sidebar.mobile-open {
                transform: translateX(0);
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            }

            .main-content,
            .main-content.expanded {
                margin-left: 0;
            }
        }

        /* Charts */
        canvas {
            max-height: 180px;
            width: 100%;
        }

        .card-header {
            background-color: white;
            font-weight: 600;
        }

        .badge {
            font-weight: 500;
        }

        /* Sidebar Base */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: var(--dost-sidebar-width);
            background-color: white;
            border-right: 1px solid #e2e8f0;
            transition: all 0.3s;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar.collapsed {
            width: var(--dost-sidebar-collapsed-width);
        }

        .sidebar.collapsed .sidebar-link span,
        .sidebar.collapsed .sidebar-heading {
            display: none;
        }

        .sidebar.collapsed .sidebar-link i {
            margin-right: 0;
        }

        /* Sidebar Header */
        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .logo-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--dost-primary);
            font-weight: bold;
        }

        .logo-img {
            height: 32px;
            margin-right: 0.5rem;
            mix-blend-mode: multiply;
        }

        .portal-text {
            font-size: 1rem;
        }

        .toggle-btn {
            background: none;
            border: none;
            color: var(--dost-primary);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0;
            line-height: 1;
        }

        /* Collapsed state header – keep logo, hide text, button to the right */
        .sidebar.collapsed .sidebar-header {
            justify-content: space-between;
            padding: 1rem 0.5rem;
        }

        .sidebar.collapsed .logo-link {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar.collapsed .logo-img {
            height: 28px;
            margin-right: 0;
        }

        .sidebar.collapsed .portal-text {
            display: none;
        }

        .sidebar.collapsed .toggle-btn {
            margin-left: auto;
        }
    </style>
</head>

<body>


    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('dashboard') }}" class="logo-link">
                <img src="{{ asset('images/dost-logo.png') }}" alt="DOST" height="32" class="logo-img"
                    onerror="this.onerror=null; this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/6/6e/Department_of_Science_and_Technology_%28DOST%29.svg/1200px-Department_of_Science_and_Technology_%28DOST%29.svg.png';">
                <span class="portal-text">KM Portal</span>
            </a>
            <button class="toggle-btn" id="toggleSidebar"><i class="fas fa-bars"></i></button>
        </div>
        <div class="sidebar-nav">
            <a href="{{ route('dashboard') }}"
                class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i> <span>Dashboard</span>
            </a>
            <div class="sidebar-heading"><span>Categories</span></div>
            @php $cats = \App\Models\Category::orderBy('cat_name')->pluck('cat_name'); @endphp
            @foreach ($cats as $cat)
                <a href="{{ route('category.show', $cat) }}"
                    class="sidebar-link {{ request()->route('name') == $cat ? 'active' : '' }}">
                    <i class="fas fa-folder"></i> <span>{{ $cat }}</span>
                </a>
            @endforeach

            <div class="sidebar-heading"><span>Actions</span></div>
            <a href="{{ route('question.create') }}" class="sidebar-link">
                <i class="fas fa-question-circle"></i> <span>Ask a Question</span>
            </a>
            @if (auth()->user()->isAdmin() || auth()->user()->isKmChampion())
                <a href="{{ route('admin.documents.pending') }}" class="sidebar-link">
                    <i class="fas fa-clock"></i> <span>Pending Approvals</span>
                </a>
            @endif
            @if (auth()->user()->isAdmin())
                <a href="{{ route('admin.users') }}" class="sidebar-link">
                    <i class="fas fa-users"></i> <span>Users</span>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="sidebar-link">
                    <i class="fas fa-tags"></i> <span>Categories Mgt</span>
                </a>
            @endif
            <a href="{{ route('documents.my-uploads') }}" class="sidebar-link">
                <i class="fas fa-upload"></i> <span>My Uploads</span>
            </a>

        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <nav class="navbar navbar-light bg-white border-bottom px-3 py-2">
            <button class="btn btn-sm btn-outline-secondary d-md-none" id="mobileMenuBtn"><i
                    class="fas fa-bars"></i></button>
            <div class="ms-auto">
                <div class="dropdown">
                    <button class="btn btn-link text-dark text-decoration-none dropdown-toggle" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle fa-lg"></i> {{ auth()->user()->user_first_name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i
                                    class="fas fa-user-edit"></i> My Profile</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i
                                        class="fas fa-sign-out-alt"></i> Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container-fluid py-3 px-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show py-2">{{ session('success') }}<button
                        type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button></div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show py-2">{{ session('error') }}<button
                        type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button></div>
            @endif
            @yield('content')
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar collapse/expand (desktop)
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleBtn = document.getElementById('toggleSidebar');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            });
        }

        // Restore sidebar state from localStorage
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
        }

        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', function() {
                sidebar.classList.toggle('mobile-open');
            });
        }

        // Close sidebar on mobile when clicking a link
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 768) {
                    sidebar.classList.remove('mobile-open');
                }
            });
        });
    </script>
    @stack('scripts')
    @include('modals.add-link')

</body>

</html>
