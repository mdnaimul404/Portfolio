<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_FILES['certificates']['tmp_name'] as $index => $tmpName) {
        $fileName = basename($_FILES['certificates']['name'][$index]);
        $targetFile = '../uploads/certificates/' . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if (in_array($fileType, ['jpg', 'jpeg', 'png', 'pdf'])) {
            if (move_uploaded_file($tmpName, $targetFile)) {
                $title = $_POST['titles'][$index];
                $stmt = $conn->prepare("INSERT INTO certificates (file_name, title) VALUES (?, ?)");
                $stmt->bind_param("ss", $fileName, $title);
                $stmt->execute();
            }
        }
    }
    $success = "Certificates uploaded successfully.";
}
?>

<h2>Upload Certificates</h2>
<?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
<form method="post" enctype="multipart/form-data">
    <div id="certificate-section">
        <input type="file" name="certificates[]" required>
        <input type="text" name="titles[]" placeholder="Certificate Title" required>
    </div>
    <button type="button" onclick="addMore()">Add More</button><br><br>
    <button type="submit">Upload All</button>
</form>

<script>
function addMore() {
    const div = document.createElement("div");
    div.innerHTML = `<input type="file" name="certificates[]" required>
                     <input type="text" name="titles[]" placeholder="Certificate Title" required>`;
    document.getElementById("certificate-section").appendChild(div);
}
</script>
