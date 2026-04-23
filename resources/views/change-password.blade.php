@extends('layouts.app')

@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-md-5">
            <div class="card card-box p-4">
                <div class="text-center mb-4">
                    <i class="fas fa-key" style="font-size: 3rem; color: var(--dost-blue);"></i>
                    <h2 class="mt-2" style="color: var(--dost-blue);">Change Your Password</h2>
                    <p class="text-muted">Please set a new password to continue</p>
                </div>

                <form method="POST" action="{{ route('password.change') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">New Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>

                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle"></i> Requirements: 8+ characters, uppercase, lowercase, number,
                        special character (@$!%*#?&)
                    </div>

                    <button type="submit" class="btn btn-dost w-100 py-2">
                        <i class="fas fa-save"></i> Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
