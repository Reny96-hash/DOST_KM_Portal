@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-box p-4">
                <div class="text-center mb-3">
                    <i class="fas fa-user-plus" style="font-size: 2rem; color: #000847;"></i>
                    <h4 style="color: #000847; font-size: 1rem; margin-top: 8px;">Register New User</h4>
                    <p class="text-muted" style="font-size: 0.7rem;">Administrator Access Only</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label" style="font-size: 0.7rem; font-weight: 600;">FULL NAME</label>
                        <input type="text" name="full_name" class="form-control" style="font-size: 0.75rem;" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label" style="font-size: 0.7rem; font-weight: 600;">DOST EMAIL</label>
                        <input type="email" name="email" class="form-control" style="font-size: 0.75rem;"
                            placeholder="name@dost.gov.ph" required>
                        <small class="text-muted" style="font-size: 0.6rem;">Must be @dost.gov.ph email address</small>
                    </div>

                    <div class="mb-2">
                        <label class="form-label" style="font-size: 0.7rem; font-weight: 600;">DIVISION</label>
                        <input type="text" name="division" class="form-control" style="font-size: 0.75rem;" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label" style="font-size: 0.7rem; font-weight: 600;">ROLE</label>
                        <select name="role" class="form-select" style="font-size: 0.75rem;" required>
                            <option value="staff">Staff</option>
                            <option value="info_owner">Info Owner</option>
                            <option value="km_champion">KM Champion</option>
                            <option value="edts_admin">EDTS Admin</option>
                            <option value="director">Director</option>
                            <option value="admin">System Administrator</option>
                        </select>
                    </div>

                    <button type="submit" class="btn w-100 mt-2"
                        style="background-color: #000847; color: white; font-size: 0.75rem; padding: 8px;">
                        <i class="fas fa-paper-plane"></i> Register & Send Credentials
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
