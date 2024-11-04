<?php
include 'db_connection.php'; 

$user_id = 1; // Assuming the user ID is known, replace with session user ID in a real app

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['bid'])) {
        // Place bid logic
        $product_id = $_POST['product_id'];
        $bid_amount = $_POST['bid_amount'];

        $bid_sql = "INSERT INTO bids (product_id, user_id, bid_amount) VALUES (:product_id, :user_id, :bid_amount)";
        $bid_stmt = $pdo->prepare($bid_sql);
        $bid_stmt->bindParam(':product_id', $product_id);
        $bid_stmt->bindParam(':user_id', $user_id);
        $bid_stmt->bindParam(':bid_amount', $bid_amount);
        
        if ($bid_stmt->execute()) {
            // Update product status to 'bidded'
            $update_sql = "UPDATE products SET status = 'bidded' WHERE id = :product_id";
            $update_stmt = $pdo->prepare($update_sql);
            $update_stmt->bindParam(':product_id', $product_id);
            $update_stmt->execute();

            echo '<script>alert("Bid placed successfully!")</script>';
        } else {
            echo '<script>alert("Failed to place bid.")</script>';
        }
    }

    if (isset($_POST['cancel_bid'])) {
        // Cancel bid logic
        $product_id = $_POST['product_id'];

        // Delete the bid
        $cancel_bid_sql = "DELETE FROM bids WHERE product_id = :product_id AND user_id = :user_id";
        $cancel_bid_stmt = $pdo->prepare($cancel_bid_sql);
        $cancel_bid_stmt->bindParam(':product_id', $product_id);
        $cancel_bid_stmt->bindParam(':user_id', $user_id);
        $cancel_bid_stmt->execute();

        // Update product status back to 'active'
        $restore_sql = "UPDATE products SET status = 'active' WHERE id = :product_id";
        $restore_stmt = $pdo->prepare($restore_sql);
        $restore_stmt->bindParam(':product_id', $product_id);
        $restore_stmt->execute();

        echo '<script>alert("Bid canceled successfully!")</script>';
    }
}

// Existing code for fetching active products
$searchTerm = '';
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
}

$sql = "SELECT * FROM products WHERE status = 'active' AND product_name LIKE :searchTerm";
$stmt = $pdo->prepare($sql);
$searchTerm = '%' . $searchTerm . '%'; 
$stmt->bindParam(':searchTerm', $searchTerm);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Existing code for fetching expired products
$expiredSql = "
    SELECT p.*, 
           (SELECT CONCAT(u.firstname, ' ', u.lastname) 
            FROM bids b 
            JOIN users u ON b.user_id = u.id 
            WHERE b.product_id = p.id 
            ORDER BY b.bid_amount DESC 
            LIMIT 1) AS highest_bidder,
           (SELECT MAX(b.bid_amount) 
            FROM bids b 
            WHERE b.product_id = p.id) AS highest_bid
    FROM products p
    WHERE p.bid_end_time <= NOW()
";
$expiredStmt = $pdo->prepare($expiredSql);
$expiredStmt->execute();
$expiredProducts = $expiredStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user's existing bids
$myBidsSql = "SELECT p.*, b.bid_amount 
               FROM bids b 
               JOIN products p ON b.product_id = p.id 
               WHERE b.user_id = :user_id";
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
    <title>Blue Market Online Bidding</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600' rel='stylesheet' type='text/css'>
  <style>
     body {
    font-family: 'Papyrus', sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4; /* Light background */
}

.navbar {
    background-color: #2c3e50; /* Darker navy blue */
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.navbar ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    align-items: center;
}

.navbar li {
    display: inline-block;
    margin: 0 15px;
}

.navbar a {
    color: white;
    text-decoration: none;
    transition: color 0.3s;
}

.navbar a:hover {
    color: #ecf0f1; /* Light gray on hover */
}

.search-bar {
    margin-right: 20px;
    display: flex;
    align-items: center;
}

.search-bar input {
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-right: 5px;
}

.search-bar button {
    border: none;
    background: #27ae60; /* Green */
    color: white;
    padding: 8px 12px;
    border-radius: 5px;
    cursor: pointer;
}


section {
    padding: 20px;
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 20px;
}

.product-card {
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 10px;
    background: white;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s, box-shadow 0.2s;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.product-card img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
}

.product-card h3 {
    font-size: 1rem; /* Adjusted font size */
    margin: 0.5rem 0;
    color: #333;
}

.product-card p {
    padding: 0 0.5rem;
    margin: 0.5rem 0;
    color: #666;
    font-size: 0.9rem; /* Smaller font size for text */
}

.starting-bid {
    font-weight: bold;
    color: #2980b9; /* Professional blue */
}

/* Bid Form Styles */
.product-card form {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0.5rem 0;
}

.product-card label {
    margin-bottom: 0.5rem;
}

.product-card input[type="number"] {
    padding: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 4px;
    margin-bottom: 0.5rem;
    width: 100%;
}

