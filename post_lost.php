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
        $post_success = true;
    } else {
        $post_error = $conn->error;
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
    <?php if (!empty($post_success)): ?>
        <div class="center-popup-success">
            <div class="center-popup-content">
                <span class="center-popup-message">Lost item posted successfully!</span>
                <a href="dashboard.php" class="center-popup-btn">Back to Dashboard</a>
            </div>
        </div>
    <?php elseif (!empty($post_error)): ?>
        <div class="center-popup-error">
            <div class="center-popup-content">
                <span class="center-popup-message">Error: <?php echo htmlspecialchars($post_error); ?></span>
                <a href="dashboard.php" class="center-popup-btn">Back to Dashboard</a>
            </div>
        </div>
    <?php endif; ?>
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

<style>
.center-popup-success, .center-popup-error {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.25);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}
.center-popup-content {
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.15);
  padding: 2.5rem 2rem 2rem 2rem;
  min-width: 320px;
  max-width: 90vw;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.5rem;
}
.center-popup-success .center-popup-content {
  border: 3px solid #27ae60;
}
.center-popup-error .center-popup-content {
  border: 3px solid #e74c3c;
}
.center-popup-message {
  font-size: 1.3rem;
  font-weight: 700;
  color: #2c3e50;
}
.center-popup-btn {
  background: linear-gradient(45deg, #667eea, #764ba2);
  color: #fff;
  border: none;
  padding: 0.9rem 2.2rem;
  border-radius: 25px;
  font-weight: 600;
  cursor: pointer;
  text-decoration: none;
  font-size: 1.1rem;
  margin-top: 0.5rem;
  transition: all 0.3s ease;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  box-shadow: 0 5px 15px rgba(102, 126, 234, 0.18);
}
.center-popup-btn:hover {
  background: linear-gradient(45deg, #764ba2, #667eea);
  color: #fff;
  transform: translateY(-2px);
}
</style>
