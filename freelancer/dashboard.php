<?php
session_start();
require __DIR__ . "/../config/database.php";

if (!isset($_SESSION['freelancer_id'])) {
    header("Location: ../login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT id, full_name, email, phone, location, category, skills, experience_years, portfolio_url, linkedin_url, status FROM freelancers WHERE id = ?");
$stmt->execute([$_SESSION['freelancer_id']]);
$freelancer = $stmt->fetch();

if (!$freelancer) {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM applications WHERE freelancer_id = ?");
$countStmt->execute([$freelancer['id']]);
$applications = (int)$countStmt->fetchColumn();

$projectCount = (int)$pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'open'")->fetchColumn();

$activeStmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM applications a
    JOIN projects p ON p.id = a.project_id
    WHERE a.freelancer_id = ? AND a.status = 'accepted' AND p.status = 'assigned'
");
$activeStmt->execute([$freelancer['id']]);
$activeProjects = (int)$activeStmt->fetchColumn();

$recentProjects = $pdo->query("
    SELECT id, title, category, budget, created_at
    FROM projects
    WHERE status = 'open'
    ORDER BY created_at DESC
    LIMIT 5
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard | VELTRIXO</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../css/style.css">
</head>
<body class="dashboard-page">

<nav class="navbar navbar-expand-lg bg-dark navbar-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">VELTRIXO</a>
        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="text-white small">Hi, <?= htmlspecialchars($freelancer['full_name']) ?></span>
            <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="mb-4">
        <h1 class="fw-bold">Freelancer Dashboard</h1>
        <p class="text-muted">Manage your VELTRIXO freelancer profile and projects.</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <span>Open Projects</span>
                <strong><?= $projectCount ?></strong>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <span>My Applications</span>
                <strong><?= $applications ?></strong>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <span>Active Projects</span>
                <strong><?= $activeProjects ?></strong>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="dashboard-card">
                <h4>My Profile</h4>
                <hr>
                <p><strong>Name:</strong> <?= htmlspecialchars($freelancer['full_name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($freelancer['email']) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($freelancer['phone']) ?></p>
                <p><strong>Category:</strong> <?= htmlspecialchars($freelancer['category'] ?: 'Not set') ?></p>
                <p><strong>Experience:</strong> <?= htmlspecialchars($freelancer['experience_years']) ?> years</p>
                <p><strong>Location:</strong> <?= htmlspecialchars($freelancer['location'] ?: 'Not set') ?></p>
                <span class="badge bg-success"><?= htmlspecialchars(ucfirst($freelancer['status'])) ?></span>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Latest Projects</h4>
                    <span class="text-muted small">Available opportunities</span>
                </div>

                <?php if (!$recentProjects): ?>
                    <p class="text-muted">No projects are available right now.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Project</th>
                                    <th>Category</th>
                                    <th>Budget</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($recentProjects as $project): ?>
                                <tr>
                                    <td><?= htmlspecialchars($project['title']) ?></td>
                                    <td><?= htmlspecialchars($project['category']) ?></td>
                                    <td>₹<?= number_format((float)$project['budget'], 2) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-brand" disabled>Apply Soon</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
