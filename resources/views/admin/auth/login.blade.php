<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Admin Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Source Sans 3', -apple-system, sans-serif;
            background-color: #f1f4f9;
            background-image: 
                radial-gradient(at 0% 0%, rgba(79, 70, 229, 0.1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, rgba(99, 102, 241, 0.1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, rgba(139, 92, 246, 0.1) 0, transparent 50%);
        }
        .login-card {
            background: #ffffff;
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 420px;
            padding: 50px;
            position: relative;
            z-index: 1;
        }
        .brand-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.3);
            color: white;
            font-size: 1.8rem;
        }
        .brand-name {
            font-weight: 800;
            color: #1e293b;
            letter-spacing: -0.5px;
            font-size: 1.5rem;
            text-align: center;
            margin-bottom: 8px;
        }
        .brand-sub {
            text-align: center;
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 40px;
        }
        .form-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            margin-bottom: 8px;
        }
        .input-group {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            overflow: hidden;
            transition: all 0.2s ease;
        }
        .input-group:focus-within {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            background: #ffffff;
        }
        .input-group-text {
            background: transparent;
            border: none;
            color: #94a3b8;
            padding-left: 16px;
        }
        .form-control {
            background: transparent;
            border: none;
            padding: 14px 16px 14px 8px;
            font-weight: 500;
            color: #334155;
        }
        .form-control:focus {
            background: transparent;
            box-shadow: none;
        }
        .btn-login {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white;
            border-radius: 14px;
            padding: 14px;
            font-weight: 700;
            border: none;
            width: 100%;
            margin-top: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.3);
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -10px rgba(79, 70, 229, 0.4);
            color: white;
        }
        .footer-text {
            text-align: center;
            margin-top: 40px;
            font-size: 0.8rem;
            color: #94a3b8;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand-icon">
        <i class="bi bi-fingerprint"></i>
    </div>
    <div class="brand-name">SMK 1 TAMANAN</div>
    <div class="brand-sub">Portal Administrasi Absensi</div>

    <form method="POST" action="{{ route('admin.login.post') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Username</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input name="username" class="form-control" placeholder="ID Admin" required autofocus>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
        </div>

        @error('username')
            <div class="alert alert-danger border-0 rounded-3 small py-2 mb-4 text-center">
                <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
            </div>
        @enderror

        <button type="submit" class="btn btn-login">
            Masuk Sekarang <i class="bi bi-arrow-right ms-2"></i>
        </button>
    </form>
    
    <div class="footer-text">
        © {{ date('Y') }} FaceReq Connection
    </div>
</div>

</body>
</html>
