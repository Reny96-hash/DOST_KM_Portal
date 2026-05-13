@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">My Profile</div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label>First Name</label>
                        <input type="text" name="user_first_name" class="form-control"
                            value="{{ old('user_first_name', $user->user_first_name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Last Name</label>
                        <input type="text" name="user_last_name" class="form-control"
                            value="{{ old('user_last_name', $user->user_last_name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="user_email" class="form-control"
                            value="{{ old('user_email', $user->user_email) }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Division</label>
                        <input type="text" name="user_division" class="form-control"
                            value="{{ old('user_division', $user->user_division) }}">
                    </div>
                    <div class="mb-3">
                        <label>Designation</label>
                        <input type="text" name="user_designation" class="form-control"
                            value="{{ old('user_designation', $user->user_designation) }}">
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection
