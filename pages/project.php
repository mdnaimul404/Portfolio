<?php
include '../includes/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: projects.php");
    exit();
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: projects.php");
    exit();
}

$project = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($project['title']) ?></title>
  <style>
    /* General reset */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* Body and container */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f7f9fc;
        color: #333;
        line-height: 1.6;
        padding: 40px 20px;
        display: flex;
        justify-content: center;
        min-height: 100vh;
    }

    .container {
        max-width: 900px;
        width: 100%;
        background-color: #fff;
        padding: 40px 50px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        gap: 30px;
        align-items: center;
    }

    .container:hover {
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    /* Heading */
    h1 {
        font-size: 2.8rem;
        font-weight: 700;
        color: #0052cc;
        margin-bottom: 20px;
        text-align: center;
    }

    /* Description */
    p {
        font-size: 1.125rem;
        color: #444;
        text-align: justify;
        width: 100%;
    }

    /* Strong label styling */
    strong {
        font-weight: 600;
        color: #222;
        margin-bottom: 10px;
        display: block;
    }

    /* Image container */
    .project-image {
        margin: 30px 0;
        text-align: center;
        width: 100%;
    }

    .project-image img {
        max-width: 100%;
        border-radius: 15px;
        box-shadow: 0 8px 24px rgba(0, 82, 204, 0.2);
        transition: transform 0.3s ease;
    }

    .project-image img:hover {
        transform: scale(1.05);
    }

    /* Back button styling */
    .back-btn {
        background-color: #0052cc;
        color: #fff;
        border: none;
        padding: 14px 30px;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
        margin-top: 20px;
    }

    .back-btn:hover {
        background-color: #003d99;
        box-shadow: 0 8px 25px rgba(0, 61, 153, 0.6);
    }

    /* Responsive */
    @media (max-width: 768px) {
        body {
            padding: 20px 10px;
        }
        .container {
            padding: 25px 20px;
        }
        h1 {
            font-size: 2rem;
        }
        p {
            font-size: 1rem;
        }
        .back-btn {
            font-size: 1rem;
            padding: 12px 25px;
        }
    }
  </style>
</head>
<body>
  <div class="container">
    <h1><?= htmlspecialchars($project['title']) ?></h1>
    <p><strong>Description:</strong></p>
    <p><?= nl2br(htmlspecialchars($project['description'])) ?></p>

    <?php if (!empty($project['image_path'])): ?>
      <div class="project-image">
        <img src="../uploads/<?= htmlspecialchars($project['image_path']) ?>" alt="<?= htmlspecialchars($project['title']) ?>" />
      </div>
    <?php endif; ?>

    <button class="back-btn" onclick="window.location.href='projects.php'">← Back to Projects</button>
  </div>
</body>
</html>
