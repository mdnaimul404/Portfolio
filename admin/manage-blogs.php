<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM blogs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage-blogs.php");
    exit();
}

$result = $conn->query("SELECT * FROM blogs ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Blogs</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background: #f9fbff;
            margin: 0;
            padding: 40px 20px;
            color: #34495e;
        }

        h1 {
            text-align: center;
            font-weight: 600;
            font-size: 2.6rem;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        p {
            max-width: 900px;
            margin: 0 auto 30px;
            font-size: 1rem;
            text-align: center;
        }

        p a {
            color: #3498db;
            text-decoration: none;
            font-weight: 600;
            margin: 0 12px;
            transition: color 0.3s ease;
        }

        p a:hover {
            color: #2980b9;
            text-decoration: underline;
        }

        table {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            border-collapse: separate;
            border-spacing: 0 12px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        thead {
            background: #2980b9;
            color: white;
        }

        thead th {
            padding: 16px 20px;
            font-weight: 600;
            text-align: left;
            font-size: 1rem;
            user-select: none;
        }

        tbody tr {
            background: #fff;
            box-shadow: 0 2px 10px rgba(41, 128, 185, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 8px;
            cursor: default;
        }

        tbody tr:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 30px rgba(41, 128, 185, 0.2);
        }

        tbody td {
            padding: 14px 20px;
            vertical-align: middle;
            font-size: 0.95rem;
            color: #34495e;
            border-bottom: none;
            max-width: 200px;
            overflow-wrap: break-word;
        }

        tbody td a {
            color: #2980b9;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        tbody td a:hover {
            color: #1c5980;
            text-decoration: underline;
        }

        /* Actions cell styling */
        tbody td:last-child {
            white-space: nowrap;
        }

        tbody td:last-child a {
            margin-right: 15px;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 0.9rem;
            box-shadow: 0 2px 10px rgba(41, 128, 185, 0.15);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        tbody td:last-child a:first-child {
            background-color: #27ae60;
            color: white;
        }

        tbody td:last-child a:first-child:hover {
            background-color: #219150;
        }

        tbody td:last-child a:last-child {
            background-color: #e74c3c;
            color: white;
        }

        tbody td:last-child a:last-child:hover {
            background-color: #c0392b;
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 20px 10px;
            }

            table, thead, tbody, th, td, tr {
                display: block;
            }

            thead tr {
                display: none;
            }

            tbody tr {
                margin-bottom: 20px;
                box-shadow: 0 4px 15px rgba(41, 128, 185, 0.12);
                border-radius: 10px;
                padding: 20px;
                background: white;
            }

            tbody td {
                padding: 10px 14px;
                text-align: right;
                font-size: 0.95rem;
                border-bottom: 1px solid #eee;
                position: relative;
            }

            tbody td:last-child {
                border-bottom: 0;
                text-align: center;
            }

            tbody td::before {
                content: attr(data-label);
                position: absolute;
                left: 15px;
                width: 50%;
                padding-left: 10px;
                font-weight: 600;
                color: #555;
                text-align: left;
                white-space: nowrap;
            }

            tbody td:last-child a {
                display: inline-block;
                margin: 5px 10px;
            }
        }
    </style>
</head>
<body>
    <h1>Manage Blogs</h1>
    <p>
        <a href="dashboard.php">← Back to Dashboard</a> |
        <a href="add_blog.php">+ Add New Blog</a>
    </p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Excerpt</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($blog = $result->fetch_assoc()): ?>
                <tr>
                    <td data-label="ID"><?= htmlspecialchars($blog['id']) ?></td>
                    <td data-label="Title"><?= htmlspecialchars($blog['title']) ?></td>
                    <td data-label="Excerpt"><?= htmlspecialchars(substr($blog['content'], 0, 100)) ?>...</td>
                    <td data-label="Actions">
                        <a href="edit_blog.php?id=<?= $blog['id'] ?>">Edit</a>
                        <a href="manage-blogs.php?delete=<?= $blog['id'] ?>" onclick="return confirm('Delete this blog?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
