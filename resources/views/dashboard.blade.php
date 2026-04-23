@extends('layouts.app')

@section('content')
    <div style="max-height: calc(100vh - 150px); overflow-y: auto; padding-right: 5px;">
        <!-- Welcome Card - Compact -->
        <div class="card card-box p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h4 style="color: #000847; font-size: 1rem; margin: 0;">Welcome, {{ auth()->user()->user_first_name }}!
                    </h4>
                    <p class="text-muted" style="font-size: 0.7rem; margin: 2px 0 0 0;">
                        <strong>Division:</strong> {{ auth()->user()->user_division ?? 'Not specified' }} |
                        <strong>Role:</strong> {{ ucfirst(auth()->user()->user_role) }}
                    </p>
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> {{ auth()->user()->user_first_name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('logout') }}"><i class="fas fa-sign-out-alt"></i>
                                Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Action Cards - Dashboard Only -->
        <div class="row mb-3">
            <div class="col-md-6 mb-2">
                <a href="{{ route('upload.form') }}" class="text-decoration-none">
                    <div class="card card-box action-card" style="padding: 15px; height: 80px;">
                        <i class="fas fa-upload" style="font-size: 1.3rem;"></i>
                        <h6 style="font-size: 0.75rem; margin-top: 5px;">Upload Document</h6>
                    </div>
                </a>
            </div>
            @if (auth()->user()->user_role == 'admin')
                <div class="col-md-6 mb-2">
                    <a href="{{ route('admin.users') }}" class="text-decoration-none">
                        <div class="card card-box action-card" style="padding: 15px; height: 80px;">
                            <i class="fas fa-users" style="font-size: 1.3rem;"></i>
                            <h6 style="font-size: 0.75rem; margin-top: 5px;">Manage Users</h6>
                        </div>
                    </a>
                </div>
            @endif
        </div>

        <!-- Document Repository Section -->
        <div class="card card-box p-3">
            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
                <h5 style="color: #000847; font-size: 0.85rem; margin: 0;"><i class="fas fa-database"></i> Document
                    Repository</h5>
                <form method="GET" action="{{ route('search') }}" class="d-flex gap-2 flex-wrap">
                    <div style="position: relative;">
                        <i class="fas fa-search"
                            style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); font-size: 0.7rem; color: #6c757d;"></i>
                        <input type="text" name="search" placeholder="Search documents..."
                            value="{{ request('search') }}"
                            style="font-size: 0.7rem; padding: 5px 10px 5px 28px; width: 180px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    <select name="category"
                        style="font-size: 0.7rem; padding: 5px 10px; border: 1px solid #ddd; border-radius: 4px; background-color: white;">
                        <option value="">All Categories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                {{ $cat }}</option>
                        @endforeach
                    </select>
                    <select name="sort"
                        style="font-size: 0.7rem; padding: 5px 10px; border: 1px solid #ddd; border-radius: 4px; background-color: white;">
                        <option value="desc" {{ request('sort', 'desc') == 'desc' ? 'selected' : '' }}>Newest First
                        </option>
                        <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                    <button type="submit" class="btn"
                        style="background-color: #000847; color: white; font-size: 0.7rem; padding: 5px 12px; border: none; border-radius: 4px;">
                        <i class="fas fa-sliders-h"></i> Sort
                    </button>
                    @if (request('search') || request('category') || request('sort'))
                        <a href="{{ route('dashboard') }}" class="btn"
                            style="background-color: #6c757d; color: white; font-size: 0.7rem; padding: 5px 12px; border: none; border-radius: 4px; text-decoration: none;">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    @endif
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="background-color: #000847; color: white; padding: 8px;">Title</th>
                            <th style="background-color: #000847; color: white; padding: 8px;">Description</th>
                            <th style="background-color: #000847; color: white; padding: 8px;">Category</th>
                            <th style="background-color: #000847; color: white; padding: 8px;">Uploaded By</th>
                            <th style="background-color: #000847; color: white; padding: 8px;">Date</th>
                            <th style="background-color: #000847; color: white; padding: 8px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documents as $doc)
                            <tr>
                                <td style="padding: 8px;"><strong>{{ $doc->doc_title }}</strong></td>
                                <td style="padding: 8px;" title="{{ $doc->doc_description }}">
                                    {{ Str::limit($doc->doc_description, 60) ?: 'No description' }}
            </div>
        </div>
        <td style="padding: 8px;">{{ $doc->doc_category }}</td>
        <td style="padding: 8px;">{{ $doc->user->user_first_name ?? 'Unknown' }}</td>
        <td style="padding: 8px;">{{ $doc->created_at->format('Y-m-d') }}</td>
        <td style="padding: 8px;">
            <a href="{{ route('download', $doc->doc_id) }}" class="btn btn-sm"
                style="background-color: #000847; color: white; font-size: 0.65rem; padding: 4px 10px; text-decoration: none; border-radius: 4px;">
                <i class="fas fa-download"></i> Download
            </a>
        </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center" style="padding: 20px;">No documents found.</td>
        </tr>
        @endforelse
        </tbody>
        </table>
    </div>

    <div class="mt-2">
        {{ $documents->appends(request()->query())->links() }}
    </div>
    </div>
    </div>
@endsection
