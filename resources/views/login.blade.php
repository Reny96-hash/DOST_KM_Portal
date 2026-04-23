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
            background: linear-gradient(135deg, #f0f2f5 0%, #e0e5ec 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            max-width: 400px;
            width: 100%;
            margin: 20px;
        }

        .login-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .dost-header {
            background-color: #000000;
            padding: 30px 20px;
            text-align: center;
        }

        .dost-logo {
            max-width: 100px;
            margin-bottom: 5px;
        }

        .dost-subtitle {
            font-family: 'Arial Black', sans-serif;
            color: #00AEEF;
            font-size: 0.9rem;
            letter-spacing: 1px;
            margin: 8px 0 0 0;
            text-transform: uppercase;
        }

        .form-area {
            padding: 30px 25px 25px 25px;
        }

        .form-area h3 {
            color: #000847;
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 5px;
            text-align: center;
        }

        .form-area .subtitle {
            text-align: center;
            color: #666;
            font-size: 0.7rem;
            margin-bottom: 25px;
        }

        .form-label {
            font-size: 0.7rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }

        .input-group-text {
            background-color: white;
            border-right: none;
        }

        .form-control {
            border-left: none;
            font-size: 0.8rem;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #dee2e6;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
            border-left: none;
        }

        .invalid-feedback {
            font-size: 0.65rem;
            color: #dc3545;
            display: none;
            margin-top: 5px;
        }

        .invalid-feedback.show {
            display: block;
        }

        .btn-login {
            background-color: #000847;
            color: white;
            padding: 10px;
            font-size: 0.8rem;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            width: 100%;
            margin-top: 10px;
        }

        .btn-login:hover {
            background-color: #001c5a;
        }

        .btn-login:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .links {
            text-align: center;
            margin-top: 15px;
            font-size: 0.65rem;
        }

        .links a {
            color: #000847;
            text-decoration: none;
        }

        .links a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 8px 12px;
            font-size: 0.7rem;
            margin-bottom: 15px;
            border-radius: 6px;
        }

        hr {
            margin: 15px 0;
            border-color: #eee;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="dost-header">
                <img src="{{ asset('images/dost-logo.png') }}" alt="DOST Logo" class="dost-logo"
                    onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/6/6e/Department_of_Science_and_Technology_%28DOST%29.svg/1200px-Department_of_Science_and_Technology_%28DOST%29.svg.png'">
                <p class="dost-subtitle">KNOWLEDGE MANAGEMENT PORTAL</p>
            </div>

            <div class="form-area">
                <h3>LOGIN</h3>
                <p class="subtitle">Internal Access for DOST Personnel</p>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ url('/login') }}" id="loginForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">DOST EMAIL</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"
                                    style="color: #000847; font-size: 12px;"></i></span>
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
                            <span class="input-group-text"><i class="fas fa-key"
                                    style="color: #000847; font-size: 12px;"></i></span>
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
                    <a href="#">Forgot Password?</a>
                    <span class="mx-2">|</span>
                    <a href="#">First-Time Setup</a>
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

        // Track if user has interacted with fields
        let emailTouched = false;
        let passwordTouched = false;

        function validateEmail() {
            const email = emailInput.value.trim();
            const emailPattern = /@dost\.gov\.ph$/;

            if (!emailTouched) {
                // No error shown if user hasn't typed yet
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
                // No error shown if user hasn't typed yet
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

        // When user starts typing in email field
        emailInput.addEventListener('input', function() {
            emailTouched = true;
            validateEmail();
            updateButtonState();
        });

        // When user leaves email field
        emailInput.addEventListener('blur', function() {
            emailTouched = true;
            validateEmail();
            updateButtonState();
        });

        // When user starts typing in password field
        passwordInput.addEventListener('input', function() {
            passwordTouched = true;
            validatePassword();
            updateButtonState();
        });

        // When user leaves password field
        passwordInput.addEventListener('blur', function() {
            passwordTouched = true;
            validatePassword();
            updateButtonState();
        });

        // Form submit validation
        loginForm.addEventListener('submit', function(e) {
            // Mark both as touched
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

        // Initially button is disabled (no input yet)
        loginBtn.disabled = true;
    </script>
</body>

</html>
