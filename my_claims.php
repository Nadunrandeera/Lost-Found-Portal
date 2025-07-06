<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/db.php';

$user_id = $_SESSION['user_id'];

// Get claims for items posted by this user
$stmt = $conn->prepare("
    SELECT claims.*, items.title 
    FROM claims 
    JOIN items ON claims.item_id = items.id 
    WHERE items.user_id = ?
    ORDER BY claims.claim_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$results = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Item Claims</title>
    <style>
        .claim {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px;
        }
    </style>
</head>
<body>
    <h2>📨 Claim Requests for Your Items</h2>
    <p><a href="dashboard.php">⬅ Back to Dashboard</a></p>

    <?php if ($results->num_rows > 0): ?>
        <?php while ($row = $results->fetch_assoc()): ?>
            <div class="claim">
                <strong>Item:</strong> <?php echo htmlspecialchars($row['title']); ?><br>
                <strong>Claimer:</strong> <?php echo htmlspecialchars($row['claimer_name']); ?><br>
                <strong>Message:</strong><br>
                <em><?php echo nl2br(htmlspecialchars($row['message'])); ?></em><br>
                <strong>Date:</strong> <?php echo $row['claim_date']; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No claims submitted for your items yet.</p>
    <?php endif; ?>
</body>
</html>
