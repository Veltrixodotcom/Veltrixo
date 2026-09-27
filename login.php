<?php
session_start();
require __DIR__ . "/config/database.php";

if (isset($_SESSION['freelancer_id'])) {
    header("Location: freelancer/dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = "Enter your email and password.";
    } else {
        $stmt = $pdo->prepare("SELECT id, full_name, password_hash, status FROM freelancers WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['status'] !== 'approved') {
                $error = "Your freelancer account is currently " . htmlspecialchars($user['status']) . ".";
            } else {
                session_regenerate_id(true);
                $_SESSION['freelancer_id'] = (int)$user['id'];
                header("Location: freelancer/dashboard.php");
                exit;
            }
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Freelancer Login | VELTRIXO</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">
<div class="container py-5">
    <div class="auth-card login-card mx-auto">
        <div class="text-center mb-4">
            <div class="brand">VELTRIXO</div>
            <p class="text-muted mb-0">Freelancer Portal</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="post">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control mb-3" required>

            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>

            <button class="btn btn-brand w-100 mt-4">Login</button>
        </form>

        <div class="text-center mt-4">
            <a href="register.php">Create Freelancer Account</a>
        </div>
    </div>
</div>
</body>
</html>
