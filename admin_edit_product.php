<?php
include 'db_connection.php';

$product_id = $_GET['id'] ?? null;

if ($product_id) {
    // Fetch the current product details
    $sql = "SELECT * FROM products WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $product_id);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "Product not found.";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $starting_bid = $_POST['starting_bid'];
    $bid_end_time = $_POST['bid_end_time'];
    $status = $_POST['status'];
    $image_url = $_POST['image_url'];

    $update_sql = "UPDATE products SET product_name = :product_name, description = :description, starting_bid = :starting_bid, bid_end_time = :bid_end_time, status = :status, image_url = :image_url WHERE id = :id";
    $update_stmt = $pdo->prepare($update_sql);
    $update_stmt->bindParam(':product_name', $product_name);
    $update_stmt->bindParam(':description', $description);
    $update_stmt->bindParam(':starting_bid', $starting_bid);
    $update_stmt->bindParam(':bid_end_time', $bid_end_time);
    $update_stmt->bindParam(':status', $status);
    $update_stmt->bindParam(':image_url', $image_url);
    $update_stmt->bindParam(':id', $product_id);

    if ($update_stmt->execute()) {
        echo '<script>alert("Product updated successfully!"); window.location.href = "admin_bid.php";</script>';
        exit;
    } else {
        echo "Error updating product.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | Edit Product</title>
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
            padding-top: 20px;
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
    .header {
        /* display: none; */
    }
  thead {
      font-size: 8px;
  }
  tbody {
      font-size: 5px;
  }
}
a i {
            color: #2980b9;
            margin: 0 5px;
        }
        a:hover i {
            color: #e74c3c;
        }


        /* .header {
            background-color: #2980b9;
            color: white;
            padding: 10px;
            border-radius: 5px;
        } */
        h2 {
            color: #2980b9;
        }
        
        
        form {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"],
        input[type="datetime-local"],
        select,
        textarea {
            width: 30%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            
        }
        input[type="submit"] {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 4px;
        }
        input[type="submit"]:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <h2><span >Blue </span><span>Market</span></h2>
        <a href="admin_dashboard.php"><i class="fa fa-home"></i> Home</a>
        <a href="admin_add_product.php"><i class="fa fa-tag"></i> Product</a>
        <a href="admin_bid.php"><i class="fa fa-gavel"></i> Bid</a>
        <a href="index.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>

    <div class="navbar" id="navbar">
        <a href="admin_dashboard.php"><i class="fa fa-home"></i> Home</a>
        <a href="admin_add_product.php"><i class="fa fa-tag"></i> Product</a>
        <a href="admin_bid.php"><i class="fa fa-gavel"></i> Bid</a>
    </div>

    <div class="content">
        <div class="header">
            <h2>Edit Product</h2>
        </div>

        <form action="admin_edit_product.php?id=<?php echo $product_id; ?>" method="POST">
            <label for="product_name">Product Name:</label>
            <input type="text" name="product_name" id="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
<br>
            <label for="description">Description:</label>
            <textarea name="description" id="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>

            <label for="starting_bid">Starting Bid:</label>
            <input type="number" step="0.01" name="starting_bid" id="starting_bid" value="<?php echo htmlspecialchars($product['starting_bid']); ?>" required>

            <label for="bid_end_time">Bid End Time:</label>
            <input type="datetime-local" name="bid_end_time" id="bid_end_time" value="<?php echo date('Y-m-d\TH:i', strtotime($product['bid_end_time'])); ?>" required>

            <label for="status">Status:</label>
            <select name="status" id="status" required>
                <option value="active" <?php echo $product['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                <option value="expired" <?php echo $product['status'] === 'expired' ? 'selected' : ''; ?>>Expired</option>
            </select>

            <label for="image_url">Image URL:</label>
            <input type="file" name="image_url" id="image_url" value="<?php echo htmlspecialchars($product['image_url']); ?>">

            <input type="submit" value="Update Product">
        </form>
    </div>
</body>
</html>
