<?php
// admin_dashboard.php
session_start();
require_once('../config/database.php.php');

// Handle Status Change Requests
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];

    $allowedStatuses = [
        'approve' => 'approved',
        'reject'  => 'rejected',
        'pending' => 'pending'
    ];

    if (array_key_exists($action, $allowedStatuses)) {
        $stmt = $pdo->prepare("UPDATE freelancers SET status = :status WHERE id = :id");
        $stmt->execute([
            'status' => $allowedStatuses[$action],
            'id'     => $id
        ]);
        header("Location: admin_dashboard.php?msg=StatusUpdated");
        exit;
    }
}

// Fetch All Freelancers
$stmt = $pdo->query("SELECT id, name, email, status FROM freelancers ORDER BY id DESC");
$freelancers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>VELTRIXO Admin - Freelancer Approvals</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #f8f9fa; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; background: #fff; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #007bff; color: #fff; }
        .badge { padding: 5px 10px; border-radius: 4px; font-weight: bold; color: #fff; }
        .badge-pending { background-color: #ffc107; color: #212529; }
        .badge-approved { background-color: #28a745; }
        .badge-rejected { background-color: #dc3545; }
        .btn { padding: 6px 12px; text-decoration: none; color: #fff; border-radius: 4px; font-size: 13px; margin-right: 5px; }
        .btn-approve { background-color: #28a745; }
        .btn-reject { background-color: #dc3545; }
        .btn-pending { background-color: #6c757d; }
    </style>
</head>
<body>

    <h1>VELTRIXO Admin Panel</h1>
    <h3>Manage Freelancer Statuses</h3>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($freelancers as $row): ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= htmlspecialchars($row['name']); ?></td>
                <td><?= htmlspecialchars($row['email']); ?></td>
                <td>
                    <span class="badge badge-<?= $row['status']; ?>">
                        <?= ucfirst($row['status']); ?>
                    </span>
                </td>
                <td>
                    <?php if ($row['status'] !== 'approved'): ?>
                        <a href="?action=approve&id=<?= $row['id']; ?>" class="btn btn-approve">Approve</a>
                    <?php endif; ?>

                    <?php if ($row['status'] !== 'rejected'): ?>
                        <a href="?action=reject&id=<?= $row['id']; ?>" class="btn btn-reject">Reject</a>
                    <?php endif; ?>

                    <?php if ($row['status'] !== 'pending'): ?>
                        <a href="?action=pending&id=<?= $row['id']; ?>" class="btn btn-pending">Set Pending</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>