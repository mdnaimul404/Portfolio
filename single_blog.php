<?php
include 'includes/db.php';

// Get blog ID from URL
$blogId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch blog data
$stmt = $conn->prepare("SELECT * FROM blogs WHERE id = ?");
$stmt->bind_param("i", $blogId);
$stmt->execute();
$blogResult = $stmt->get_result();

if ($blogResult->num_rows === 0) {
    echo "Blog not found.";
    exit;
}
$blog = $blogResult->fetch_assoc();

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_submit'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $comment = trim($_POST['comment']);

    if ($name && $comment) {
        $stmt = $conn->prepare("INSERT INTO comments (blog_id, name, email, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $blogId, $name, $email, $comment);
        $stmt->execute();
        echo "<p style='color:green;'>Thank you for your comment! It will be visible once approved.</p>";
    } else {
        echo "<p style='color:red;'>Please fill in required fields (name and comment).</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($blog['title']) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <h1><?= htmlspecialchars($blog['title']) ?></h1>
    <p><?= nl2br(htmlspecialchars($blog['content'])) ?></p>

    <hr>

    <h3>Leave a Comment</h3>
    <form method="post">
        <label>Name*: <br><input type="text" name="name" required></label><br><br>
        <label>Email: <br><input type="email" name="email"></label><br><br>
        <label>Comment*: <br><textarea name="comment" rows="4" required></textarea></label><br><br>
        <button type="submit" name="comment_submit">Submit Comment</button>
    </form>

    <hr>

    <h3>Comments</h3>
    <?php
    $commentQuery = $conn->prepare("SELECT * FROM comments WHERE blog_id = ? AND status = 'approved' ORDER BY created_at DESC");
    $commentQuery->bind_param("i", $blogId);
    $commentQuery->execute();
    $commentsResult = $commentQuery->get_result();

    if ($commentsResult->num_rows > 0) {
        while ($c = $commentsResult->fetch_assoc()) {
            $avatar = $c['avatar'] ? $c['avatar'] : 'assets/default-avatar.png';
            echo "<div style='border-bottom:1px solid #ddd; margin-bottom:10px; padding-bottom:10px;'>";
            echo "<img src='{$avatar}' alt='avatar' width='40' style='vertical-align:middle; border-radius:50%;'>";
            echo "<strong> " . htmlspecialchars($c['name']) . "</strong> <small>on " . $c['created_at'] . "</small><br>";
            echo "<p>" . nl2br(htmlspecialchars($c['comment'])) . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>No comments yet. Be the first to comment!</p>";
    }
    ?>
</body>
</html>
