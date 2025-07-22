<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title       = $_POST['title'];
    $description = $_POST['description'];
    $category    = $_POST['category'];
    $type        = 'lost'; // fixed for this page
    $user_id     = $_SESSION['user_id'];

    // Handle image upload
    $image_name = $_FILES['image']['name'];
    $tmp_name   = $_FILES['image']['tmp_name'];
    $target_dir = "images/";
    $image_path = $target_dir . time() . "_" . basename($image_name);

    move_uploaded_file($tmp_name, $image_path);

    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO items (user_id, title, description, type, category, image_path, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())");
    $stmt->bind_param("isssss", $user_id, $type, $title, $description, $category, $image_path);

    if ($stmt->execute()) {
        echo "Lost item posted successfully! <a href='dashboard.php'>Go back to dashboard</a>";
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Post Lost Item</title>
    <link rel="stylesheet" type="text/css" href="css/login.css">
    <link rel="stylesheet" type="text/css" href="css/report.css">
</head>
<body class="report-body">
    <div class="report-container">
        <h2 class="report-title">Report a Lost Item</h2>
        <form class="report-form" method="POST" action="" enctype="multipart/form-data">
            <label for="title" class="report-label">Title:</label>
            <input type="text" id="title" name="title" class="report-input" required><br><br>
            <label for="description" class="report-label">Description:</label><br>
            <textarea id="description" name="description" class="report-textarea" required></textarea><br><br>
            <label for="category" class="report-label">Category:</label>
            <input type="text" id="category" name="category" class="report-input" required><br><br>
            <label for="image" class="report-label">Upload Image:</label>
            <input type="file" id="image" name="image" class="report-input" accept="image/*" required><br><br>
            <button type="submit" class="report-btn">Submit</button>
        </form>
        <br>
        <a href="dashboard.php" class="report-back">Back to Dashboard</a>
    </div>
</body>
</html>
