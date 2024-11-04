<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

include 'db_connection.php'; 
$stmt = $pdo->query("SELECT COUNT(*) AS total FROM products");
$totalProducts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $pdo->query("SELECT COUNT(*) AS total FROM bids");
$totalBids = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $pdo->query("SELECT COUNT(*) AS total FROM users");
$totalActiveUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $pdo->query("SELECT SUM(bid_amount) AS total_bid_amount FROM bids");
$totalBidAmount = $stmt->fetch(PDO::FETCH_ASSOC)['total_bid_amount'];

$totalBidAmount = $totalBidAmount ? $totalBidAmount : 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | Dashboard</title>
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
        .header > p {
            color: white;
        }
        .content {
            margin-left: 220px;
            padding: 20px;
        }
        .stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
            gap: 10px;
        }
        .stat-box {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            width: 25%;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .stat-box i {
            font-size: 24px;
            color: #2980b9;
            margin-bottom: 10px;
        }
        .header {
            background-color: #2980b9;
            color: white;
            padding: 10px;
            border-radius: 5px;
        }
        
        @media (max-width: 768px) {
            .navbar {
                margin: 0;
            }
            .content {
                margin: 0;
            }
            .stats {
                flex-direction: column;
                align-items: center;
            }
            .stat-box {
                width: 80%;
                margin-bottom: 10px;
            }
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
            <p>Dashboard</p>
        </div>
        <br>
        <!-- Stats -->
        <div class="stats">
            <div class="stat-box">
                <i class="fa fa-box"></i>
                <h3>Total Products</h3>
                <p><?php echo $totalProducts; ?></p>
            </div>
            <div class="stat-box">
                <i class="fa fa-gavel"></i>
                <h3>Total Bids</h3>
                <p><?php echo $totalBids; ?></p>
            </div>
            <div class="stat-box">
                <i class="fa fa-money-bill"></i>
                <h3>Total Bid Amount</h3>
                <p>₱<?php echo number_format($totalBidAmount, 2); ?></p>
            </div>
            <div class="stat-box">
                <i class="fa fa-users"></i>
                <h3>Total Users</h3>
                <p><?php echo $totalActiveUsers; ?></p>
            </div>
        </div>
        
    </div>
</body>
</html>
