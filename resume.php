<?php
include 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Resume | Md. Naimul Islam</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f0f4ff;
      color: #121212;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      flex-direction: column;
      text-align: center;
      padding: 20px;
    }
    h1 {
      margin-bottom: 20px;
    }
    a.download-btn {
      display: inline-block;
      margin-top: 20px;
      padding: 12px 28px;
      font-size: 18px;
      font-weight: 700;
      background: linear-gradient(45deg, #007BFF, #00BFFF);
      color: white;
      text-decoration: none;
      border-radius: 30px;
      box-shadow:
        0 0 8px #007BFF,
        0 0 20px #00BFFF;
      transition: box-shadow 0.3s ease-in-out;
    }
    a.download-btn:hover {
      box-shadow:
        0 0 15px #007BFF,
        0 0 30px #00BFFF,
        0 0 45px #007BFF;
    }
    p.no-resume {
      font-size: 1.2rem;
      color: #555;
      margin-top: 40px;
    }
  </style>
</head>
<body>
  <h1>My Latest Resume</h1>

  <?php
  $res = $conn->query("SELECT * FROM resume ORDER BY uploaded_at DESC LIMIT 1");
  if ($res->num_rows > 0) {
      $row = $res->fetch_assoc();
      // Use file_path as stored in DB to link correctly
      echo "<a class='download-btn' href='{$row['file_path']}' target='_blank' rel='noopener'>Download Resume</a>";
  } else {
      echo "<p class='no-resume'>No resume uploaded yet.</p>";
  }
  ?>
</body>
</html>
