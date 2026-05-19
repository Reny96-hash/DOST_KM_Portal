@extends('layouts.app')

@section('content')
    @include('partials.breadcrumbs', [
        'breadcrumbs' => [['label' => 'Manage Users', 'url' => route('admin.users')], ['label' => 'Edit User']],
    ])
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-2">
                    <h6 class="mb-0"><i class="fas fa-user-edit"></i> Edit User</h6>
                    <small class="text-muted">{{ $user->user_first_name }} {{ $user->user_last_name }}</small>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.update', $user->user_id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Employee ID</label>
                            <input type="text" class="form-control" value="{{ $user->emp_id }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control"
                                value="{{ $user->user_first_name }} {{ $user->user_last_name }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="text" class="form-control" value="{{ $user->user_email }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Division</label>
                            <input type="text" name="user_division" class="form-control"
                                value="{{ $user->user_division }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="user_role" class="form-select" required>
                                <option value="staff" {{ $user->user_role == 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="km_champion" {{ $user->user_role == 'km_champion' ? 'selected' : '' }}>KM
                                    Champion</option>
                                <option value="admin" {{ $user->user_role == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            <small class="text-muted">
                                @if ($user->user_role == 'staff')
                                    Staff can view and download documents only.
                                @elseif($user->user_role == 'km_champion')
                                    KM Champion can approve documents and view all content.
                                @else
                                    Admin has full system access including user management.
                                @endif
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Security Clearance</label>
                            <select name="security_clearance" class="form-select" required>
                                <option value="Public" {{ $user->security_clearance == 'Public' ? 'selected' : '' }}>Public
                                </option>
                                <option value="Internal" {{ $user->security_clearance == 'Internal' ? 'selected' : '' }}>
                                    Internal</option>
                                <option value="Confidential"
                                    {{ $user->security_clearance == 'Confidential' ? 'selected' : '' }}>Confidential
                                </option>
                                <option value="Secret" {{ $user->security_clearance == 'Secret' ? 'selected' : '' }}>Secret
                                </option>
                                <option value="Top Secret"
                                    {{ $user->security_clearance == 'Top Secret' ? 'selected' : '' }}>Top Secret</option>
                            </select>
                            <small class="text-muted">Determines which documents the user can view.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="user_status" class="form-select" required>
                                <option value="active" {{ $user->user_status == 'active' ? 'selected' : '' }}>Active
                                </option>
                                <option value="disabled" {{ $user->user_status == 'disabled' ? 'selected' : '' }}>Disabled
                                </option>
                            </select>
                            <small class="text-muted">Disabled users cannot login.</small>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.users') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
