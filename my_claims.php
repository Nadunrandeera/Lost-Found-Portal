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
    SELECT claims.*, items.title, items.status 
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
    <link rel="stylesheet" href="css/claim.css">
</head>
<body>
    <div class="claims-container">
        <div class="claims-title">📨 Claim Requests for Your Items</div>
        <a href="dashboard.php" class="back-link">⬅ Back to Dashboard</a>

        <?php if ($results->num_rows > 0): ?>
            <?php while ($row = $results->fetch_assoc()): ?>
                <div class="claim-card">
                    <strong>Item:</strong> <?php echo htmlspecialchars($row['title']); ?><br>
                    <strong>Claimer:</strong> <?php echo htmlspecialchars($row['claimer_name']); ?><br>
                    <strong>Message:</strong> <em><?php echo nl2br(htmlspecialchars($row['message'])); ?></em><br>
                    <span class="claim-date"><strong>Date:</strong> <?php echo $row['claim_date']; ?></span>
                    <?php if ($row['status'] === 'pending'): ?>
                        <div class="claim-pending-msg">Wait For the Admin Approve...!</div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No claims submitted for your items yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
