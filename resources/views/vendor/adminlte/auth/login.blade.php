<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('img/partner-logo.png') }}" type="image/x-icon">
    <title>Partner | Login</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">

    <style>
        * {
            box-sizing: border-box;
        }

        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 25%, #1e3a8a 75%, #3b82f6 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(120, 219, 255, 0.2) 0%, transparent 50%);
            pointer-events: none;
        }

        body::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.02'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 
                0 20px 25px -5px rgba(0, 0, 0, 0.1), 
                0 10px 10px -5px rgba(0, 0, 0, 0.04),
                0 0 0 1px rgba(255, 255, 255, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 380px;
            animation: slideUp 0.5s ease-out;
            position: relative;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.8), transparent);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Logo Section */
        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-link {
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.5rem;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .logo-link:hover {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transform: translateY(-2px);
        }

        .logo-img {
            width: 45px;
            height: 45px;
            background: white;
            padding: 6px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .welcome-text {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.875rem;
            margin-top: 0.5rem;
            font-weight: 400;
        }

        /* Card Header */
        .card-header {
            background: linear-gradient(135deg, rgba(30, 58, 138, 0.9) 0%, rgba(59, 130, 246, 0.9) 100%);
            backdrop-filter: blur(10px);
            color: white;
            padding: 1.5rem;
            text-align: center;
            border-bottom: none;
            position: relative;
            overflow: hidden;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M20 20c0-5.5-4.5-10-10-10s-10 4.5-10 10 4.5 10 10 10 10-4.5 10-10zm10 0c0-5.5-4.5-10-10-10s-10 4.5-10 10 4.5 10 10 10 10-4.5 10-10z'/%3E%3C/g%3E%3C/svg%3E");
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .card-title {
            margin: 0;
            font-size: 1.125rem;
            font-weight: 600;
            position: relative;
            z-index: 1;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        /* Card Body */
        .card-body {
            padding: 2rem 1.5rem 1.5rem;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            color: #374151;
            font-weight: 500;
            margin-bottom: 0.375rem;
            font-size: 0.875rem;
            display: block;
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: stretch;
        }

        .form-control {
            border: 2px solid rgba(209, 213, 219, 0.6);
            border-radius: 12px;
            padding: 0.75rem 0.875rem;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            height: 42px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            flex: 1;
            border-right: none;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .form-control:focus {
            border-color: rgba(30, 58, 138, 0.8);
            box-shadow: 
                0 0 0 3px rgba(30, 58, 138, 0.1),
                0 1px 3px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.95);
            outline: none;
            z-index: 2;
            transform: translateY(-1px);
        }

        .form-control::placeholder {
            color: #9ca3af;
            font-size: 0.875rem;
        }

        .form-control.is-invalid {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .input-group-append {
            display: flex;
        }

        .input-group-text {
            background: rgba(248, 250, 252, 0.9);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(209, 213, 219, 0.6);
            color: #6b7280;
            border-radius: 12px;
            font-size: 0.875rem;
            padding: 0.75rem;
            border-left: none;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 42px;
            transition: all 0.3s ease;
        }

        .form-control:focus + .input-group-append .input-group-text {
            border-color: rgba(30, 58, 138, 0.8);
            background: rgba(248, 250, 252, 0.95);
            transform: translateY(-1px);
        }

        /* Password Toggle */
        .password-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 50px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            border: none;
            background: none;
            color: #6b7280;
            cursor: pointer;
            padding: 0;
            font-size: 0.875rem;
            transition: color 0.2s ease;
        }

        .toggle-password:hover {
            color: #1e3a8a;
        }

        /* Checkbox */
        .icheck-primary {
            margin: 0.75rem 0;
        }

        .icheck-primary input[type=checkbox]:checked + label::before {
            background-color: #1e3a8a;
            border-color: #1e3a8a;
        }

        .icheck-primary label {
            font-size: 0.875rem;
            color: #374151;
            font-weight: 400;
        }

        /* Button */
        .btn-primary {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #60a5fa 100%);
            background-size: 200% 200%;
            border: none;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            width: 100%;
            height: 42px;
            color: white;
            cursor: pointer;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            box-shadow: 
                0 4px 14px 0 rgba(30, 58, 138, 0.3),
                0 1px 3px 0 rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover {
            background-position: 200% 200%;
            transform: translateY(-2px);
            box-shadow: 
                0 6px 20px 0 rgba(30, 58, 138, 0.4),
                0 4px 6px 0 rgba(0, 0, 0, 0.1);
            color: white;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:active {
            transform: translateY(-1px);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Links */
        .forgot-password {
            text-align: center;
            margin-top: 1.5rem;
        }

        .forgot-password a {
            color: #1e3a8a;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            padding: 0.5rem;
            border-radius: 8px;
            display: inline-block;
        }

        .forgot-password a:hover {
            color: #3b82f6;
            background: rgba(30, 58, 138, 0.05);
            transform: translateY(-1px);
        }

        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(229, 231, 235, 0.8);
            font-size: 0.875rem;
        }

        .register-link a {
            color: #1e3a8a;
            font-weight: 600;
            text-decoration: none;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .register-link a:hover {
            color: #3b82f6;
            background: rgba(30, 58, 138, 0.05);
        }

        .text-muted {
            color: #6b7280;
        }

        /* Validation */
        .invalid-feedback {
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: block;
            font-weight: 500;
        }

        /* Loading Animation */
        .fa-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                padding: 0.75rem;
            }
            
            .login-card {
                max-width: 100%;
            }

            .card-header,
            .card-body {
                padding: 1.25rem;
            }

            .logo-section {
                margin-bottom: 1.5rem;
            }

            .logo-link {
                font-size: 1.375rem;
            }

            .logo-img {
                width: 40px;
                height: 40px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Logo Section -->
        <div style="position: absolute; top: 2rem; left: 50%; transform: translateX(-50%);">
            <div class="logo-section">
                <a href="{{ url('/') }}" class="logo-link">
                    <img src="{{ asset('img/partner-logo.png') }}" alt="Partner Logo" class="logo-img">
                    <b>Partner</b>
                </a>
                <p class="welcome-text">Welcome to your partner dashboard</p>
            </div>
        </div>

        <!-- Login Card -->
        <div class="login-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Sign In to Continue
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('auth.login-verify') }}" method="post" id="loginForm">
                    @csrf

                    <!-- Email Field -->
                    <div class="form-group">
                        <label for="email" class="form-label">EMAIL ADDRESS</label>
                        <div class="input-group">
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" 
                                   placeholder="Enter your email" 
                                   required 
                                   autofocus>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </div>
                            </div>
                        </div>
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <label for="password" class="form-label">PASSWORD</label>
                        <div class="password-wrapper">
                            <div class="input-group">
                                <input type="password" 
                                       name="password" 
                                       id="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Enter your password" 
                                       required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="toggle-password" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="form-group">
                        <div class="icheck-primary">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label for="remember">
                                Remember me for 30 days
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary" id="loginBtn">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Sign In
                        </button>
                    </div>

                    <!-- Forgot Password -->
                    @if(Route::has('password.request'))
                        <div class="forgot-password">
                            <a href="{{ route('password.request') }}">
                                <i class="fas fa-key mr-1"></i>
                                Forgot your password?
                            </a>
                        </div>
                    @endif

                    <!-- Register Link -->
                    @if(Route::has('register'))
                        <div class="register-link">
                            <span class="text-muted">Don't have an account?</span>
                            <a href="{{ route('register') }}">Create a new account</a>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        $(document).ready(function() {
            // Loading state for login button
            $('#loginForm').on('submit', function() {
                const submitBtn = $('#loginBtn');
                submitBtn.prop('disabled', true)
                        .html('<i class="fas fa-spinner fa-spin mr-2"></i>Signing In...');
            });

            // Focus on email field
            $('#email').focus();

            // Remove validation errors on input
            $('.form-control').on('input', function() {
                if ($(this).hasClass('is-invalid')) {
                    $(this).removeClass('is-invalid');
                    $(this).siblings('.invalid-feedback').fadeOut();
                }
            });

            // Enhanced form interactions
            $('.form-control').on('focus', function() {
                $(this).closest('.form-group').addClass('focused');
            }).on('blur', function() {
                if (!$(this).val()) {
                    $(this).closest('.form-group').removeClass('focused');
                }
            });
        });
    </script>
</body>
</html>