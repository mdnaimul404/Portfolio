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
    $description = trim($_POST['description']);
    $project_link = trim($_POST['project_link']);
    
    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/projects/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $filename = basename($_FILES['image']['name']);
        $imageFileType = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($imageFileType, $allowedTypes)) {
            $error = "Only JPG, JPEG, PNG & GIF files are allowed.";
        } else {
            $targetFile = $uploadDir . time() . '_' . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                // Save relative path for DB
                $image = 'uploads/projects/' . basename($targetFile);
            } else {
                $error = "Failed to upload image.";
            }
        }
    }

    if (empty($title) || empty($description)) {
        $error = "Title and Description are required.";
    }

    if (!$error) {
        $stmt = $conn->prepare("INSERT INTO projects (title, description, image, project_link) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $description, $image, $project_link);
        if ($stmt->execute()) {
            $success = "Project added successfully!";
            // Clear form fields
            $title = $description = $project_link = '';
        } else {
            $error = "Database error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Project</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Add New Project</h1>
    <p><a href="manage-projects.php">← Back to Projects</a></p>

    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color:green;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <label>Title:<br>
            <input type="text" name="title" value="<?= isset($title) ? htmlspecialchars($title) : '' ?>" required>
        </label><br><br>

        <label>Description:<br>
            <textarea name="description" rows="6" required><?= isset($description) ? htmlspecialchars($description) : '' ?></textarea>
        </label><br><br>

        <label>Project Link:<br>
            <input type="url" name="project_link" value="<?= isset($project_link) ? htmlspecialchars($project_link) : '' ?>">
        </label><br><br>

        <label>Project Image:<br>
            <input type="file" name="image" accept="image/*">
        </label><br><br>

        <button type="submit">Add Project</button>
    </form>
</body>
</html>
