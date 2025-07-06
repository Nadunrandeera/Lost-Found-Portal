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
</head>
<body>
    <h2>Report a Lost Item</h2>
    <form method="POST" action="" enctype="multipart/form-data">
        Title: <input type="text" name="title" required><br><br>
        Description:<br>
        <textarea name="description" required></textarea><br><br>
        Category: 
        <input type="text" name="category" required><br><br>
        Upload Image: 
        <input type="file" name="image" accept="image/*" required><br><br>
        <button type="submit">Submit</button>
    </form>
    <br>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
