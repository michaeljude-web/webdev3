<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $twofa_code = $_POST['twofa_code'];
    
    $stmt = $pdo->prepare("UPDATE admin SET twofa_code = :twofa_code WHERE username = :username");
    $stmt->bindParam(':twofa_code', $twofa_code);
    $stmt->bindParam(':username', $_SESSION['admin']);
    
    if ($stmt->execute()) {
        $success = "2FA code updated successfully!";
    } else {
        $error = "Failed to update 2FA code.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set 2FA Code</title>
    <style>
        body {
            font-family: "Courier New", Courier, monospace;
            background-color: #e0e0e0;
            color: #000;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 400px;
            padding: 20px;
            background-color: #f0f8ff;
            border: 2px solid #008080;
            box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            text-align: center;
        }
        h1 {
            font-size: 22px;
            color: #008080;
            margin-bottom: 20px;
            text-shadow: 1px 1px 0 #fff;
        }
        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #008080;
        }
        input[type="text"] {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #008080;
            border-radius: 5px;
            font-size: 14px;
            background-color: #fff;
            box-shadow: inset 1px 1px 2px rgba(0, 0, 0, 0.3);
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #008080;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #006666;
        }
        .error-message {
            color: red;
            font-weight: bold;
        }
        .success-message {
            color: green;
            font-weight: bold;
        }
    </style>
    <script>
        function handleSuccess() {
            alert("2FA code updated successfully!");
            // history.replaceState(null, '', '');
            window.location.href = "https://www.google.com";
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Set 2FA Code</h1>
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
            <script>
                handleSuccess();
            </script>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="input-group">
                <label for="twofa_code">Two-Factor Authentication Code:</label>
                <input type="text" id="twofa_code" name="twofa_code" required>
            </div>
            <button type="submit">Set 2FA Code</button>
        </form>
    </div>
</body>
</html>
