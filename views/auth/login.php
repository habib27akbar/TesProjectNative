<?php
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../helpers/auth.php';
checkGuest();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = mysqli_prepare($koneksi, "SELECT id, nama, username, password FROM users WHERE username = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['username'] = $user['username'];
        header('Location: ./index.php?page=dashboard');
        exit;
    }

    $error = 'Username atau password salah.';
}
require_once __DIR__ . '/../../layouts/header.php';
?>
<div class="login-wrapper">
    <div class="card login-card shadow-lg">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:70px;height:70px;">
                    <i class="bi bi-shield-lock fs-2"></i>
                </div>
                <h3 class="fw-bold">Login Admin</h3>

            </div>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= e($error); ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control form-control-lg" required autofocus>
                </div>
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control form-control-lg" required>
                </div>
                <button class="btn btn-primary btn-lg w-100" type="submit">Login</button>
            </form>

        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>