<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: manage-blogs.php");
    exit();
}

$id = intval($_GET['id']);
$error = '';
$success = '';

// Fetch existing blog
$stmt = $conn->prepare("SELECT * FROM blogs WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: manage-blogs.php");
    exit();
}

$blog = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $image = $blog['image']; // Keep old image unless replaced

    // Handle image upload if new image is provided
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
        $stmt = $conn->prepare("UPDATE blogs SET title = ?, content = ?, image = ? WHERE id = ?");
        $stmt->bind_param("sssi", $title, $content, $image, $id);
        if ($stmt->execute()) {
            $success = "Blog updated successfully!";
            $blog['title'] = $title;
            $blog['content'] = $content;
            $blog['image'] = $image;
        } else {
            $error = "Database error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Blog</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Edit Blog</h1>
    <p><a href="manage-blogs.php">← Back to Blogs</a></p>

    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color:green;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <label>Title:<br>
            <input type="text" name="title" value="<?= htmlspecialchars($blog['title']) ?>" required>
        </label><br><br>

        <label>Content:<br>
            <textarea name="content" rows="8" required><?= htmlspecialchars($blog['content']) ?></textarea>
        </label><br><br>

        <label>Current Image:<br>
            <?php if ($blog['image']): ?>
                <img src="../<?= htmlspecialchars($blog['image']) ?>" alt="Blog Image" style="max-width:300px;"><br><br>
            <?php else: ?>
                No image uploaded.
            <?php endif; ?>
        </label>

        <label>Replace Image:<br>
            <input type="file" name="image" accept="image/*">
        </label><br><br>

        <button type="submit">Update Blog</button>
    </form>
</body>
</html>
