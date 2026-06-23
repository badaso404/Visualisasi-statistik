<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin &middot; Statistik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style> body { background:#1e293b; } </style>
</head>
<body class="d-flex align-items-center" style="min-height:100vh;">
<div class="container" style="max-width:400px;">
    <div class="card shadow border-0">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <i class="bi bi-bar-chart-fill fs-1 text-primary"></i>
                <h5 class="mt-2 mb-0">Panel Admin Statistik</h5>
                <small class="text-muted">Masuk untuk mengelola data</small>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('admin.login.attempt') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" name="remember" value="1" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">Ingat saya</label>
                </div>
                <button class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right"></i> Masuk</button>
            </form>
        </div>
    </div>
    <p class="text-center text-secondary small mt-3">&copy; {{ date('Y') }} Visualisasi Statistik</p>
</div>
</body>
</html>
