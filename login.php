<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $user   = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header("Location: dashboard.php");
        exit();
    } else {
        $login_error = true;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Lost & Found</title>
    <link rel="stylesheet" href="css/login.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="container">
        <div class="section login-section">
            <div class="project-title">Lost & Found Portal</div>
            <h2>Login</h2>
            <?php if (!empty($login_error)): ?>
                <div id="login-error-popup" class="popup-error">
                    <span class="popup-error-message">Invalid email or password!</span>
                    <button class="popup-error-close" onclick="document.getElementById('login-error-popup').style.display='none'">&times;</button>
                </div>
            <?php endif; ?>
            <form method="POST" action="" class="login-form">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="login-btn">Login</button>
            </form>
            <p class="register-link">Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>
    <style>
        .popup-error {
            position: fixed;
            top: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            color: #fff;
            padding: 1rem 2.5rem 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(231, 76, 60, 0.15);
            z-index: 9999;
            display: flex;
            align-items: center;
            font-size: 1.1rem;
            animation: fadeInDown 0.5s;
        }
        .popup-error-message {
            flex: 1;
        }
        .popup-error-close {
            background: none;
            border: none;
            color: #fff;
            font-size: 1.5rem;
            margin-left: 1.2rem;
            cursor: pointer;
            font-weight: bold;
            transition: color 0.2s;
        }
        .popup-error-close:hover {
            color: #ffd6d6;
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px) translateX(-50%); }
            to { opacity: 1; transform: translateY(0) translateX(-50%); }
        }
    </style>
</body>
</html>
