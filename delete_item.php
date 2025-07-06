<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'])) {
    $item_id = $_POST['item_id'];
    $user_id = $_SESSION['user_id'];

    // Get image path first
    $img_stmt = $conn->prepare("SELECT image_path FROM items WHERE id = ? AND user_id = ?");
    $img_stmt->bind_param("ii", $item_id, $user_id);
    $img_stmt->execute();
    $result = $img_stmt->get_result();
    $item = $result->fetch_assoc();

    if ($item && file_exists($item['image_path'])) {
        unlink($item['image_path']); // delete the image file
    }

    // 🚨 FIRST: Delete related claims
    $del_claims = $conn->prepare("DELETE FROM claims WHERE item_id = ?");
    $del_claims->bind_param("i", $item_id);
    $del_claims->execute();

    // ✅ THEN: Delete the item
    $stmt = $conn->prepare("DELETE FROM items WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $item_id, $user_id);
    $stmt->execute();

    header("Location: dashboard.php");
    exit();
}
?>
