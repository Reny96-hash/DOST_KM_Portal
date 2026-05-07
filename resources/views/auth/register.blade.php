<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DOST KM Portal - Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #269dc700 0%, #1a6d8f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-container {
            max-width: 550px;
            width: 100%;
            margin: 20px;
        }

        .register-card {
            background: transparent;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        /* Header with WHITE background */
        .dost-header {
            background-color: #FFFFFF;
            padding: 35px 20px;
            text-align: center;
        }

        .dost-logo {
            max-width: 90px;
            margin-bottom: 10px;
        }

        .dost-subtitle {
            font-family: 'Arial Black', sans-serif;
            color: #269dc7;
            font-size: 0.85rem;
            letter-spacing: 1px;
            margin: 8px 0 0 0;
            text-transform: uppercase;
        }

        /* Form area with color #1d799d */
        .form-area {
            background-color: #1d799d;
            padding: 35px 30px 30px 30px;
        }

        .form-area h3 {
            color: #FFFFFF;
            font-size: 1.4rem;
            font-weight: bold;
            margin-bottom: 8px;
            text-align: center;
        }

        .form-area .subtitle {
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.7rem;
            margin-bottom: 25px;
        }

        .form-label {
            font-size: 0.7rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: #FFFFFF;
        }

        .input-group {
            margin-bottom: 15px;
        }

        .input-group-text {
            background-color: white;
            border-right: none;
            border: 1px solid #ddd;
        }

        .input-group-text i {
            color: #1d799d;
        }

        .form-control {
            border-left: none;
            font-size: 0.8rem;
            border: 1px solid #ddd;
            color: #333;
        }

        .form-control::placeholder {
            color: #999;
            font-size: 0.75rem;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #FFFFFF;
            border-left: none;
        }

        .input-group:focus-within .input-group-text {
            border-color: #FFFFFF;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
            border-left: none;
        }

        .invalid-feedback {
            font-size: 0.65rem;
            color: #ffcccc;
            display: none;
            margin-top: 5px;
        }

        .invalid-feedback.show {
            display: block;
        }

        /* Button styling */
        .btn-register {
            background-color: #FFFFFF;
            color: #1d799d;
            padding: 12px;
            font-size: 0.85rem;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            width: 100%;
            margin-top: 15px;
            transition: all 0.3s;
        }

        .btn-register:hover {
            background-color: #f0f0f0;
            transform: translateY(-1px);
        }

        .btn-register:disabled {
            background-color: #cccccc;
            color: #666;
            cursor: not-allowed;
        }

        /* Links */
        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 0.7rem;
        }

        .login-link a {
            color: #FFFFFF;
            text-decoration: none;
            opacity: 0.9;
        }

        .login-link a:hover {
            opacity: 1;
            text-decoration: underline;
        }

        .alert {
            padding: 10px 15px;
            font-size: 0.7rem;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        hr {
            margin: 20px 0;
            border-color: rgba(255, 255, 255, 0.2);
        }

        .password-requirements {
            background-color: rgba(255, 255, 255, 0.15);
            padding: 12px;
            border-radius: 8px;
            font-size: 0.65rem;
            margin-bottom: 15px;
            color: rgba(255, 255, 255, 0.9);
        }

        .password-requirements ul {
            margin-left: 20px;
            margin-top: 5px;
        }

        .password-requirements li {
            margin-bottom: 3px;
        }

        small.text-muted {
            color: rgba(255, 255, 255, 0.7) !important;
            font-size: 0.6rem;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="register-card">
            <!-- Header with WHITE background -->
            <div class="dost-header">
                <img src="{{ asset('images/dost-logo.png') }}" alt="DOST Logo" class="dost-logo"
                    onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/6/6e/Department_of_Science_and_Technology_%28DOST%29.svg/1200px-Department_of_Science_and_Technology_%28DOST%29.svg.png'">
                <p class="dost-subtitle">KNOWLEDGE MANAGEMENT PORTAL</p>
            </div>

            <!-- Form area with color #1d799d -->
            <div class="form-area">
                <h3>REGISTER</h3>
                <p class="subtitle">Create your DOST KM Portal Account</p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <p class="mb-0">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Employee ID *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                <input type="text" name="emp_id" class="form-control" value="{{ old('emp_id') }}"
                                    placeholder="e.g., DOST-2024-001" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Middle Initial</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" name="user_middle_initial" class="form-control"
                                    value="{{ old('user_middle_initial') }}" maxlength="5" placeholder="A">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" name="user_first_name" class="form-control"
                                    value="{{ old('user_first_name') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" name="user_last_name" class="form-control"
                                    value="{{ old('user_last_name') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Division/Office</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-building"></i></span>
                            <input type="text" name="user_division" class="form-control"
                                value="{{ old('user_division') }}" placeholder="e.g., Information Technology Division">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">DOST Email *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="user_email" class="form-control" value="{{ old('user_email') }}"
                                placeholder="name@dost.gov.ph" required>
                        </div>
                        <small class="text-muted">Must be a valid @dost.gov.ph email address</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirm Password *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-check"></i></span>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="password-requirements">
                        <i class="fas fa-info-circle"></i> <strong>Password Requirements:</strong>
                        <ul class="mb-0">
                            <li>Minimum 8 characters</li>
                            <li>At least one uppercase letter (A-Z)</li>
                            <li>At least one lowercase letter (a-z)</li>
                            <li>At least one number (0-9)</li>
                            <li>At least one special character (@$!%*#?&)</li>
                        </ul>
                    </div>

                    <button type="submit" class="btn-register">
                        <i class="fas fa-user-plus"></i> REGISTER
                    </button>

                    <hr>

                    <div class="login-link">
                        Already have an account? <a href="{{ route('login') }}">Login here</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
