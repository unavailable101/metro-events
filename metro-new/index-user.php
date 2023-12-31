<?php
  session_start();
  // include("login.php");
  // include("register.php");
  if (!isset($_SESSION['username'])){
    header('location:login-user.php');
  }
  include("header-user.php");
  include("api-user.php");

?>

    <style>
      input[type="submit"]{
        width: 90%;
        height: 40px;
        border-radius: 25px;
        font-size: 15px;
      }

      .all-events {
        position:relative;
        padding-bottom: 4%;
      }

      .all-events input[type="submit"]{
        position: absolute;
        bottom: 10px;
        left: 50%;
        transform: translateX(-50%);
      }
    </style>
    

<div id="content-placeholder">
  <?php
      echo allEvents();
  ?>
</div>

<!-- <script src="script.js"></script> -->