.product-card input[type="submit"] {
    background-color: #27ae60; /* Green for professionalism */
    color: white;
    border: none;
    width: 100%;
    padding: 0.5rem;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.product-card input[type="submit"]:hover {
    background-color: #219653; /* Darker green */
}

/* Footer */
footer {
    text-align: center;
    padding: 20px;
    background-color: #f8f8f8;
    border-top: 1px solid #ddd;
}

/* Media Queries */
@media (max-width: 1200px) {
    .product-grid {
        grid-template-columns: repeat(5, 1fr);
    }
}

@media (max-width: 992px) {
    .product-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

@media (max-width: 768px) {
    .product-grid {
        grid-template-columns: repeat(3, 1fr);
    }

    .navbar >h1>a {
        display: none;
    }
}

@media (max-width: 600px) {
    /* .navbar {
        flex-direction: column;
        align-items: flex-start;
    }

    .navbar li {
        display: block;
        margin: 5px 0;
    } */

    .product-card {
        flex: 1 1 100%; /* Full width on smaller screens */
    }
}

/* Profile */
.profile-container {
    position: relative;
}

/* .profile-icon {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background-color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #4a90e2; 
    cursor: pointer;
    position: relative;
} */

.profile-icon i {
    font-size: 20px;
}

.profile-dropdown {
    display: none;
    position: absolute;
    top: 20px;
    right: 0;
    background-color: white;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 5px;
}

.profile-dropdown a {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    text-decoration: none;
    color: #333;
    transition: background-color 0.3s;
}

.profile-dropdown a:hover {
    background-color: #f0f0f0;
}

.profile-dropdown i {
    margin-right: 10px; 
}

.profile-container:hover .profile-dropdown {
    display: block;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
}

.modal-content {
    background-color: white;
    padding: 20px;
    border-radius: 10px;
    width: 400px;
}

.modal-content input {
    width: 94%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.modal-content button {
    padding: 10px 15px;
    background-color: #4a90e2; /* Match navbar */
    color: white;
    width: 100%;
    border: none;
    cursor: pointer;
    border-radius: 5px;
}

.close-modal {
    float: right;
    cursor: pointer;
    font-size: 20px;
}

#products {
    padding: 2rem;
    background-color: #f9f9f9;
}

#products h2 {
    text-align: center;
    color: #333;
    margin-bottom: 1.5rem;
}





.notification-dropdown {
    display: none; /* Hidden by default */
    position: absolute;
    background-color: white;
    border: 1px solid #ddd;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    padding: 10px;
    z-index: 1;
    width: 200px; /* Adjust width as necessary */
}

.notification-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 5px 0;
}

.notification-item button {
    background: none;
    border: none;
    color: red;
    cursor: pointer;
    font-size: 0.8rem;
}


  </style>
</head>
<body>
<nav class="navbar">
    <h1><a href="users_dashboard.php">Blue Market</a></h1>
    <div class="search-bar">
        <form action="users_dashboard.php" method="GET">
            <input type="text" name="search" placeholder="Search products..." required>
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <ul>
        <li><a href="#home">Home</a></li>
        <li><a href="#products">About</a></li>
        <li><a href="#contact">Contact</a></li>
        <li class="notification">
    <div class="notification-icon" id="notificationIcon">
        <i class="fas fa-bell"></i>
        <span class="notification-dot" id="notificationDot"></span>
    </div>
    <div class="notification-dropdown" id="notificationDropdown">
        <div id="notificationList">
            <!-- Notifications will be dynamically added here -->
        </div>
    </div>
</li>

        <li>
            <div class="profile-container">
                <div class="profile-icon" id="profileIcon">
                    <i class="fa fa-user"></i>
                </div>
                <div class="profile-dropdown">
                    <a id="editProfileBtn"><i class="fas fa-edit"></i>Edit Profile</a>
                    <a href="users_login.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
                </div>
            </div>
        </li>
    </ul>
</nav>

<section id="home">
    <h2>Welcome to Blue Market</h2>
    <p>Online Bidding</p>
</section>

<section id="products">
    <h2>Active Products</h2>
    <div class="product-grid" id="productGrid">
        <?php if ($products): ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                    <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                    <p class="starting-bid">Starting Bid: ₱<?php echo number_format($product['starting_bid'], 2); ?></p>
                    <p class="bid-end-time">Bid End Time: <?php echo htmlspecialchars($product['bid_end_time']); ?></p>
                    
                    <form action="users_dashboard.php" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <label for="bid_amount">Your Bid:</label>
                        <input type="number" step="0.01" name="bid_amount" id="bid_amount_<?php echo $product['id']; ?>" required>
                        <input type="submit" name="bid" value="Place Bid">
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No active products available.</p>
        <?php endif; ?>
    </div>
</section>

