<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DOST KM Portal - Login</title>
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
        }

        .login-container {
            max-width: 420px;
            width: 100%;
            margin: 20px;
        }

        .login-card {
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

        /* Form area with NEW color #1d799d */
        .form-area {
            background-color: #1d799d;
            padding: 35px 30px 30px 30px;
        }

        /* Login title - WHITE for contrast */
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
            margin-bottom: 20px;
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

        /* Button - WHITE with teal text */
        .btn-login {
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

        .btn-login:hover {
            background-color: #f0f0f0;
            transform: translateY(-1px);
        }

        .btn-login:disabled {
            background-color: #cccccc;
            color: #666;
            cursor: not-allowed;
        }

        /* Links - WHITE */
        .links {
            text-align: center;
            margin-top: 20px;
            font-size: 0.7rem;
        }

        .links a {
            color: #FFFFFF;
            text-decoration: none;
            opacity: 0.9;
        }

        .links a:hover {
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
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Header with WHITE background -->
            <div class="dost-header">
                <img src="{{ asset('images/dost-logo.png') }}" alt="DOST Logo" class="dost-logo"
                    onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/6/6e/Department_of_Science_and_Technology_%28DOST%29.svg/1200px-Department_of_Science_and_Technology_%28DOST%29.svg.png'">
                <p class="dost-subtitle">KNOWLEDGE MANAGEMENT PORTAL</p>
            </div>

            <!-- Form area with NEW color #1d799d -->
            <div class="form-area">
                <h3>LOGIN</h3>
                <p class="subtitle">Internal Access for DOST Personnel</p>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">DOST EMAIL</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="email" id="emailInput" class="form-control"
                                placeholder="name@dost.gov.ph">
                        </div>
                        <div class="invalid-feedback" id="emailFeedback">
                            <i class="fas fa-exclamation-circle"></i> Must be a valid @dost.gov.ph email address
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">PASSWORD</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                            <input type="password" name="password" id="passwordInput" class="form-control"
                                placeholder="Enter password">
                        </div>
                        <div class="invalid-feedback" id="passwordFeedback">
                            <i class="fas fa-exclamation-circle"></i> Please enter your password
                        </div>
                    </div>

                    <button type="submit" id="loginBtn" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> LOGIN
                    </button>
                </form>

                <div class="links">
                    {{-- <a href="#">Forgot Password?</a>
                    <span class="mx-2">|</span> --}}
                    <a href="{{ route('register') }}">First-Time Setup</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const emailInput = document.getElementById('emailInput');
        const passwordInput = document.getElementById('passwordInput');
        const loginBtn = document.getElementById('loginBtn');
        const loginForm = document.getElementById('loginForm');
        const emailFeedback = document.getElementById('emailFeedback');
        const passwordFeedback = document.getElementById('passwordFeedback');

        let emailTouched = false;
        let passwordTouched = false;

        function validateEmail() {
            const email = emailInput.value.trim();
            const emailPattern = /@dost\.gov\.ph$/;

            if (!emailTouched) {
                emailInput.classList.remove('is-invalid');
                emailFeedback.classList.remove('show');
                return true;
            }

            if (email === '') {
                emailInput.classList.add('is-invalid');
                emailFeedback.classList.add('show');
                return false;
            } else if (!emailPattern.test(email)) {
                emailInput.classList.add('is-invalid');
                emailFeedback.classList.add('show');
                return false;
            } else {
                emailInput.classList.remove('is-invalid');
                emailFeedback.classList.remove('show');
                return true;
            }
        }

        function validatePassword() {
            const password = passwordInput.value.trim();

            if (!passwordTouched) {
                passwordInput.classList.remove('is-invalid');
                passwordFeedback.classList.remove('show');
                return true;
            }

            if (password === '') {
                passwordInput.classList.add('is-invalid');
                passwordFeedback.classList.add('show');
                return false;
            } else {
                passwordInput.classList.remove('is-invalid');
                passwordFeedback.classList.remove('show');
                return true;
            }
        }

        function updateButtonState() {
            const isEmailValid = emailTouched ? validateEmail() : true;
            const isPasswordValid = passwordTouched ? validatePassword() : true;

            if (isEmailValid && isPasswordValid) {
                loginBtn.disabled = false;
            } else {
                loginBtn.disabled = true;
            }
        }

        emailInput.addEventListener('input', function() {
            emailTouched = true;
            validateEmail();
            updateButtonState();
        });

        emailInput.addEventListener('blur', function() {
            emailTouched = true;
            validateEmail();
            updateButtonState();
        });

        passwordInput.addEventListener('input', function() {
            passwordTouched = true;
            validatePassword();
            updateButtonState();
        });

        passwordInput.addEventListener('blur', function() {
            passwordTouched = true;
            validatePassword();
            updateButtonState();
        });

        loginForm.addEventListener('submit', function(e) {
            emailTouched = true;
            passwordTouched = true;

            const isEmailValid = validateEmail();
            const isPasswordValid = validatePassword();

            if (!isEmailValid || !isPasswordValid) {
                e.preventDefault();
                loginBtn.disabled = true;
                return false;
            }

            loginBtn.disabled = true;
            loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> LOGGING IN...';
            return true;
        });

        loginBtn.disabled = true;
    </script>
</body>

</html>
