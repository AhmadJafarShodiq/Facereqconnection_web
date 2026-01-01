<!DOCTYPE html>
<html>
<head>
    <title>Login Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height:100vh">

<div class="card shadow" style="width:380px">
    <div class="card-body">
        <h4 class="text-center mb-4">Admin Login</h4>

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf

            <div class="mb-3">
                <label>Username</label>
                <input name="username" class="form-control" required autofocus>
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            @error('username')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror

            <button class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</div>

</body>
</html>
