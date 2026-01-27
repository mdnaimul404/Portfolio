<?php
session_start();
include '../includes/db.php';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['resume'])) {
    $fileName = basename($_FILES['resume']['name']);
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $targetDir = '../uploads/resume/';
    $targetFile = $targetDir . uniqid() . '_' . $fileName;

    $allowedTypes = ['pdf', 'doc', 'docx'];
    $maxSize = 5 * 1024 * 1024; // 5MB max size

    if (!in_array($fileType, $allowedTypes)) {
        $error = "Only PDF, DOC, and DOCX files are allowed.";
    } elseif ($_FILES['resume']['size'] > $maxSize) {
        $error = "File size must be less than 5MB.";
    } elseif (move_uploaded_file($_FILES['resume']['tmp_name'], $targetFile)) {
        $displayName = htmlspecialchars($fileName);
        $pathToStore = str_replace('../', '', $targetFile);

        $stmt = $conn->prepare("INSERT INTO resume (file_name, file_path, uploaded_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $displayName, $pathToStore);
        $stmt->execute();
        $success = "Resume uploaded successfully.";
    } else {
        $error = "Failed to upload resume.";
    }
}

// Handle delete request
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = $_GET['delete'];

    // Get file path before deleting from DB
    $stmt = $conn->prepare("SELECT file_path FROM resume WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($filePath);
        $stmt->fetch();

        // Delete file from folder
        if (file_exists("../" . $filePath)) {
            unlink("../" . $filePath);
        }

        // Delete from DB
        $deleteStmt = $conn->prepare("DELETE FROM resume WHERE id = ?");
        $deleteStmt->bind_param("i", $deleteId);
        $deleteStmt->execute();
    }

    header("Location: manage-resume.php");
    exit();
}

// Fetch resumes
$resumeData = $conn->query("SELECT * FROM resume ORDER BY uploaded_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Resume</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Container */
        .container {
            max-width: 900px;
            margin: 40px auto 80px auto; /* bottom margin to avoid overlap with fixed button */
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-radius: 8px;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        table thead tr {
            background: linear-gradient(90deg, #007BFF, #00BFFF);
            color: #fff;
            text-transform: uppercase;
            font-size: 14px;
        }

        table th, table td {
            padding: 12px 18px;
            text-align: left;
            border-bottom: 1px solid #e2e2e2;
        }

        table tbody tr:hover {
            background-color: #f1faff;
        }

        /* Glowing link effect */
        .glow-link {
            color: #007BFF;
            text-decoration: none;
            font-weight: 600;
            position: relative;
            transition: color 0.3s ease-in-out;
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
        }

        .glow-link:hover {
            color: #00BFFF;
            animation: glowPulse 1.5s infinite alternate;
            text-shadow:
                0 0 6px #00BFFF,
                0 0 12px #00BFFF,
                0 0 18px #00BFFF,
                0 0 24px #00BFFF;
            background-color: rgba(0, 191, 255, 0.1);
        }

        @keyframes glowPulse {
            from {
                text-shadow:
                    0 0 6px #00BFFF,
                    0 0 12px #00BFFF,
                    0 0 18px #00BFFF,
                    0 0 24px #00BFFF;
            }
            to {
                text-shadow:
                    0 0 12px #00BFFF,
                    0 0 18px #00BFFF,
                    0 0 24px #00BFFF,
                    0 0 30px #00BFFF;
            }
        }

        /* Upload form */
        form {
            text-align: center;
            margin-bottom: 30px;
        }

        input[type="file"] {
            padding: 6px;
            font-size: 16px;
        }

        .upload-btn {
            background: linear-gradient(45deg, #28a745, #218838);
            color: white;
            padding: 10px 28px;
            font-size: 16px;
            font-weight: 700;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            box-shadow:
                0 0 10px #28a745,
                0 0 20px #218838,
                0 0 30px #28a745;
            transition: box-shadow 0.3s ease-in-out;
            margin-left: 10px;
        }

        .upload-btn:hover {
            box-shadow:
                0 0 15px #28a745,
                0 0 30px #218838,
                0 0 45px #28a745,
                0 0 60px #218838;
        }

        /* Responsive */
        @media screen and (max-width: 600px) {
            table thead {
                display: none;
            }
            table, table tbody, table tr, table td {
                display: block;
                width: 100%;
            }
            table tr {
                margin-bottom: 15px;
                border-bottom: 2px solid #ddd;
            }
            table td {
                text-align: right;
                padding-left: 50%;
                position: relative;
                border-bottom: 1px solid #eee;
            }
            table td::before {
                content: attr(data-label);
                position: absolute;
                left: 15px;
                width: 45%;
                padding-left: 10px;
                font-weight: 700;
                text-align: left;
                color: #333;
            }
        }

        /* Back to Dashboard Button */
        .back-button {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(45deg, #ff416c, #ff4b2b);
            color: white;
            padding: 12px 28px;
            font-size: 18px;
            font-weight: 700;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            text-decoration: none;
            box-shadow:
                0 0 8px #ff416c,
                0 0 20px #ff4b2b,
                0 0 30px #ff416c,
                0 0 40px #ff4b2b;
            transition: box-shadow 0.3s ease-in-out;
            z-index: 1000;
        }

        .back-button:hover {
            box-shadow:
                0 0 12px #ff416c,
                0 0 30px #ff4b2b,
                0 0 45px #ff416c,
                0 0 60px #ff4b2b,
                0 0 75px #ff416c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Upload New Resume</h2>
        <?php if (isset($success)) echo "<p style='color:green; text-align:center;'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p style='color:red; text-align:center;'>$error</p>"; ?>

        <form method="post" enctype="multipart/form-data">
            <input type="file" name="resume" required />
            <button type="submit" class="upload-btn">Upload Resume</button>
        </form>

        <h2>Uploaded Resume</h2>
        <hr>

        <?php if ($resumeData->num_rows > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>File Name</th>
                    <th>Uploaded At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; while ($row = $resumeData->fetch_assoc()): ?>
                <tr>
                    <td data-label="#"><?= $i++; ?></td>
                    <td data-label="File Name"><?= htmlspecialchars($row['file_name']); ?></td>
                    <td data-label="Uploaded At"><?= htmlspecialchars($row['uploaded_at']); ?></td>
                    <td data-label="Action">
                        <a class="glow-link" href="../<?= htmlspecialchars($row['file_path']); ?>" target="_blank" rel="noopener">View</a> |
                        <a class="glow-link" href="manage-resume.php?delete=<?= $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this resume?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>No resumes uploaded yet.</p>
        <?php endif; ?>
    </div>

    <a href="dashboard.php" class="back-button">← Back to Dashboard</a>
</body>
</html>
