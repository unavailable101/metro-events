<?php
    session_start();
     include("api-user.php");

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        toRegister();
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Metro Events - User</title>
        <link rel="stylesheet" href="styles.css" />
        <link rel="icon" type="image/x-icon" href="images/background/kitty.png">

        <style>
            body {
                /* find appropriate bg for kanang mu fit */
                background-image: url('images/background/wp4586808-4k-anime-town-wallpapers.jpg');
                background-repeat: no-repeat;
                background-size: cover;
                background-position: center center;
                background-attachment: fixed;
            }
        </style>
    </head>
    <body>
        <div class="center-form register-form">
            <h1>REGISTER</h1>
            <form method="POST" action="">
                <div class="txt_field">
                    <input type="text" name="name" method="POST" required>
                    <label>Name</label>
                </div>
                <div class="txt_field">
                    <input type="text" name="username" method="POST" required>
                    <label>Username</label>
                </div>
                <div class="txt_field">
                    <input type="email" name="email" method="POST" required>
                    <label>Email</label>
                </div>
                <div class="txt_field">
                    <input type="password" name="password" method="POST" required> 
                    <label>Password</label>
                </div>
                <input name="submit" type="Submit" value="Sign Up">
                <div class="signup_link">
                    Have an Account ? <a href="login-user.php" id="login">Login Here</a>
                </div>
            </form>
        </div>
    </body>
</html>