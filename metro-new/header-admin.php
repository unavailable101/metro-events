<title>Metro Events - Admin</title>
<link rel="icon" type="image/x-icon" href="images/background/kitty.png">
<link rel="stylesheet" type="text/css" href="styles.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="script.js"></script>

<style>
    body{
        background-image: url('images/background/eyyy.png');
        background-position: center 15%;
    }

    .nav a{
        color: white;
    }

    .nav a:hover{
        color: black;
        text-decoration: underline;
        background-color: white;
        transition: 0.1s ease-in;
    }
    hr{
        margin:0;
    }
</style>

<div class="header-container">
    <div class="metro-events">
        <h2>M E T R O  E V E N T S</h2>
    </div>
    <div class="nav-container">
        <span class="nav">
            <a href="index-admin.php" class="nav-link" >
                Events
            </a>
        </span>

        <span class="nav">
            <a href="create-events.php" class="nav-link" >
                Create Events
            </a>
        </span>

        <span class="nav">
            <a href="notification.php" class="nav-link" >
                Notification
            </a>
        </span>

        <span class="nav">
            <a href="logout.php">
                Log Out
            </a>
        </span>
    </div>
</div>
<hr>