<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $image = '';

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/blogs/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $filename = basename($_FILES['image']['name']);
        $targetFile = $uploadDir . time() . '_' . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $image = 'uploads/blogs/' . basename($targetFile);
        } else {
            $error = "Failed to upload image.";
        }
    }

    if (empty($title) || empty($content)) {
        $error = "Title and Content are required.";
    }

    if (!$error) {
        $stmt = $conn->prepare("INSERT INTO blogs (title, content, image) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $content, $image);
        if ($stmt->execute()) {
            $success = "Blog added successfully!";
            $title = $content = '';
        } else {
            $error = "Database error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add New Blog</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 40px 20px;
            color: #34495e;
        }

        h1 {
            text-align: center;
            font-weight: 600;
            font-size: 2.4rem;
            margin-bottom: 20px;
        }

        p a {
            display: block;
            text-align: center;
            margin-bottom: 30px;
            color: #3498db;
            font-weight: 600;
            text-decoration: none;
        }

        p a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 700px;
            background: white;
            padding: 30px;
            margin: 0 auto;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        form label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
        }

        input[type="text"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            margin-bottom: 20px;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
        }

        button {
            padding: 12px 28px;
            font-size: 1rem;
            background-color: #2ecc71;
            border: none;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #27ae60;
        }

        .message {
            max-width: 700px;
            margin: 0 auto 20px;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 1rem;
            text-align: center;
        }

        .message.success {
            background-color: #eafaf1;
            color: #2ecc71;
            border: 1px solid #2ecc71;
        }

        .message.error {
            background-color: #fdecea;
            color: #e74c3c;
            border: 1px solid #e74c3c;
        }
    </style>
</head>
<body>

    <h1>Add New Blog</h1>
    <p><a href="manage-blogs.php">← Back to Blogs</a></p>

    <?php if ($error): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="message success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="container">
        <form action="" method="POST" enctype="multipart/form-data">
            <label>Title:</label>
            <input type="text" name="title" value="<?= isset($title) ? htmlspecialchars($title) : '' ?>" required>

            <label>Content:</label>
            <textarea name="content" rows="8" required><?= isset($content) ? htmlspecialchars($content) : '' ?></textarea>

            <label>Blog Image:</label>
            <input type="file" name="image" accept="image/*">

            <button type="submit">Add Blog</button>
        </form>
    </div>
</body>
</html>
