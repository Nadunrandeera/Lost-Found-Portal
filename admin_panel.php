<?php
session_start();
include 'includes/db.php';

// Check if user is logged in and admin
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($is_admin);
$stmt->fetch();
$stmt->close();

if (!$is_admin) {
    echo "Access denied. Admins only.";
    exit();
}

// Fetch all items
$items = $conn->query("SELECT items.*, users.name AS posted_by FROM items JOIN users ON items.user_id = users.id ORDER BY created_at DESC");

// Handle approve/delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve_id'])) {
        $approve_id = $_POST['approve_id'];
        $stmt = $conn->prepare("UPDATE items SET status = 'open' WHERE id = ?");
        $stmt->bind_param("i", $approve_id);
        $stmt->execute();
        header("Location: admin_panel.php");
        exit();
    }
    if (isset($_POST['delete_id'])) {
        $delete_id = $_POST['delete_id'];

        // Delete related claims first
        $del_claims = $conn->prepare("DELETE FROM claims WHERE item_id = ?");
        $del_claims->bind_param("i", $delete_id);
        $del_claims->execute();

        // Delete item image path before deleting item
        $img_stmt = $conn->prepare("SELECT image_path FROM items WHERE id = ?");
        $img_stmt->bind_param("i", $delete_id);
        $img_stmt->execute();
        $result = $img_stmt->get_result();
        $item = $result->fetch_assoc();
        if ($item && file_exists($item['image_path'])) {
            unlink($item['image_path']);
        }

        // Delete item
        $stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();

        header("Location: admin_panel.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Lost & Found</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 8px; border: 1px solid #ccc; }
        img { max-width: 100px; height: auto; }
        form { display: inline; }
    </style>
</head>
<body>
    <h2>Admin Panel</h2>
    <p><a href="dashboard.php">⬅ Back to Dashboard</a> | <a href="logout.php">Logout</a></p>

    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Image</th>
            <th>Type</th>
            <th>Status</th>
            <th>Posted By</th>
            <th>Actions</th>
        </tr>

        <?php while ($row = $items->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="image"></td>
            <td><?php echo $row['type']; ?></td>
            <td><?php echo $row['status']; ?></td>
            <td><?php echo htmlspecialchars($row['posted_by']); ?></td>
            <td>
                <?php if ($row['status'] == 'pending'): ?>
                <form method="POST" onsubmit="return confirm('Approve this item?');">
                    <input type="hidden" name="approve_id" value="<?php echo $row['id']; ?>">
                    <button type="submit">Approve</button>
                </form>
                <?php endif; ?>

                <form method="POST" onsubmit="return confirm('Delete this item?');">
                    <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                    <button type="submit">Delete</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>

    </table>
</body>
</html>
