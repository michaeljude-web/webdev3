<?php
session_start();
session_destroy(); 
header("Location: index.php"); 
header("Location: users_login.php"); 
exit();
?>
