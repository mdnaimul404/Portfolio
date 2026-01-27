<?php
session_start();
include '../includes/db.php';  // Adjust path if your db.php is elsewhere

// Define target directory for uploads
$target_dir = __DIR__ . '/../assets/uploads/';

// Create folder if not exists, with permissions 0777 recursively
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$message = '';

// Handle Image Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_image'])) {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = basename($_FILES['image']['name']);
        $fileSize = $_FILES['image']['size'];
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Reject HEIC format explicitly
        if ($fileType === 'heic') {
            $message = "HEIC format is not supported. Please upload JPG, PNG, WEBP, or GIF.";
        } elseif ($fileSize > 10 * 1024 * 1024) {
            $message = "File size exceeds 5MB limit.";
        } else {
            // Sanitize filename
            $safeFileName = preg_replace("/[^a-zA-Z0-9\.\-_]/", "_", $fileName);
            $target_file = $target_dir . $safeFileName;

            // Avoid overwriting existing files by adding a suffix if file exists
            $fileBase = pathinfo($safeFileName, PATHINFO_FILENAME);
            $fileExt = pathinfo($safeFileName, PATHINFO_EXTENSION);
            $counter = 1;
            while (file_exists($target_file)) {
                $safeFileName = $fileBase . '_' . $counter . '.' . $fileExt;
                $target_file = $target_dir . $safeFileName;
                $counter++;
            }

            // Move uploaded file
            if (move_uploaded_file($fileTmpPath, $target_file)) {
                $relative_path = "assets/uploads/" . $safeFileName;

                // Save to database
                $stmt = $conn->prepare("INSERT INTO home_slider_images (image_path) VALUES (?)");
                $stmt->bind_param("s", $relative_path);

                if ($stmt->execute()) {
                    $message = "Image uploaded and saved successfully!";
                } else {
                    $message = "Database error: Failed to save image path.";
                    // Delete file if DB insert fails
                    unlink($target_file);
                }
            } else {
                $message = "Failed to move uploaded file.";
            }
        }
    } else {
        $message = "No file uploaded or upload error.";
    }
}

// Handle Image Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_image'])) {
    $idToDelete = intval($_POST['delete_image']);

    // Get image path from DB
    $stmt = $conn->prepare("SELECT image_path FROM home_slider_images WHERE id = ?");
    $stmt->bind_param("i", $idToDelete);
    $stmt->execute();
    $stmt->bind_result($imgPathToDelete);
    $stmt->fetch();
    $stmt->close();

    if ($imgPathToDelete) {
        $fullPath = __DIR__ . '/../' . $imgPathToDelete;

        // Delete file if exists
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        // Delete DB record
        $stmt = $conn->prepare("DELETE FROM home_slider_images WHERE id = ?");
        $stmt->bind_param("i", $idToDelete);
        if ($stmt->execute()) {
            $message = "Image deleted successfully.";
        } else {
            $message = "Failed to delete image from database.";
        }
        $stmt->close();
    } else {
        $message = "Image not found in database.";
    }
}

// Fetch all images to display
$images = [];
$result = $conn->query("SELECT id, image_path FROM home_slider_images ORDER BY uploaded_at DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin - Upload and Manage Slider Images</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap');

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 0 30px 60px;
            background: #f5f7fa;
            color: #2c3e50;
        }

        h1 {
            margin-bottom: 25px;
            font-weight: 600;
            font-size: 2.4rem;
            color: #34495e;
            text-align: center;
        }

        form {
            background: white;
            padding: 25px 30px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
            margin-bottom: 40px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        label {
            font-weight: 600;
            display: block;
            margin-bottom: 10px;
            color: #34495e;
        }

        input[type="file"] {
            width: 100%;
            padding: 8px 10px;
            border: 1.8px solid #bdc3c7;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input[type="file"]:focus {
            border-color: #2980b9;
            outline: none;
        }

        button {
            display: block;
            width: 100%;
            margin-top: 20px;
            background-color: #2980b9;
            color: white;
            border: none;
            padding: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 5px 12px rgba(41, 128, 185, 0.5);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        button:hover {
            background-color: #3498db;
            box-shadow: 0 8px 16px rgba(52, 152, 219, 0.6);
        }

        .message {
            max-width: 500px;
            margin: 0 auto 30px;
            padding: 15px 20px;
            background-color: #dff0d8;
            border: 1.5px solid #a3c293;
            color: #3c763d;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            box-shadow: 0 3px 6px rgba(0,0,0,0.07);
        }

        h2 {
            font-weight: 600;
            color: #34495e;
            text-align: center;
            margin-bottom: 30px;
            font-size: 1.9rem;
        }

        .image-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 24px;
            padding: 0 20px;
        }

        .image-item {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            padding: 12px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
        }

        .image-item:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }

        .image-item img {
            max-width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 12px;
            user-select: none;
        }

        .image-item form {
            margin: 0;
        }

        .image-item button {
            background-color: #e74c3c;
            padding: 8px 16px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(231, 76, 60, 0.5);
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            user-select: none;
        }

        .image-item button:hover {
            background-color: #c0392b;
            box-shadow: 0 7px 18px rgba(192, 57, 43, 0.7);
        }

        a.back-link {
            display: block;
            max-width: 500px;
            margin: 40px auto 0;
            padding: 12px 0;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            color: #2980b9;
            border-radius: 8px;
            border: 2px solid #2980b9;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        a.back-link:hover {
            background-color: #2980b9;
            color: white;
        }

        @media (max-width: 480px) {
            body {
                padding: 0 15px 50px;
            }
            form, .message, a.back-link {
                max-width: 100%;
                padding-left: 15px;
                padding-right: 15px;
            }
            .image-list {
                grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
                gap: 18px;
                padding: 0 10px;
            }
            .image-item img {
                height: 100px;
            }
        }
    </style>
</head>
<body>
    <h1>Upload Image for Home Slider</h1>

    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <label for="image">Select Image (max 10MB):</label><br>
        <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/webp,image/gif" required><br>
        <button type="submit" name="upload_image">Upload Image</button>
    </form>

    <h2>Existing Images</h2>

    <?php if (empty($images)): ?>
        <p style="text-align:center; color:#666;">No images uploaded yet.</p>
    <?php else: ?>
        <div class="image-list">
            <?php foreach ($images as $img): ?>
                <div class="image-item">
                    <img src="<?php echo htmlspecialchars('../' . $img['image_path']); ?>" alt="Slider Image">
                    <form action="" method="POST" onsubmit="return confirm('Are you sure you want to delete this image?');">
                        <input type="hidden" name="delete_image" value="<?php echo $img['id']; ?>">
                        <button type="submit">Delete</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <a href="dashboard.php" class="back-link">&larr; Back to Dashboard</a>
    <a href="../index.php" class="back-link">&larr; Back to Home</a>
</body>
</html>
