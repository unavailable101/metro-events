<title>Metro Events - User</title>
<link rel="icon" type="image/x-icon" href="images/background/kitty.png">
<link rel="stylesheet" type="text/css" href="styles.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="script.js"></script>

<style>
    body{
        background-image: url('images/background/wp12710230-4k-anime-town-wallpapers.png');
        background-position: center center;
    }

    .nav a{
        color: black;
        text-decoration: none;
        background-color: white;
        border-radius: 25px
        /* transition: 0.1s ease-in; */
    }

    .nav a:hover{
        color: white;
        text-decoration: underline;
        background-color: black;
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
            <a href="index-user.php" class="nav-link">
                Events
            </a>
        </span>

        <!-- <span class="nav">
            <a href="#" class="nav-link" data-content="profile.php">
                Profile
            </a>
        </span> -->
        <span class="nav">
            <a href="events-organizer.php" class="nav-link">
                Events Organizer
            </a>
        </span>

        <span class="nav">
            <a href="notification.php" class="nav-link">
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