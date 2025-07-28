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
$stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
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
    <link rel="stylesheet" href="css/item.css">
</head>
<body>
    <div class="item-container">
        <div class="item-title"><?php echo htmlspecialchars($item['title']); ?></div>
        <img src="<?php echo $item['image_path']; ?>" class="item-image" alt="Item Image"><br>
        <div class="item-info">
            <!-- <strong>Type:</strong> <?php echo $item['type']; ?><br> -->
            <strong>Category:</strong> <?php echo $item['category']; ?><br>
            <strong>Description:</strong> <?php echo nl2br(htmlspecialchars($item['description'])); ?><br>
        </div>
        <div class="item-status <?php echo $item['status'] == 'open' ? 'status-open' : 'status-claimed'; ?>">
            <?php echo ucfirst($item['status']); ?>
        </div>
        <?php if ($item['status'] === 'pending'): ?>
            <div style="margin: 1rem 0; background: linear-gradient(45deg, #ffb347, #ffcc80); color: #a65c00; padding: 0.7rem 1.2rem; border-radius: 12px; font-weight: 600; font-size: 1rem; text-align: center; box-shadow: 0 2px 8px rgba(255, 179, 71, 0.08); letter-spacing: 0.5px;">Wait For the Admin Approve...!</div>
        <?php endif; ?>
        <br><br>

        <?php if ($item['status'] == 'open') { ?>
            <div class="claim-section">
                <h3>Claim This Item</h3>
                <form method="POST" action="claim_item.php" class="claim-form">
                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                    <label for="claimer_name">Your Name:</label>
                    <input type="text" id="claimer_name" name="claimer_name" required>
                    <label for="message">Why is this item yours?</label>
                    <textarea id="message" name="message" required></textarea>
                    <button type="submit" class="claim-btn">Submit Claim</button>
                </form>
            </div>
        <?php } elseif ($item['status'] == 'claimed') { ?>
            <p><strong>This item has already been claimed.</strong></p>
        <?php } ?>

        <a href="dashboard.php" class="back-link">&larr; Back to Dashboard</a>
    </div>
</body>
</html>