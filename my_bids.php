<?php
include 'db_connection.php'; 

$user_id = 1; // Assuming the user ID is known

// Cancel bid
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_bid'])) {
    $bid_id = $_POST['bid_id'];
    
    $cancel_bid_sql = "DELETE FROM bids WHERE id = :bid_id AND user_id = :user_id";
    $cancel_bid_stmt = $pdo->prepare($cancel_bid_sql);
    $cancel_bid_stmt->bindParam(':bid_id', $bid_id);
    $cancel_bid_stmt->bindParam(':user_id', $user_id);
    $cancel_bid_stmt->execute();
    
    echo '<script>alert("Bid canceled successfully!")</script>';
}

$myBidsSql = "SELECT b.*, p.product_name, p.image_url FROM bids b JOIN products p ON b.product_id = p.id WHERE b.user_id = :user_id";
$myBidsStmt = $pdo->prepare($myBidsSql);
$myBidsStmt->bindParam(':user_id', $user_id);
$myBidsStmt->execute();
$myBids = $myBidsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blue Market - My Bids</title>
    <link rel="stylesheet" href="assets/css/users_dashboard.css">
</head>
<body>
<nav class="navbar">
    <h1><a href="users_dashboard.php">Blue Market</a></h1>
    <ul>
        <li><a href="users_dashboard.php">Dashboard</a></li>
        <li><a href="users_login.php">Logout</a></li>
    </ul>
</nav>

<section id="my-bids">
    <h2>My Bids</h2>
    <div class="product-grid" id="myBidGrid">
        <?php if ($myBids): ?>
            <?php foreach ($myBids as $bid): ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($bid['image_url']); ?>" alt="<?php echo htmlspecialchars($bid['product_name']); ?>">
                    <h3><?php echo htmlspecialchars($bid['product_name']); ?></h3>
                    <p>Your Bid Amount: ₱<?php echo number_format($bid['bid_amount'], 2); ?></p>
                    <form action="my_bids.php" method="POST">
                        <input type="hidden" name="bid_id" value="<?php echo $bid['id']; ?>">
                        <input type="submit" name="cancel_bid" value="Cancel Bid">
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No bids placed.</p>
        <?php endif; ?>
    </div>
</section>
</body>
</html>
