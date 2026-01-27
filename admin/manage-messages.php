<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

$result = $conn->query("SELECT * FROM messages ORDER BY sent_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Contact Messages</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #edf0f5;
            margin: 0;
            padding: 30px;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        a {
            color: #3498db;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .message-box {
            background: #ffffff;
            border: none;
            border-left: 6px solid #3498db;
            padding: 20px 25px;
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .message-box:hover {
            transform: translateY(-3px);
            background-color: #fdfdfd;
        }

        .subject {
            font-weight: 600;
            font-size: 18px;
            color: #2c3e50;
        }

        .meta {
            font-size: 13px;
            color: #777;
            margin-top: 5px;
        }

        .preview {
            margin-top: 12px;
            font-size: 15px;
            color: #444;
            line-height: 1.5em;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background: #ffffff;
            border-radius: 16px;
            padding: 30px;
            max-width: 600px;
            width: 90%;
            margin: 80px auto;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
            position: relative;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .close {
            position: absolute;
            top: 18px;
            right: 24px;
            font-size: 26px;
            color: #aaa;
            cursor: pointer;
            transition: 0.2s;
        }

        .close:hover {
            color: #e74c3c;
        }

        .modal h2 {
            margin-top: 0;
            color: #2c3e50;
        }

        .modal p {
            margin: 8px 0;
            font-size: 15px;
            color: #444;
        }

        .modal hr {
            margin: 20px 0;
            border: none;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>

<h1>Contact Messages</h1>
<p><a href="dashboard.php">← Back to Dashboard</a></p>

<?php if ($result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>
        <div class="message-box" onclick="openModal(`<?= htmlspecialchars(addslashes($row['name'])) ?>`, `<?= htmlspecialchars(addslashes($row['email'])) ?>`, `<?= htmlspecialchars(addslashes($row['subject'])) ?>`, `<?= nl2br(htmlspecialchars(addslashes($row['message']))) ?>`, `<?= htmlspecialchars($row['sent_at']) ?>`)">
            <div class="subject"><?= htmlspecialchars($row['subject']) ?></div>
            <div class="meta">
                From: <?= htmlspecialchars($row['name']) ?> | <?= htmlspecialchars($row['email']) ?> | <?= htmlspecialchars($row['sent_at']) ?>
            </div>
            <div class="preview">
                <?php
                    $lines = explode("\n", $row['message']);
                    echo htmlspecialchars($lines[0] ?? '');
                    echo isset($lines[1]) ? '<br>' . htmlspecialchars($lines[1]) : '';
                ?>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No messages found.</p>
<?php endif; ?>

<!-- Modal -->
<div id="messageModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalSubject"></h2>
        <p><strong>From:</strong> <span id="modalName"></span> &lt;<span id="modalEmail"></span>&gt;</p>
        <p><strong>Sent at:</strong> <span id="modalTime"></span></p>
        <hr>
        <p id="modalMessage"></p>
    </div>
</div>

<script>
    function openModal(name, email, subject, message, time) {
        document.getElementById("modalSubject").textContent = subject;
        document.getElementById("modalName").textContent = name;
        document.getElementById("modalEmail").textContent = email;
        document.getElementById("modalTime").textContent = time;
        document.getElementById("modalMessage").innerHTML = message;
        document.getElementById("messageModal").style.display = "block";
    }

    function closeModal() {
        document.getElementById("messageModal").style.display = "none";
    }

    window.onclick = function(event) {
        const modal = document.getElementById("messageModal");
        if (event.target === modal) {
            modal.style.display = "none";
        }
    }
</script>

</body>
</html>
