<?php
    session_start();
    include("api-user.php");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        toLogin();
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Metro Events - User</title>
        <link rel="stylesheet" type="text/css" href="styles.css">
        <link rel="icon" type="image/x-icon" href="images/background/kitty.png">
        
        <style>
            body{
                background-image: url('images/background/waiting.jpg');
                background-repeat: no-repeat;
                background-size: cover;
                background-position: center;
                background-attachment: fixed;
            }
        </style>

    </head>
    <body>
        <div class="login-container">
            <div class="left">
                <h1>M E T R O  E V E N T S</h1>
            </div>
            <div class="right">
                <div class="login-inner-container center-form login-form" id="blur">
                    <h1>LOG IN</h1>
                    <br>
                    <form method="POST" action="">
                        <div class="txt_field">
                            <input type="text" name="username" method="POST" required>
                            <label>Username</label>
                        </div>
                        <br>
                        <div class="txt_field">
                            <input type="password" name="password" method="POST" required>
                            <label>Password</label>
                        </div>
                        <br><br>
                        <input name="submit" type="Submit" value="Log In">
                        <br><br>
                        <span>Don't have an account? <a href="register-user.php" id="register">Register</a></span>
                        <br>
                        <span>Login as <a href="login-admin.php" id="register">Admin</a></span>
                    </form>
                    <br><br>
                </div>
            </div>
        </div>
    </body>
</html>