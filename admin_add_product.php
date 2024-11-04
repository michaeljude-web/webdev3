<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | Product</title>
    <link rel="stylesheet" href="assets/font/css/all.min.css">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="assets/css/admin_dashboard.css">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        .sidebar {
            width: 200px;
            background-color: #2c3e50;
            height: 100vh;
            position: fixed;
        }
        .sidebar h2 {
            color: #ffffff;
            text-align: center;
        }
        .sidebar a {
            padding: 15px;
            text-decoration: none;
            color: #ffffff;
            display: block;
        }
        .sidebar a:hover {
            background-color: #1abc9c;
        }

        .navbar {
            margin-left: 200px;
            padding: 10px;
            background-color: #34495e;
        }
        .navbar a {
            color: #ffffff;
            margin: 0 15px;
            text-decoration: none;
        }
        .content {
            margin-left: 220px;
            padding: 20px;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .navbar {
                display: flex;
                justify-content: center;
                gap: 20px;
                margin: 0;
            }
            .content {
                margin-left: 0;
            }
        }
        a i {
            color: #2980b9;
            margin: 0 5px;
        }
        a:hover i {
            color: #e74c3c;
        }

        .header {
         
            color: white;
            
            border-radius: 5px;
        }
        h2 {
            color: #2980b9;
        }

        /* Form Styles */
        form {
            margin-top: 20px;
        }
        .form-group {
            display: flex;
            margin-bottom: 15px;
            align-items: center;
        }
        .form-group label {
            flex: 1;
            font-weight: bold;
            margin-right: 10px;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            flex: 2;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group input[type="file"] {
            padding: 3px;
        }
        input[type="submit"] {
            background-color: #2980b9;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
        }
        input[type="submit"]:hover {
            background-color: #1abc9c;
        }
    </style>
</head>
<body>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <br>
        <h2><span style="color: #ffff;">Blue </span><span>Market</span></h2>
        <a href="admin_dashboard.php"><i class="fa fa-home"></i> Home</a>
        <a href="admin_add_product.php"><i class="fa fa-tag"></i> Product</a>
        <a href="admin_bid.php"><i class="fa fa-gavel"></i> Bid</a>
        <a href="logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
    
    <!-- Navbar -->
    <div class="navbar" id="navbar">
        <a href="admin_dashboard.php"><i class="fa fa-home"></i> Home</a>
        <a href="admin_add_product.php"><i class="fa fa-tag"></i> Product</a>
        <a href="admin_bid.php"><i class="fa fa-gavel"></i> Bid</a>
    </div>
    
    <!-- Content -->
    <div class="content">
        <div class="header">
            <p>Add Product</p>
            <span id="dateError" style="color: red; display: none;">Please select valid date and time.</span>
        </div>

        <?php
include 'db_connection.php';
// try {
//     $sql = "UPDATE products SET status = 'expired' WHERE status = 'active' AND bid_end_time < NOW()";
//     $stmt = $pdo->prepare($sql);
//     $stmt->execute();
// } catch (PDOException $e) {
//     echo "Error updating products: " . $e->getMessage();
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $starting_bid = $_POST['starting_bid'];
    $bid_end_time = $_POST['bid_end_time'];
    $seller_id = 1;


    $status = 'active';

    $image_url = null;
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
        }
    }

    $sql = "INSERT INTO products (product_name, description, starting_bid, bid_end_time, status, image_url, seller_id)
            VALUES (:product_name, :description, :starting_bid, :bid_end_time, :status, :image_url, :seller_id)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':product_name', $product_name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':starting_bid', $starting_bid);
    $stmt->bindParam(':bid_end_time', $bid_end_time);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':image_url', $image_url);
    $stmt->bindParam(':seller_id', $seller_id);

    if ($stmt->execute()) {
        echo '<script>alert("Product added successfully!")</script>';
    } else {
        echo '<script>alert("Failed to add Product")</script>';
    }
}
?>


        <form id="addProductForm" action="admin_add_product.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="product_name">Product Name:</label>
                <input type="text" id="product_name" name="product_name" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label for="starting_bid">Starting Bid:</label>
                <input type="number" step="0.01" id="starting_bid" name="starting_bid" required>
            </div>

            <div class="form-group">
    <label for="bid_end_time">Bid End Time:</label>
    <input type="datetime-local" id="bid_end_time" name="bid_end_time" required oninput="validateDate()">
    
</div><span id="dateError" style="color: red; display: none;">Please select a date and time in the future.</span>

            <!-- <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="pending">Pending</option>
                    <option value="active">Active</option>
                    <option value="sold">Sold</option>
                </select>
            </div> -->

            <div class="form-group">
                <label for="image">Product Image:</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>

            <div class="form-group">
                <input type="submit" value="Add Product">
            </div>
        </form>
    </div>
    <script>

    function validateDate() {
    const input = document.getElementById('bid_end_time');
    const errorMessage = document.getElementById('dateError');
    const now = new Date();

    // Set sa minimum date 
    const minDate = new Date(now.getTime() - (now.getTimezoneOffset() * 60000)).toISOString().slice(0, 16);
    
    if (input.value < minDate) {
        errorMessage.style.display = 'inline';
        input.setCustomValidity('Invalid date'); 
    } else {
        errorMessage.style.display = 'none';
        input.setCustomValidity(''); 
    }
}
    </script>
</body>
</html>
