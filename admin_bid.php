<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | Bid</title>
    <link rel="stylesheet" href="assets/font/css/all.min.css">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="assets/css/admin_dashboard.css">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            margin: 0;
            padding: 0;
            /* background-color: #f4f4f4; */
            background: #E7EAE5;
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
            background: #E7EAE5;
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


        .header {
            background-color: #2980b9;
            color: white;
            padding: 10px;
            border-radius: 5px;
        }
        h2 {
            color: #2980b9;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            text-align: center;
            
        }
        table, th, td {
            border: 1px solid #000;
            background-color:#f2f3f4;
        }
        /* table th:nth-last-child(2), */
table td:nth-last-child(2) {
    /* background-color: #c39fc3;  */
    color: green; 
   
}

}

        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #2980b9;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        a i {
            color: #2980b9;
            margin: 0 5px;
        }
        a:hover i {
            color: #e74c3c;
        }

        .pagination {
    display: flex;
    justify-content: center;
    margin: 20px 0;
}

.pagination a {
    padding: 10px 15px;
    margin: 0 5px;
    border: 1px solid #2980b9;
    border-radius: 5px;
    text-decoration: none;
    color: #2980b9;
    transition: background-color 0.3s, color 0.3s;
}

.pagination a:hover {
    background-color: #2980b9;
    color: white;
}

.pagination a.active {
    background-color: #2980b9;
    color: white;
    border: 1px solid #2980b9;
}

    </style>
</head>
<body>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <br>
        <h2><span style="color: #ffff; ">Blue Market</span></h2>
        <a href="admin_dashboard.php"><i class="fa fa-home"></i> Home</a>
        <a href="admin_add_product.php"><i class="fa fa-tag"></i> Product</a>
        <a href="admin_bid.php"><i class="fa fa-gavel"></i> Bid</a>
        
        <a href="index.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
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
            <p>Product Status</p>
            <form action="" method="GET" style="display: inline-block; float: right;">
                <input type="text" name="search" placeholder="Search Product" style="padding: 5px;">
                <button type="submit" style="padding: 5px;"><i class="fas fa-search"></i></button>
                <!-- <button type="submit" style="padding: 5px;"><i class="fa fa-arrow-left"></i></button> -->
            </form>
        </div>
        
        <section id="products">
            <h2>Product Status</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Amount</th>
                        <th>Email</th>
                        <th>Win</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php    
            include 'db_connection.php';

            $itemsPerPage = 6; 
            $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($currentPage - 1) * $itemsPerPage;

            $search = $_GET['search'] ?? '';
            $searchTerm = '%' . $search . '%';

            // Count expired products
            $countSql = "SELECT COUNT(*) FROM products WHERE bid_end_time <= NOW() AND product_name LIKE :search"; // Updated query for expired products
            $countStmt = $pdo->prepare($countSql);
            $countStmt->bindParam(':search', $searchTerm);
            $countStmt->execute();
            $totalItems = $countStmt->fetchColumn();
            $totalPages = ceil($totalItems / $itemsPerPage);

            // Get expired products
            $sql = "
                SELECT p.*, 
                       (SELECT CONCAT(u.firstname, ' ', u.lastname) 
                        FROM bids b 
                        JOIN users u ON b.user_id = u.id 
                        WHERE b.product_id = p.id 
                        ORDER BY b.bid_amount DESC 
                        LIMIT 1) AS highest_bidder,
                       (SELECT u.email 
                        FROM bids b 
                        JOIN users u ON b.user_id = u.id 
                        WHERE b.product_id = p.id 
                        ORDER BY b.bid_amount DESC 
                        LIMIT 1) AS winner_email,
                       (SELECT MAX(b.bid_amount) 
                        FROM bids b 
                        WHERE b.product_id = p.id) AS highest_bid
                FROM products p
                WHERE p.bid_end_time <= NOW() AND p.product_name LIKE :search
                LIMIT :offset, :itemsPerPage
            "; // Updated query for expired products

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':search', $searchTerm);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($products): 
                foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($product['description']); ?></td>
                        <td>Expired</td> <!-- Hardcoded status -->
                        <td>
                            $<?php echo number_format($product['highest_bid'], 2) ?: 'N/A'; ?> <!-- Show highest bid -->
                        </td>
                        <td>
                            <?php echo htmlspecialchars($product['winner_email']) ?: 'N/A'; ?> <!-- Show winner email -->
                        </td>
                        <td>
                            <?php echo htmlspecialchars($product['highest_bidder']) ?: 'N/A'; ?> <!-- Show highest bidder -->
                        </td>
                        <td>
                            <a href="admin_edit_product.php?id=<?php echo $product['id']; ?>" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="admin_delete_product.php?id=<?php echo $product['id']; ?>" title="Delete" onclick="return confirm('Are you sure you want to delete this product?');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; 
            else: ?>
                <tr>
                    <td colspan="7">No expired products found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>

        <div class="pagination">
            <?php if ($currentPage > 1): ?>
                <a href="?page=<?php echo $currentPage - 1; ?>&search=<?php echo urlencode($search); ?>">Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" <?php if ($i == $currentPage) echo 'class="active"'; ?>>
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
                <a href="?page=<?php echo $currentPage + 1; ?>&search=<?php echo urlencode($search); ?>">Next</a>
            <?php endif; ?>
        </div>

        </tbody>
        </table>
    </section>
</div>

    
    <script>
        
    </script>
</body>
</html>
