<?php
include 'db_connection.php';

try {
    $input = json_decode(file_get_contents("php://input"), true);
    $productId = $input['productId'];
    $bidAmount = $input['bidAmount'];

    
    $stmt = $pdo->prepare("INSERT INTO bids (product_id, bid_amount) VALUES (:productId, :bidAmount)");
    $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
    $stmt->bindParam(':bidAmount', $bidAmount, PDO::PARAM_STR);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to place bid']);
    }

} catch (PDOException $e) {
    
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
