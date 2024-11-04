<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 


    $checkEmailSql = "SELECT * FROM users WHERE email = :email";
    $checkEmailStmt = $pdo->prepare($checkEmailSql);
    $checkEmailStmt->bindParam(':email', $email);
    $checkEmailStmt->execute();

    if ($checkEmailStmt->rowCount() > 0) {
        echo '<script>alert("Email already exists!")</script>';
    } else {
        $sql = "INSERT INTO users (firstname, lastname, gender, age, address, email, password) 
                VALUES (:firstname, :lastname, :gender, :age, :address, :email, :password)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':firstname', $firstname);
        $stmt->bindParam(':lastname', $lastname);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':age', $age);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);

        if ($stmt->execute()) {
            echo '<script>alert("New record created successfully!")</script>';
        } else {
            echo '<script>alert("Error: Try again!")</script>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Page</title>
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

        .row {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        input[type=text], input[type=password], input[type=number], input[type=email], textarea {
            width: 90%; 
            height: 39px; 
            border-radius: 4px; 
            background-color: #fff; 
            border: solid 1px #cbc9c9;
            padding-left: 10px;
        }

        .half-input {
            flex: 1;
        }

        label {
            display: inline-block;
            margin-bottom: 5px;
        }

        .gender-age-container {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .gender-container {
            flex: 1;
        }

        input[type=radio] {
            margin-right: 10px;
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

        .login {
            text-align: center;
            margin-top: 10px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        textarea {
            resize: none;
            height: 60px;
        }

        .row, .gender-container, textarea {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
       
<form method="POST" action="">
    <h2>Sign Up</h2>

  
    <div class="row">
        <div class="half-input">
            <label for="firstname"><i class="icon-user"></i> First Name:</label>
            <input type="text" name="firstname" id="firstname" placeholder="First Name" required />
        </div>
        <div class="half-input">
            <label for="lastname"><i class="icon-user"></i> Last Name:</label>
            <input type="text" name="lastname" id="lastname" placeholder="Last Name" required />
        </div>
    </div>

    <div class="gender-age-container">
        <div class="gender-container">
            <label><i class="icon-heart"></i> Gender:</label><br>
            <input type="radio" id="male" name="gender" value="male" required>
            <label for="male">Male</label>
            <input type="radio" id="female" name="gender" value="female" required>
            <label for="female">Female</label>
        </div> 
        <div class="half-input">
            <label for="age"><i class="icon-calendar"></i> Age:</label>
            <input type="number" name="age" id="age" placeholder="Age" required />
        </div>
    </div>

    <label for="address"><i class="icon-home"></i> Address:</label>
    <textarea name="address" id="address" placeholder="Your Address" required></textarea>

    <div class="row">
        <div class="half-input">
            <label for="email"><i class="icon-envelope"></i> Email:</label>
            <input type="email" name="email" id="email" placeholder="Email" required />
        </div>
        <div class="half-input">
            <label for="password"><i class="icon-shield"></i> Password:</label>
            <input type="password" name="password" id="password" placeholder="Password" required />
        </div>
    </div>

    <button type="submit" class="button">Sign Up</button>
</form>
        <div class="login">
            <p>Already have an account? <a href="users_login.php">Login</a></p>
        </div>
    </div>
</body>
</html>