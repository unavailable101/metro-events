<?php
    session_start();

    $isAdmin = $_SESSION['isAdmin'];
        
    session_unset();

    if ($isAdmin == true){
        header("Location: login-admin.php");
    } else {
        header("Location: login-user.php");
    }
    exit();
?>