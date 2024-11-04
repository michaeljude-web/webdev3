
<?php
session_start(); 
include 'db_connection.php';

$user_id = $_SESSION['user_id']; 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $password = $_POST['password'] ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    try {
        $sql = "UPDATE users SET firstname = :firstname, lastname = :lastname, address = :address, email = :email" . ($password ? ", password = :password" : "") . " WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':firstname', $firstname);
        $stmt->bindParam(':lastname', $lastname);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    
        if ($password) {
            $stmt->bindParam(':password', $password);
        }
        $stmt->execute();

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>
