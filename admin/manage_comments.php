<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
include '../includes/db.php';

// Approve comment
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $stmt = $conn->prepare("UPDATE comments SET status = 'approved' WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_comments.php");
    exit();
}

// Delete comment
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_comments.php");
    exit();
}

$result = $conn->query("SELECT * FROM comments ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Comments</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            margin: 0;
            padding: 30px;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom right, #f2f7ff, #e0ebf8);
            color: #333;
        }

        h1 {
            text-align: center;
            font-size: 32px;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        a {
            color: #3498db;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
        }

        thead {
            background: #f5f9ff;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }

        th {
            font-weight: 600;
            color: #34495e;
        }

        tr:hover {
            background-color: #f0f7ff;
            transition: background 0.3s ease;
        }

        .actions a {
            padding: 6px 12px;
            margin-right: 5px;
            font-size: 14px;
            border-radius: 6px;
            transition: 0.2s ease;
        }

        .actions a:hover {
            transform: scale(1.05);
        }

        .approve {
            background-color: #2ecc71;
            color: white;
        }

        .delete {
            background-color: #e74c3c;
            color: white;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #2980b9;
        }

        .status {
            font-weight: bold;
            text-transform: capitalize;
        }

        .status.approved {
            color: #2ecc71;
        }

        .status.pending {
            color: #f39c12;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Manage Comments</h1>
    <p><a href="dashboard.php" class="back-link">← Back to Dashboard</a></p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Blog ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Comment</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['blog_id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['comment'])) ?></td>
                <td class="status <?= $row['status'] ?>"><?= $row['status'] ?></td>
                <td><?= $row['created_at'] ?></td>
                <td class="actions">
                    <?php if ($row['status'] === 'pending'): ?>
                        <a href="?approve=<?= $row['id'] ?>" class="approve">Approve</a>
                    <?php endif; ?>
                    <a href="?delete=<?= $row['id'] ?>" class="delete" onclick="return confirm('Are you sure you want to delete this comment?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
