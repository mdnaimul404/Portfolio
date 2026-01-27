<?php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    // Basic validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        die("❌ Please fill in all fields.");
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("❌ Invalid email address. Please provide a valid one.");
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        die("❌ Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssss", $name, $email, $subject, $message);

    // Execute and redirect or show error
    if ($stmt->execute()) {
        header("Location: message-success.php");
        exit();
    } else {
        die("❌ Database error: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: contact.php");
    exit();
}
