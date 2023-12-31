<?php
  session_start();
  if (!isset($_SESSION['username'])){
    header('location:login-admin.php');
  }

  include("api-admin.php");
  include("header-admin.php");

?>


<div id="content-placeholder">
      <?php
          echo allEvents();
      ?>
</div>

<!-- <script src="script.js"></script> -->
    
