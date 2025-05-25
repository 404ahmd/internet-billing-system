<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="mb-4 text-center">Login Admin</h4>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->has('errors'))
                        <div class="alert alert-danger">{{ $errors->first('login_error') }}</div>
                    @endif

                    <form method="POST" action="{{ url('/member/auth') }}">
                        @csrf
                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" placeholder="Username">
                        </div>
                        <div class="mb-3">
                        <button class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
                <a href="/forgot-password" class="text-center mb-2">Forgot Passwrod ??</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
