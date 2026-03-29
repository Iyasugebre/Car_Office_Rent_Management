<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Car & Office Rent Management – ERP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
</head>
<body class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <div style="background: var(--auth-primary); width: 60px; height: 60px; border-radius: 1.5rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.4);">
                <i data-feather="lock" style="color: white; width: 30px; height: 30px;"></i>
            </div>
            <h1>Welcome Back</h1>
            <p>Enter your credentials to access the admin panel.</p>
        </div>

        <form action="{{ url('/login') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="auth-input" placeholder="admin@example.com" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="auth-input" placeholder="••••••••" required>
                @error('password')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: #94a3b8; cursor: pointer;">
                    <input type="checkbox" name="remember" style="accent-color: var(--auth-primary);">
                    Remember me
                </label>
                <a href="#" style="font-size: 0.8125rem; color: var(--auth-primary); text-decoration: none; font-weight: 600;">Forgot password?</a>
            </div>

            <button type="submit" class="btn-auth">
                Sign In
            </button>
        </form>

        <div style="margin-top: 2rem; text-align: center; font-size: 0.8125rem; color: #94a3b8;">
            Protected area. Authorized personnel only.
        </div>
    </div>

    <script>
        feather.replace();
    </script>
</body>
</html>
