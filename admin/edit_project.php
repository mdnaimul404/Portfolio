<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: manage-projects.php");
    exit();
}

$id = intval($_GET['id']);
$error = '';
$success = '';

// Fetch existing project
$stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: manage-projects.php");
    exit();
}

$project = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $project_link = trim($_POST['project_link']);
    $image = $project['image']; // Keep existing image unless replaced

    // Handle image upload if a new image was provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/projects/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $filename = basename($_FILES['image']['name']);
        $targetFile = $uploadDir . time() . '_' . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $image = 'uploads/projects/' . basename($targetFile);
        } else {
            $error = "Failed to upload image.";
        }
    }

    if (empty($title) || empty($description)) {
        $error = "Title and Description are required.";
    }

    if (!$error) {
        $stmt = $conn->prepare("UPDATE projects SET title = ?, description = ?, image = ?, project_link = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $title, $description, $image, $project_link, $id);
        if ($stmt->execute()) {
            $success = "Project updated successfully!";
            // Refresh project data
            $project['title'] = $title;
            $project['description'] = $description;
            $project['image'] = $image;
            $project['project_link'] = $project_link;
        } else {
            $error = "Database error: " . $conn->error;
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Project</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Edit Project</h1>
    <p><a href="manage-projects.php">← Back to Projects</a></p>

    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color:green;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <label>Title:<br>
            <input type="text" name="title" value="<?= htmlspecialchars($project['title']) ?>" required>
        </label><br><br>

        <label>Description:<br>
            <textarea name="description" rows="6" required><?= htmlspecialchars($project['description']) ?></textarea>
        </label><br><br>

        <label>Project Link:<br>
            <input type="url" name="project_link" value="<?= htmlspecialchars($project['project_link']) ?>">
        </label><br><br>

        <label>Current Image:<br>
            <?php if ($project['image']): ?>
                <img src="../<?= htmlspecialchars($project['image']) ?>" alt="Project Image" style="max-width:200px;"><br><br>
            <?php else: ?>
                No image uploaded.
            <?php endif; ?>
        </label>

        <label>Replace Image:<br>
            <input type="file" name="image" accept="image/*">
        </label><br><br>

        <button type="submit">Update Project</button>
    </form>
</body>
</html>
