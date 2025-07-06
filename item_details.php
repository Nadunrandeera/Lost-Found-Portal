<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/db.php';

if (!isset($_GET['id'])) {
    echo "Item ID is missing.";
    exit();
}

$item_id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM items WHERE id = ? AND status = 'open'");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$item) {
    echo "Item not found.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Item Details</title>
</head>
<body>
    <h2><?php echo htmlspecialchars($item['title']); ?></h2>
    <img src="<?php echo $item['image_path']; ?>" width="300"><br><br>
    <strong>Type:</strong> <?php echo $item['type']; ?><br>
    <strong>Category:</strong> <?php echo $item['category']; ?><br>
    <strong>Description:</strong><br>
    <p><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
    <strong>Status:</strong> <?php echo $item['status']; ?><br><br>

    <?php if ($item['status'] == 'open') { ?>
        <h3>Claim This Item</h3>
        <form method="POST" action="claim_item.php">
            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
            Your Name: <input type="text" name="claimer_name" required><br><br>
            Why is this item yours?<br>
            <textarea name="message" required></textarea><br><br>
            <button type="submit">Submit Claim</button>
        </form>
    <?php } else { ?>
        <p><strong>This item has already been claimed.</strong></p>
    <?php } ?>

    <br><a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
