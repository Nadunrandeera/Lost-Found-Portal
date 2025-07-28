<?php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        $register_success = true;
    } else {
        $register_error = $conn->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Lost & Found</title>
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
    <div class="container">
        <div class="section register-section">
            <h2>Register</h2>
            <?php if (!empty($register_success)): ?>
                <div id="register-success-popup" class="popup-success">
                    <span class="popup-success-message">Registered successfully! <a href='login.php' style='color:#fff;text-decoration:underline;font-weight:bold;'>Login now</a></span>
                    <button class="popup-success-close" onclick="document.getElementById('register-success-popup').style.display='none'">&times;</button>
                </div>
            <?php elseif (!empty($register_error)): ?>
                <div id="register-error-popup" class="popup-error">
                    <span class="popup-error-message">Error: <?php echo htmlspecialchars($register_error); ?></span>
                    <button class="popup-error-close" onclick="document.getElementById('register-error-popup').style.display='none'">&times;</button>
                </div>
            <?php endif; ?>
            <form method="POST" action="" class="register-form">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="register-btn">Register</button>
            </form>
            <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
    <style>
        .popup-success {
            position: fixed;
            top: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(45deg, #27ae60, #2ecc71);
            color: #fff;
            padding: 1rem 2.5rem 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(39, 174, 96, 0.15);
            z-index: 9999;
            display: flex;
            align-items: center;
            font-size: 1.1rem;
            animation: fadeInDown 0.5s;
        }
        .popup-success-message {
            flex: 1;
        }
        .popup-success-close {
            background: none;
            border: none;
            color: #fff;
            font-size: 1.5rem;
            margin-left: 1.2rem;
            cursor: pointer;
            font-weight: bold;
            transition: color 0.2s;
        }
        .popup-success-close:hover {
            color: #d4ffe6;
        }
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
