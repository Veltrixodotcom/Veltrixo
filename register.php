<?php
session_start();
require __DIR__ . "/config/database.php";


if (isset($_SESSION['freelancer_id'])) {
    header("Location: freelancer/dashboard.php");
    exit;
}

$errors = [];
$old = [
    'full_name' => '',
    'email' => '',
    'phone' => '',
    'location' => '',
    'category' => '',
    'experience_years' => '',
    'portfolio_url' => '',
    'linkedin_url' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $key => $_) {
        $old[$key] = trim($_POST[$key] ?? '');
    }
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($old['full_name'] === '') $errors[] = "Full name is required.";
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "Enter a valid email address.";
    if ($old['phone'] === '') $errors[] = "Phone number is required.";
    if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters.";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match.";

    if ($old['portfolio_url'] !== '' && !filter_var($old['portfolio_url'], FILTER_VALIDATE_URL)) {
        $errors[] = "Portfolio URL is not valid.";
    }
    if ($old['linkedin_url'] !== '' && !filter_var($old['linkedin_url'], FILTER_VALIDATE_URL)) {
        $errors[] = "LinkedIn URL is not valid.";
    }

    if (!$errors) {
        $check = $pdo->prepare("SELECT id FROM freelancers WHERE email = ?");
        $check->execute([$old['email']]);

        if ($check->fetch()) {
            $errors[] = "An account with this email already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                INSERT INTO freelancers
                (full_name, email, phone, password_hash, location, category, experience_years, portfolio_url, linkedin_url)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $old['full_name'],
                $old['email'],
                $old['phone'],
                $hash,
                $old['location'] ?: null,
                $old['category'] ?: null,
                $old['experience_years'] !== '' ? (float)$old['experience_years'] : 0,
                $old['portfolio_url'] ?: null,
                $old['linkedin_url'] ?: null
            ]);

           $_SESSION['freelancer_id'] = (int)$pdo->lastInsertId();
	session_regenerate_id(true);
	header("Location: freelancer/dashboard.php");
	exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register | VELTRIXO</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">
<div class="container py-5">
    <div class="auth-card mx-auto">
        <div class="text-center mb-4">
            <div class="brand">VELTRIXO</div>
            <p class="text-muted mb-0">Freelancer Registration</p>
        </div>

        <?php if ($errors): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" novalidate>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name *</label>
                    <input name="full_name" class="form-control" required value="<?= htmlspecialchars($old['full_name']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($old['email']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone *</label>
                    <input name="phone" class="form-control" required value="<?= htmlspecialchars($old['phone']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Location</label>
                    <input name="location" class="form-control" value="<?= htmlspecialchars($old['location']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Professional Category</label>
                    <select name="category" class="form-select">
                        <option value="">Select category</option>
                        <?php
                        $categories = ["Digital Marketing","SEO","Social Media Marketing","Graphic Design","Video Editing","Web Development","Content Writing","UI/UX Design","Other"];
                        foreach ($categories as $cat):
                        ?>
                            <option <?= $old['category'] === $cat ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Experience (Years)</label>
                    <input type="number" min="0" step="0.5" name="experience_years" class="form-control" value="<?= htmlspecialchars($old['experience_years']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password *</label>
                    <input type="password" name="password" class="form-control" minlength="8" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirm Password *</label>
                    <input type="password" name="confirm_password" class="form-control" minlength="8" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Portfolio URL</label>
                    <input type="url" name="portfolio_url" class="form-control" placeholder="https://..." value="<?= htmlspecialchars($old['portfolio_url']) ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">LinkedIn URL</label>
                    <input type="url" name="linkedin_url" class="form-control" placeholder="https://linkedin.com/in/..." value="<?= htmlspecialchars($old['linkedin_url']) ?>">
                </div>
            </div>

            <button class="btn btn-brand w-100 mt-4">Create Freelancer Account</button>
        </form>

        <p class="text-center mt-4 mb-0">
            Already registered?
            <a href="login.php">Login</a>
        </p>
    </div>
</div>
</body>
</html>
