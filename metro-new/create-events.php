<?php
    session_start();
    include('header-admin.php');
    include("api-admin.php");
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        if (isset($_POST['create'])){
            createEvent();
        }
        if (isset($_POST['cancel'])){
            $eventId = $_POST['eventId'];
            $cancellationReason = $_POST['cancellationReason'];
            eventCancel($eventId, $cancellationReason);
        }
    }
?>

<div class="create-container">
    <div class="create-inner-container">
        
        <h2>Create Event</h2>

        <form id="eventForm" action="" method="POST" >
            <div class="txt_field">
                <input type="text" id="eventName" name="eventName" method="POST" required>
                <label for="eventName">Event Name</label>
            </div>

            <div class="txt_field">
                <input type="text" id="eventType" name="eventType" method="POST" required>    
                <label for="eventType">Type</label>
            </div>

            <div class="txt_field">
                <input type="date" id="eventDate" name="eventDate" method="POST" placeholder="" required>    
                <label for="eventDate">Date</label>
            </div>

            <div class="txt_field">    
                <input type="time" id="eventTime" name="eventTime" method="POST" placeholder="" required>
                <label for="eventTime">Time</label>
            </div>

            <input type="submit" name="create" value="Create Event">
        </form>
    </div>

    <div class="admin-events">
        <div>
            <!-- display sa mga gipang create na events -->
            <?php
                echo adminEvents();
            ?>
        </div>
    </div>
</div>