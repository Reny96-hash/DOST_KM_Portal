@extends('layouts.app')

@section('content')
    @include('partials.breadcrumbs', ['breadcrumbs' => [['label' => 'Manage Users']]])

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-2">
            <h6 class="mb-0"><i class="fas fa-users"></i> Manage Users</h6>
            <small class="text-muted">Users registered via self-registration. Edit to promote roles.</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">ID</th>
                            <th>Employee ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Division</th>
                            <th>Role</th>
                            <th>Clearance</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td class="ps-3">{{ $user->user_id }}</td>
                                <td>{{ $user->emp_id }}</td>
                                <td>{{ $user->user_first_name }} {{ $user->user_last_name }}</td>
                                <td>{{ $user->user_email }}</td>
                                <td>{{ $user->user_division ?? '-' }}</td>
                                <td>
                                    @if ($user->isAdmin())
                                        <span class="badge bg-primary">Admin</span>
                                    @elseif($user->isKmChampion())
                                        <span class="badge bg-info text-dark">KM Champion</span>
                                    @else
                                        <span class="badge bg-secondary">Staff</span>
                                    @endif
                                </td>
                                <td>{{ $user->security_clearance ?? '-' }}</td>
                                <td>
                                    @if ($user->user_status == 'active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Disabled</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('admin.users.edit', $user->user_id) }}"
                                        class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if ($user->user_id != auth()->user()->user_id)
                                        <form method="POST" action="{{ route('admin.users.destroy', $user->user_id) }}"
                                            style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"
                                                onclick="return confirm('Delete this user?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-2 border-top">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
