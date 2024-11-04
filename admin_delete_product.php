<?php
include 'db_connection.php';

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    // sa bids
    $deleteBidsSql = "DELETE FROM bids WHERE product_id = :product_id";
    $deleteBidsStmt = $pdo->prepare($deleteBidsSql);
    $deleteBidsStmt->bindParam(':product_id', $product_id);
    $deleteBidsStmt->execute();
    // products
    $deleteProductSql = "DELETE FROM products WHERE id = :id";
    $deleteProductStmt = $pdo->prepare($deleteProductSql);
    $deleteProductStmt->bindParam(':id', $product_id);
    
    if ($deleteProductStmt->execute()) {
        echo '<script>
            alert("Product Deleted");
            window.location.href = "admin_bid.php"; 
          </script>';
        exit();
    } else {
        echo "Error deleting product.";
    }
} else {
    echo "";
}
?>
