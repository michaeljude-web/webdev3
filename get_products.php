<?php
include 'db_connection.php';

try {
   
    $stmt = $pdo->prepare("SELECT id, name, description, price FROM products");
    $stmt->execute();

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

   
    header('Content-Type: application/json');
    echo json_encode($products);

} catch (PDOException $e) {
  
    echo json_encode(['error' => $e->getMessage()]);
}
?>
