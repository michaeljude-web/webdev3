<?php
session_start();
include 'db_connection.php'; 

error_reporting(E_ALL);
ini_set('display_errors', 1);

$error = '';
$showTwoFaForm = false;

$max_attempts = 5; 
$lockout_duration = 1 * 60; 

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['twofa_code'])) {
        $twofa_code = $_POST['twofa_code'];
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = :username");
        $stmt->bindParam(':username', $_SESSION['username']);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && verifyTwoFactorCode($admin['twofa_code'], $twofa_code)) {
            $_SESSION['admin'] = $admin['username'];
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $error = "Invalid 2FA code.";
        }
    } else {
        // Check if the account is locked
        if (isset($_SESSION['lockout_time']) && time() < $_SESSION['lockout_time']) {
            $remaining = ceil(($_SESSION['lockout_time'] - time()) / 60);
            $error = "Account is locked. Try again after $remaining minute(s).";
        } else {
            // Reset lockout
            unset($_SESSION['lockout_time']);

            $username = $_POST['username'];
            $password = $_POST['password'];

            $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin) {
                if ($password === $admin['password']) { // Direct comparison
                    $_SESSION['username'] = $username;
                    $showTwoFaForm = true; 
                    $_SESSION['login_attempts'] = 0; // Reset attempts on successful login
                } else {
                    $_SESSION['login_attempts']++;
                    if ($_SESSION['login_attempts'] >= $max_attempts) {
                        $_SESSION['lockout_time'] = time() + $lockout_duration; // Lockout for set duration
                        $error = "Account is locked due to too many invalid attempts. Try again in 15 minutes.";
                    } else {
                        $error = "Invalid password. Attempts: " . $_SESSION['login_attempts'];
                    }
                }
            } else {
                $_SESSION['login_attempts']++;
                if ($_SESSION['login_attempts'] >= $max_attempts) {
                    $_SESSION['lockout_time'] = time() + $lockout_duration; // Lockout for set duration
                    $error = "Account is locked due to too many invalid attempts. Try again in 15 minutes.";
                } else {
                    $error = "Invalid username. Attempts: " . $_SESSION['login_attempts'];
                }
            }
        }
    }
}

function verifyTwoFactorCode($secret, $code) {
    // Placeholder for actual 2FA verification logic
    return substr($secret, -6) === $code; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BlueMarket | Login</title>
    <link href="//netdna.bootstrapcdn.com/font-awesome/3.1.1/css/font-awesome.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/index.css">
    <style>
        .twofa-container {
            display: <?php echo $showTwoFaForm ? 'block' : 'none'; ?>;
            margin-top: 20px;
        }
        .error-message {
            color: red;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Admin Login</h1>
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!isset($_SESSION['lockout_time']) || time() >= $_SESSION['lockout_time']): ?>
            <form method="POST" action="">
                <div class="input-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="login-button">Submit</button>
            </form>
        <?php else: ?>
            <p>Your account is locked. Please try again later.</p>
        <?php endif; ?>

        <div class="twofa-container">
            <h2>Enter 2FA Code</h2>
            <form method="POST" action="">
                <div class="input-group">
                    <label for="twofa_code">Two-Factor Authentication Code:</label>
                    <input type="text" id="twofa_code" name="twofa_code" required>
                </div>
                <button type="submit" class="login-button">Verify Code</button>
            </form>
        </div>
    </div>
</body>
</html>
