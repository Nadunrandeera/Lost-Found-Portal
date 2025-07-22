<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/db.php';

$user_id = $_SESSION['user_id'];

// --- Search/Filter Input ---
$search_title    = isset($_GET['search_title']) ? '%' . $_GET['search_title'] . '%' : '%';
$search_category = isset($_GET['search_category']) ? '%' . $_GET['search_category'] . '%' : '%';
$search_type     = isset($_GET['search_type']) ? $_GET['search_type'] : '';

// --- Search Query ---
$query = $query = "SELECT * FROM items WHERE status = 'open' AND title LIKE ? AND category LIKE ?";
$params = [$search_title, $search_category];
$types = "ss";

if (!empty($search_type)) {
    $query .= " AND type = ?";
    $params[] = $search_type;
    $types .= "s";
}

$query .= " ORDER BY created_at DESC";

// --- Execute Search Query ---
$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$items = $stmt->get_result();

// --- My Posts Query ---
$my_stmt = $conn->prepare("SELECT * FROM items WHERE user_id = ? ORDER BY created_at DESC");
$my_stmt->bind_param("i", $user_id);
$my_stmt->execute();
$my_items = $my_stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Lost & Found Portal</title>
    <link rel="stylesheet" href="css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="header">
        <div class="welcome-section">
            <div class="welcome-text">
                Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
                <?php
                    $stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
                    $stmt->bind_param("i", $_SESSION['user_id']);
                    $stmt->execute();
                    $stmt->bind_result($is_admin);
                    $stmt->fetch();
                    $stmt->close();

                    if ($is_admin) {
                        echo "<span class='user-badge admin'>Admin</span>";
                    } else {
                        echo "<span class='user-badge user'>User</span>";
                    }
                ?>
            </div>
            <div class="nav-links">
                <a href="post_lost.php" class="primary">📝 Report Lost Item</a>
                <a href="my_claims.php" class="secondary">📋 My Claims</a>
                <a href="logout.php" class="secondary">🚪 Logout</a>
            </div>
        </div>
    </div>

    <div class="container">



    <!-- 🔎 Filter Form -->
    <div class="section">
        <h3>🔍 Search / Filter Items</h3>
        <form method="GET" action="" class="search-form">
            <div class="form-group">
                <label for="search_title">Title</label>
                <input type="text" id="search_title" name="search_title" value="<?php echo isset($_GET['search_title']) ? htmlspecialchars($_GET['search_title']) : ''; ?>" placeholder="Search by title...">
            </div>
            <div class="form-group">
                <label for="search_category">Category</label>
                <input type="text" id="search_category" name="search_category" value="<?php echo isset($_GET['search_category']) ? htmlspecialchars($_GET['search_category']) : ''; ?>" placeholder="Search by category...">
            </div>
            <div class="form-group">
                <label for="search_type">Type</label>
                <select id="search_type" name="search_type">
                    <option value="">Any Type</option>
                    <option value="lost" <?php if (isset($_GET['search_type']) && $_GET['search_type'] == 'lost') echo 'selected'; ?>>Lost</option>
                    <option value="found" <?php if (isset($_GET['search_type']) && $_GET['search_type'] == 'found') echo 'selected'; ?>>Found</option>
                </select>
            </div>
            <button type="submit" class="search-btn">🔍 Search</button>
        </form>
    </div>

    <!-- 📝 My Posts -->
    <div class="section">
        <h3>📌 My Posted Items</h3>
        <?php if ($my_items->num_rows > 0): ?>
            <div class="items-grid">
                <?php while ($row = $my_items->fetch_assoc()): ?>
                    <div class="item">
                        <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="item image" class="item-image">
                        <div class="item-content">
                            <div class="item-title"><?php echo htmlspecialchars($row['title']); ?></div>
                            <div class="item-meta">
                                <span class="item-tag type-<?php echo $row['type']; ?>"><?php echo ucfirst($row['type']); ?></span>
                                <span class="item-tag category"><?php echo htmlspecialchars($row['category']); ?></span>
                                <span class="status-badge status-<?php echo $row['status']; ?>">
                                    <?php
                                        if ($row['status'] == 'pending') {
                                            echo "⏳ Pending";
                                        } elseif ($row['status'] == 'closed') {
                                            echo "✔️ Closed";
                                        } else {
                                            echo "✅ Open";
                                        }
                                    ?>
                                </span>
                            </div>
                            <div class="item-actions">
                                <a href="item_details.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">👁️ View Details</a>
                                <?php if ($row['status'] != 'closed'): ?>
                                    <form method="POST" action="close_item.php" style="display:inline;">
                                        <input type="hidden" name="item_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="btn btn-success">✅ Close</button>
                                    </form>
                                <?php endif; ?>
                                <form method="POST" action="delete_item.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                    <input type="hidden" name="item_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn btn-danger">🗑️ Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <h4>📭 No Items Posted Yet</h4>
                <p>You haven't posted any items yet. Start by reporting a lost or found item!</p>
                <a href="post_lost.php" class="btn btn-primary">📝 Post Your First Item</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- 📦 Filtered Items -->
    <div class="section">
        <h3>📦 All Items (Filtered)</h3>
        <?php if ($items->num_rows > 0): ?>
            <div class="items-grid">
                <?php while ($row = $items->fetch_assoc()): ?>
                    <div class="item">
                        <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="item image" class="item-image">
                        <div class="item-content">
                            <div class="item-title"><?php echo htmlspecialchars($row['title']); ?></div>
                            <div class="item-meta">
                                <span class="item-tag type-<?php echo $row['type']; ?>"><?php echo ucfirst($row['type']); ?></span>
                                <span class="item-tag category"><?php echo htmlspecialchars($row['category']); ?></span>
                            </div>
                            <div class="item-actions">
<a href="item_details.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">👁️ View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <h4>😕 No Items Found</h4>
                <p>No items found matching your filter. Try adjusting your search criteria.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
