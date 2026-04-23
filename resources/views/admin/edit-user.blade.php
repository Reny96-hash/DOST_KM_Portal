@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-box p-4">
                <h4 style="color: #000847; font-size: 1rem; margin-bottom: 15px;">Edit User: {{ $user->user_first_name }}
                    {{ $user->user_last_name }}</h4>

                <form method="POST" action="{{ route('admin.users.update', $user->user_id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.75rem;">Division</label>
                        <input type="text" name="user_division" class="form-control" value="{{ $user->user_division }}"
                            style="font-size: 0.8rem;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.75rem;">Role</label>
                        <select name="user_role" class="form-select" style="font-size: 0.8rem;">
                            <option value="staff" {{ $user->user_role == 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="info_owner" {{ $user->user_role == 'info_owner' ? 'selected' : '' }}>Info Owner
                            </option>
                            <option value="km_champion" {{ $user->user_role == 'km_champion' ? 'selected' : '' }}>KM
                                Champion</option>
                            <option value="edts_admin" {{ $user->user_role == 'edts_admin' ? 'selected' : '' }}>EDTS Admin
                            </option>
                            <option value="director" {{ $user->user_role == 'director' ? 'selected' : '' }}>Director
                            </option>
                            <option value="admin" {{ $user->user_role == 'admin' ? 'selected' : '' }}>System Administrator
                            </option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.75rem;">Status</label>
                        <select name="user_status" class="form-select" style="font-size: 0.8rem;">
                            <option value="active" {{ $user->user_status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="disabled" {{ $user->user_status == 'disabled' ? 'selected' : '' }}>Disabled
                            </option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.users') }}" class="btn btn-secondary flex-grow-1"
                            style="font-size: 0.75rem;">Cancel</a>
                        <button type="submit" class="btn"
                            style="background-color: #000847; color: white; font-size: 0.75rem; flex-grow: 1;">Update
                            User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
