<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/db.php';

$result = $conn->query("SELECT * FROM items ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Lost & Found</title>
    <style>
        .item {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px;
        }
        img {
            max-width: 150px;
        }
    </style>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION['user_name']; ?>!</h2>
    <p><a href="post_lost.php">Report Lost Item</a> | <a href="logout.php">Logout</a></p>

    <h3>All Reported Items</h3>

    <?php while ($row = $result->fetch_assoc()) { ?>
        <div class="item">
            <img src="<?php echo $row['image_path']; ?>" alt="Item image"><br>
            <strong><?php echo htmlspecialchars($row['title']); ?></strong><br>
            Type: <?php echo $row['type']; ?><br>
            Category: <?php echo $row['category']; ?><br>
            <a href="item_details.php?id=<?php echo $row['id']; ?>">View Details</a>
        </div>
    <?php } ?>
</body>
</html>
