@extends('layouts.app')

@section('content')
    <div class="card card-box p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 style="color: #000847; font-size: 1rem; margin: 0;"><i class="fas fa-users"></i> Manage Users</h4>
            <a href="{{ route('register') }}" class="btn"
                style="background-color: #000847; color: white; font-size: 0.7rem; padding: 5px 12px;">
                <i class="fas fa-user-plus"></i> Add New User
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" style="font-size: 0.7rem; padding: 8px;">
                {{ session('success') }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" style="font-size: 0.7rem; padding: 8px;">
                {{ session('error') }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div style="max-height: 500px; overflow-y: auto;">
            <table class="table table-bordered" style="width: 100%;">
                <thead style="background-color: #000847; color: white;">
                    <tr>
                        <th style="padding: 8px;">ID</th>
                        <th style="padding: 8px;">Employee ID</th>
                        <th style="padding: 8px;">Name</th>
                        <th style="padding: 8px;">Email</th>
                        <th style="padding: 8px;">Division</th>
                        <th style="padding: 8px;">Role</th>
                        <th style="padding: 8px;">Status</th>
                        <th style="padding: 8px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td style="padding: 8px;">{{ $user->user_id }}</td>
                            <td style="padding: 8px;">{{ $user->emp_id }}</td>
                            <td style="padding: 8px;">{{ $user->user_first_name }} {{ $user->user_last_name }}</td>
                            <td style="padding: 8px;">{{ $user->user_email }}</td>
                            <td style="padding: 8px;">{{ $user->user_division ?? '-' }}</td>
                            <td style="padding: 8px;">{{ ucfirst(str_replace('_', ' ', $user->user_role)) }}</td>
                            <td style="padding: 8px;">{{ ucfirst($user->user_status) }}</td>
                            <td style="padding: 8px;">
                                <a href="{{ route('admin.users.edit', $user->user_id) }}" class="btn btn-sm"
                                    style="background-color: #6c757d; color: white; font-size: 0.6rem; padding: 4px 8px; text-decoration: none; border-radius: 3px;">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('admin.users.destroy', $user->user_id) }}"
                                    style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm"
                                        style="background-color: #6c757d; color: white; font-size: 0.6rem; padding: 4px 8px; border: none; border-radius: 3px; cursor: pointer;"
                                        onclick="return confirm('Delete this user?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $users->links() }}
        </div>
    </div>
@endsection
