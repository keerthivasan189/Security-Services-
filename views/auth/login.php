<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — Sai Saktheeswari HRMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
body { background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%); min-height: 100vh;
  display: flex; align-items: center; justify-content: center; }
.login-card { background: #fff; border-radius: 16px; padding: 40px 36px;
  width: 100%; max-width: 400px; box-shadow: 0 20px 60px rgba(0,0,0,.15); }
.login-card .brand { text-align: center; margin-bottom: 28px; }
.login-card .brand h4 { color: #6c5ce7; font-weight: 700; margin: 0; }
.login-card .brand small { color: #888; font-size: 12px; }
.form-control:focus { border-color: #6c5ce7; box-shadow: 0 0 0 .2rem rgba(108,92,231,.2); }
.btn-login { background: #6c5ce7; color: #fff; width: 100%; padding: 10px;
  border-radius: 8px; font-weight: 600; border: none; }
.btn-login:hover { background: #5a4dd1; color: #fff; }
</style>
</head>
<body>
<div class="login-card">
  <div class="brand">
    <i class="bi bi-shield-check" style="font-size:40px;color:#6c5ce7"></i>
    <h4 class="mt-2">SAI SAKTHEESWARI</h4>
    <small>Security Services — HRMS</small>
  </div>
  <?php if ($error): ?>
  <div class="alert alert-danger py-2 small"><i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="POST">
    <div class="mb-3">
      <label class="form-label fw-semibold small">Username</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-person"></i></span>
        <input type="text" name="username" class="form-control" placeholder="Enter username" required autofocus>
      </div>
    </div>
    <div class="mb-4">
      <label class="form-label fw-semibold small">Password</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-lock"></i></span>
        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
      </div>
    </div>
    <button type="submit" class="btn btn-login">
      <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
    </button>
  </form>
  <p class="text-center text-muted mt-3 mb-0" style="font-size:11px">Default: admin / password</p>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
