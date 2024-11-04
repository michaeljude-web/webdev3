<?php

include 'db_connection.php';


$email = '';
$password = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

   
    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];

       
        header("Location: users_dashboard.php");
        exit;
    } else {
        $error_message = 'Invalid email or password!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600' rel='stylesheet' type='text/css'>
    <link href="//netdna.bootstrapcdn.com/font-awesome/3.1.1/css/font-awesome.css" rel="stylesheet">
    <style>
        body {
            background-color: #d3d3d3; 
            font-family: 'Open Sans', sans-serif;
            color: #4c4c4c;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            width: 350px; 
            padding: 20px;
            background-color: #ebebeb; 
            border-radius: 8px; 
            box-shadow: 1px 2px 5px rgba(0,0,0,.31); 
            border: solid 1px #cbc9c9;
        }

        input[type=text], input[type=password] {
            width: 96.5%; 
            height: 39px; 
            border-radius: 4px; 
            background-color: #fff; 
            border: solid 1px #cbc9c9;
            margin-bottom: 20px; 
            padding-left: 10px;
        }

        label {
            display: inline-block;
            margin-bottom: 5px;
        }

        .button {
            display: block;
            text-align: center;
            padding: 10px;
            background-color: #3a57af; 
            color: white;
            width: 100%;
            border-radius: 5px; 
            text-decoration: none; 
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #2e458b;
        }

        .signup {
            text-align: center;
            margin-top: 10px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">Blue Market
        <form method="POST" action="">
            <h2>Login</h2>
            <?php if ($error_message): ?>
                <div class="error-message"><?= $error_message ?></div>
            <?php endif; ?>
            <label for="email"><i class="icon-envelope"></i> Email:</label>
            <input type="text" name="email" id="email" placeholder="Email" required />
            <label for="password"><i class="icon-shield"></i> Password:</label>
            <input type="password" name="password" id="password" placeholder="Password" required />
            <button type="submit" class="button">Login</button>
        </form>
        <div class="signup">
            <p>Don't have an account? <a href="users_signup.php">Sign up</a></p>
        </div>
    </div>
</body>
</html>
