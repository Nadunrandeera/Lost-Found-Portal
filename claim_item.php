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
        echo "<div class='claim-popup'>
                <div class='claim-popup-content'>
                    <span class='claim-popup-success'>✔️ Your claim has been submitted.</span><br>
                    <a href='dashboard.php' class='claim-popup-link'>Go back</a>
                </div>
            </div>
            <link rel='stylesheet' href='css/claim_popup.css'>";
    } else {
        echo "Error submitting claim.";
    }

    $stmt->close();
}
?>
