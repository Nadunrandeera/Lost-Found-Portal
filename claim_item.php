<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_id      = $_POST['item_id'];
    $claimer_name = $_POST['claimer_name'];
    $message      = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO claims (item_id, claimer_name, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $item_id, $claimer_name, $message);

    if ($stmt->execute()) {
        // Optional: Set item as closed
        $update = $conn->prepare("UPDATE items SET status = 'claimed' WHERE id = ?");
        $update->bind_param("i", $item_id);
        $update->execute();
        echo "Your claim has been submitted. <a href='dashboard.php'>Go back</a>";
    } else {
        echo "Error submitting claim.";
    }

    $stmt->close();
}
?>
