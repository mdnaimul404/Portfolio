<?php
include '../includes/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: blogs.php");
    exit();
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM blogs WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: blogs.php");
    exit();
}

$blog = $result->fetch_assoc();

// Handle comment submission
$comment_error = '';
$comment_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_submit'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $comment = trim($_POST['comment']);

    if ($name && $comment) {
        $stmt = $conn->prepare("INSERT INTO comments (blog_id, name, email, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $id, $name, $email, $comment);
        if ($stmt->execute()) {
            $comment_success = "Thank you! Your comment is submitted and pending approval.";
            header("Location: blog.php?id=$id");
            exit();
        } else {
            $comment_error = "Something went wrong. Please try again.";
        }
    } else {
        $comment_error = "Name and comment are required.";
    }
}

// Fetch approved comments
$commentStmt = $conn->prepare("SELECT * FROM comments WHERE blog_id = ? AND status = 'approved' ORDER BY created_at DESC");
$commentStmt->bind_param("i", $id);
$commentStmt->execute();
$commentsResult = $commentStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= htmlspecialchars($blog['title']) ?></title>
    <style>
        /* Existing comment and page styles */

        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #fafafa;
            color: #333;
            max-width: 900px;
            margin: auto;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
        }

        /* Blog image styles */
        .blog-image {
            margin: 20px 0;
            text-align: center;
        }
        .blog-image img {
            max-width: 100%;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .blog-image img:hover {
            transform: scale(1.05);
        }

        .blog-content {
            margin-top: 20px;
            line-height: 1.6;
            font-size: 1.1rem;
            color: #444;
        }

        .comment-section {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #ccc;
        }

        .comment {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }

        .comment strong {
            font-weight: 600;
        }

        .comment small {
            color: #777;
        }

        .comment-form input, .comment-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 1rem;
            font-family: inherit;
        }

        .comment-form button {
            background-color: #3498db;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .comment-form button:hover {
            background-color: #2980b9;
        }

        .comment-message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 6px;
        }

        .comment-message.success {
            background-color: #d4edda;
            color: #155724;
        }

        .comment-message.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        a.back-button {
            display: inline-block;
            margin-top: 40px;
            text-decoration: none;
            color: #3498db;
            font-weight: 600;
            font-size: 1rem;
        }
        a.back-button:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1><?= htmlspecialchars($blog['title']) ?></h1>

    <?php if (!empty($blog['image'])): ?>
        <div class="blog-image">
            <img src="../<?= htmlspecialchars($blog['image']) ?>" alt="<?= htmlspecialchars($blog['title']) ?>">
        </div>
    <?php endif; ?>

    <div class="blog-content">
        <p><?= nl2br(htmlspecialchars($blog['content'])) ?></p>
    </div>

    <!-- Comment Section -->
    <div class="comment-section">
        <h3>Comments</h3>

        <?php if ($comment_success): ?>
            <div class="comment-message success"><?= htmlspecialchars($comment_success) ?></div>
        <?php elseif ($comment_error): ?>
            <div class="comment-message error"><?= htmlspecialchars($comment_error) ?></div>
        <?php endif; ?>

        <!-- Approved Comments -->
        <?php if ($commentsResult->num_rows > 0): ?>
            <?php while ($c = $commentsResult->fetch_assoc()): ?>
                <div class="comment">
                    <strong><?= htmlspecialchars($c['name']) ?></strong>
                    <small> on <?= $c['created_at'] ?></small>
                    <p><?= nl2br(htmlspecialchars($c['comment'])) ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No comments yet. Be the first to comment!</p>
        <?php endif; ?>

        <hr style="margin: 20px 0;">

        <!-- Comment Form -->
        <form method="POST" class="comment-form">
            <input type="text" name="name" placeholder="Your Name *" required>
            <input type="email" name="email" placeholder="Your Email (optional)">
            <textarea name="comment" rows="4" placeholder="Your Comment *" required></textarea>
            <button type="submit" name="comment_submit">Submit Comment</button>
        </form>
    </div>

    <a href="blogs.php" class="back-button">← Back to Blogs</a>
</body>
</html>