<section id="expired-products">
    <h2>Expired Products</h2>
    <div class="product-grid" id="expiredProductGrid">
        <?php if ($expiredProducts): ?>
            <?php foreach ($expiredProducts as $expiredProduct): ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($expiredProduct['image_url']); ?>" alt="<?php echo htmlspecialchars($expiredProduct['product_name']); ?>">
                    <h3><?php echo htmlspecialchars($expiredProduct['product_name']); ?></h3>
                    <p><?php echo htmlspecialchars($expiredProduct['description']); ?></p>
                    <p class="starting-bid">Starting Bid: ₱<?php echo number_format($expiredProduct['starting_bid'], 2); ?></p>
                    <p class="bid-end-time">Bid End Time: <?php echo htmlspecialchars($expiredProduct['bid_end_time']); ?></p>
                    <p class="status">Status: Expired</p>
                    <?php if ($expiredProduct['highest_bidder']): ?>
                        <p class="winner"><i class="fas fa-crown"></i> <?php echo htmlspecialchars($expiredProduct['highest_bidder']); ?> <i class="fas fa-crown"></i></p>
                        <p class="winning-bid">Amount: ₱<?php echo number_format($expiredProduct['highest_bid'], 2); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No expired products available.</p>
        <?php endif; ?>
    </div>
</section>

<section id="my-bids">
    <h2>Your Bids</h2>
    <div class="product-grid" id="myBidGrid">
        <?php if ($myBids): ?>
            <?php foreach ($myBids as $myBid): ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($myBid['image_url']); ?>" alt="<?php echo htmlspecialchars($myBid['product_name']); ?>">
                    <h3><?php echo htmlspecialchars($myBid['product_name']); ?></h3>
                    <p><?php echo htmlspecialchars($myBid['description']); ?></p>
                    <p class="starting-bid">Your Bid: ₱<?php echo number_format($myBid['bid_amount'], 2); ?></p>
                    
                    <form action="users_dashboard.php" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $myBid['id']; ?>">
                        <input type="submit" name="cancel_bid" value="Cancel Bid">
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>You have not placed any bids.</p>
        <?php endif; ?>
    </div>
</section>

<footer id="contact">
    <p>Contact Us</p>
</footer>

<!-- Edit Profile Modal -->
<div class="modal" id="editProfileModal">
    <div class="modal-content">
        <span class="close-modal" id="closeModal">&times;</span>
        <h2>Edit Profile</h2>
        <form id="editProfileForm" enctype="multipart/form-data">
            <input type="text" name="firstname" placeholder="First Name" required>
            <input type="text" name="lastname" placeholder="Last Name" required>
            <input type="text" name="address" placeholder="Address" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="New Password">
            <button type="submit">Save Changes</button>
        </form>
    </div>
</div>

<script>


const notifications = []; // Store notifications here

function addNotification(productName) {
    notifications.push(productName);
    updateNotificationDisplay();
    showNotification(); // Show the red dot
}

function updateNotificationDisplay() {
    const notificationList = document.getElementById('notificationList');
    notificationList.innerHTML = ''; // Clear existing notifications

    notifications.forEach((productName, index) => {
        const notificationItem = document.createElement('div');
        notificationItem.className = 'notification-item';
        notificationItem.innerHTML = `
            <span>Congrats! You won: ${productName}</span>
            <button onclick="removeNotification(${index})">X</button>
        `;
        notificationList.appendChild(notificationItem);
    });
}

function removeNotification(index) {
    notifications.splice(index, 1); // Remove the notification
    updateNotificationDisplay(); // Update the display
    if (notifications.length === 0) {
        document.getElementById('notificationDot').style.display = 'none'; // Hide red dot if no notifications
    }
}

// Example of adding a notification when a bid is won
// Call this function with the product name when the user wins a bid
// addNotification('Product Name');





document.addEventListener('DOMContentLoaded', function () {
    fetch('get_user_data.php') 
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelector('input[name="firstname"]').value = data.user.firstname;
                document.querySelector('input[name="lastname"]').value = data.user.lastname;
                document.querySelector('input[name="address"]').value = data.user.address;
                document.querySelector('input[name="email"]').value = data.user.email;
            } else {
                alert('Failed to load user data.'); 
            }
        })
        .catch(error => console.error('Error fetching user data:', error));
});

document.getElementById('editProfileBtn').addEventListener('click', function () {
    document.getElementById('editProfileModal').style.display = 'flex';
});

document.getElementById('closeModal').addEventListener('click', function () {
    document.getElementById('editProfileModal').style.display = 'none';
});

document.getElementById('editProfileForm').addEventListener('submit', function (event) {
    event.preventDefault(); 
    var formData = new FormData(this); 

    fetch('update_profile.php', {
        method: 'POST',
        body: formData 
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Profile updated successfully!'); 
            document.getElementById('editProfileModal').style.display = 'none'; 
        } else {
            alert('Error: ' + (data.error || 'Failed to update profile.')); 
        }
    })
    .catch(error => {
        console.error('Error:', error); 
        alert('Error updating profile.');
    });
});
</script>

</body>
</html>
