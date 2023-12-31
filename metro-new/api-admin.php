<?php
    
    $adminJSON = "data/admin.json";
    $stored_admin = json_decode(file_get_contents($adminJSON), true);
    $new_admin;

    function toLogin(){
        global $adminJSON; 
        global $stored_admin;
        foreach ($stored_admin as $admin) {
            if($admin['username'] == $_POST['username']){
               if(password_verify($_POST['password'], $admin['password'])){
                // You can set a session and redirect the user to his account.
                $_SESSION['username'] = $_POST['username'];
                $_SESSION['name'] = $admin['name'];
                $_SESSION['uid'] = $admin['uid'];
                $_SESSION['isAdmin'] = true;
        
                echo "<script>alert('You are logged in. Hello {$_SESSION['username']}')</script>";
                header("Location: index-admin.php");
                exit();
               }
            }
        }
        // echo"<script>alert('Log in failed')</script>";
    }

    function toRegister(){
        global $adminJSON;
        global $stored_admin;
    
        $password = $_POST['password'];
        $encrypted_password = password_hash($password, PASSWORD_DEFAULT);
        $lastUser = end($stored_admin);
        $new_admin = [
            "uid" => $lastUser['uid']+1,
            "name" => $_POST['name'],
            "username" => $_POST['username'],
            "email" => $_POST['email'],
            "password" => $encrypted_password
        ];
    
        $_SESSION['uid'] = $new_admin['uid'];
        $_SESSION['name'] = $new_admin['name'];
        $_SESSION['username'] = $new_admin['username'];
        $_SESSION['email'] = $new_admin['email'];
        $_SESSION['isAdmin'] = true;
        
        array_push($stored_admin, $new_admin);
        if(file_put_contents($adminJSON, json_encode($stored_admin, JSON_PRETTY_PRINT))){
            echo "<script>alert('Your registration was successful')</script>";
            header("Location: index-admin.php");
            exit();
        } else {
            echo "<script>alert('Something went wrong, please try again')</script>";
        }
    }
    
    $eventJSON = "data/events.json";
    $stored_events = json_decode(file_get_contents($eventJSON), true);

    function createEvent() {
        global $eventJSON;
        global $stored_events;

        $lastEvent = end($stored_events);
        $eventId = isset($lastEvent['eventId']) ? $lastEvent['eventId'] + 1 : 1; // Generate unique event ID
        
        $new_event = [
            "eventId" => $eventId,
            "adminId" => $_SESSION['uid'],
            "orgId" => null,
            "participants" => [],
            "eventName" => $_POST['eventName'],
            "eventType" => $_POST['eventType'],
            "eventDate" => $_POST['eventDate'],
            "eventTime" => $_POST['eventTime']
        ];
        
        if (!isEventExist($new_event, $stored_events)) {

            array_push($stored_events, $new_event);

            if (file_put_contents($eventJSON, json_encode($stored_events, JSON_PRETTY_PRINT))) {
                //echo "<script>alert('Event creation successful')</script>";
                // echo "<script> window.location.href= = 'create-events.php'; </script>";
                // header("Location: create-events.php");
            } else {
                echo "<script>alert('Failed to create event. Please try again.')</script>";
            }
        //end
        } else {
            // echo '<script>alert("Event Already Created")</script>';
            echo '
            <script>
                showNotification("Event already exists");
            </script>
            ';
        }
    }

    function isEventExist($new_event, $stored_events) {
        foreach ($stored_events as $event) {
            if ($event['eventName'] == $new_event['eventName'] &&
                $event['eventType'] == $new_event['eventType']  &&
                $event['eventDate'] == $new_event['eventDate'] &&
                $event['eventTime'] == $new_event['eventTime'] ) {
                return true;
            }
        }
        return false; 
    }

    function adminEvents(){
        global $eventJSON;
        global $stored_events;
        $stored_users = json_decode(file_get_contents("data/user.json"), true);

        $reverseEvents = array_reverse($stored_events);

        foreach($reverseEvents as $event){
            if ($event['adminId'] == $_SESSION['uid']){
                
                $orgName;
                //get name of organizer
                foreach($stored_users as $org){
                    if ($event['orgId'] == $org['uid']){
                        $orgName = $org['name'];
                        break;
                    } else {
                        $orgName = null;
                    }
                }

                $lastP = end($event['participants']);
                $noParts = isset($lastP['partId']) ? $lastP['partId'] : 0;
                
                echo '
                    <div class="the-event">
                        <h2>' . $event['eventName'] . '</h2>
                        <hr>
                        <h3>'. 
                            $event['eventType'] . '<br><p style="font-size: 15px;margin-top: 0;">' . $event['eventDate'] . ' @ ' . $event['eventTime'] .
                        '</p></h3>
                        <p>Organizer: '.$orgName.'</p>
                        <p>Participants: '. $noParts .'</p>
                        <br><br>
                        <div>Cancel '. $event['eventName'] .' Event</div>
                         <form action="" method="POST">   
                            <input type="hidden" name="eventId" value = '.$event['eventId'].' >
                            <textarea id="cancelReason" name="cancellationReason" method="POST" placeholder="Enter reason for cancellation..."></textarea>
                            <br>
                            <input type="submit" name="cancel" value="Cancel Event">
                        </form>
                    </div>
                ';
            }
        }
    }

    // if(isset($_POST['functionName']) && function_exists($_POST['functionName'])) {
    //     $functionName = $_POST['functionName'];
    //     $eventId = $_POST['eventId'];
        
    //     if ($functionName == "eventCancel") {
    //         $cancellationReason = $_POST['cancellationReason'];
    //         eventCancel($eventId, $cancellationReason);
    //     } else {
    //         echo "Function $functionName does not exist";
    //     }
    // }

    function eventCancel($eventId, $cancellationReason) {
        // echo '<script> console.log('.$cancellationReason.'); </script>';

        global $eventJSON;
        global $stored_events;
        $eventName;

        foreach ($stored_events as $key => $event) {
            if ($event['eventId'] === intval($eventId)) {
                $eventName = $event['eventName'];
                unset($stored_events[$key]);
                break;
            }
        }

        // $stored_events = array_values($stored_events);

        $temp = array();
        foreach($stored_events as $event){
            $temp[] = $event;
        }
    
        $json_encoded = json_encode($temp, JSON_PRETTY_PRINT);
        
        file_put_contents($eventJSON, $json_encoded, LOCK_EX);

        if (!file_put_contents($eventJSON, $json_encoded, LOCK_EX)) {
            echo json_encode(["success" => false, "error" => "Failed to write to file"]);
            exit();
        }
        echo json_encode(["success" => true]);

        //send notification to org and participants about the matter
        cancelNotif($_SESSION['uid'], $eventName, $cancellationReason);

        // exit();
    }

    function cancelNotif($uid, $eventName, $cancellationReason){
        $notifJSON = "data/notif.json";
        $stored_notif = json_decode(file_get_contents($notifJSON), true);

        $lastNotif = end($stored_notif);
        $notifId = isset($lastNotif['notifId']) ? $lastNotif['notifId'] + 1 : 1;
        
        $new_notif = [
            "notifId" => $notifId,
            "uid" => intval($uid),
            "eventId" => null,
            "toAdmin" => false,
            "type" => "event-cancel",
            "title" => "Cancelling of Event " . $eventName,
            "body" => $eventName . " has been called of due to " . $cancellationReason . ". Apologies for the inconvenience." 
        ];

        array_push($stored_notif, $new_notif);

        if (file_put_contents($notifJSON, json_encode($stored_notif, JSON_PRETTY_PRINT))) {
            //echo "<script>alert('Event creation successful')</script>";
        } else {
            echo "<script>alert('Failed to send request. Please try again.')</script>";
        }
    }

    function allEvents(){
        global $eventJSON;
        global $stored_events;
        global $stored_admin;
        $stored_users = json_decode(file_get_contents("data/user.json"), true);
        
        $reverseEvents = array_reverse($stored_events);

        foreach($reverseEvents as $event){

            global $orgName;
            $adName;
            //get name of organizer and admin
            
            //for organizer
            foreach($stored_users as $user){
                if ($event['orgId'] == $user['uid']){
                    $orgName = $user['name'];
                    break;
                } else {
                    $orgName = null;
                }
            }
            //for admin
            foreach($stored_admin as $ad){
                if ($event['adminId'] == $ad['uid']){
                    $adName = $ad['name'];
                    break;
                }
            }
            
            $lastP = end($event['participants']);
            $noParts = isset($lastP['partId']) ? $lastP['partId'] : 0;

            echo '
                <div class="all-events">
                    <center>
                        <h2 style="margin: 0;">'. $event['eventName'] .'</h2>
                    </center>
                    <hr style="margin-top:10;margin-bottom:10;">
                    <h3 style="margin: 0;">' .
                         $event['eventType'] . '<br><p style="font-size: 15px;margin-top: 0;">' . $event['eventDate'] . ' @ ' . $event['eventTime'].'</p></h3>
                    <p>Admin: '.$adName.'</p>
                    <p>Organizer: '.$orgName.'</p>
                    <p>Participants: '. $noParts .'</p>
                </div>';
        }
    }

    function acceptRequest($requestorId, $eventId){
        $notifJSON = "data/notif.json";
        $stored_notif = json_decode(file_get_contents($notifJSON), true);

        $lastNotif = end($stored_notif);
        $notifId = isset($lastNotif['notifId']) ? $lastNotif['notifId'] + 1 : 1;

        global $eventJSON;
        global $stored_events;
        $eventName;

        // foreach($stored_events as $event){
        //     if($eventId == $event['eventId']){
        //         $event['orgId'] = $requestorId;
        //         $eventName = $event['eventName'];
        //         break;
        //     }
        // }

        $eventKey = array_search($eventId, array_column($stored_events, 'eventId'));
        $eventName = $stored_events[$eventKey]['eventName'];
        
        
        $new_notif = [
            "notifId" => $notifId,
            "uid" => intval($requestorId),
            "eventId" => intval($eventId),
            "toAdmin" => false,
            "type" => "got-accept",
            "title" => "Request Approved",
            "body" => "Your request to be an organizer of " . $eventName . " has been accepted. Yey." 
        ];
        
        if (!isNotificationExist($new_notif, $stored_notif)) {

            //send notif
            array_push($stored_notif, $new_notif);

            if (file_put_contents($notifJSON, json_encode($stored_notif, JSON_PRETTY_PRINT))) {
                //echo "<script>alert('Event creation successful')</script>";
            } else {
                echo "<script>alert('Failed to send request. Please try again.')</script>";
            }

            //update orgId or organizer of the event
            $stored_events[$eventKey]['orgId'] = intval($requestorId);

            if (file_put_contents($eventJSON, json_encode($stored_events, JSON_PRETTY_PRINT))) {
                //echo "<script>alert('Event creation successful')</script>";
            } else {
                echo "<script>alert('Failed to send request. Please try again.')</script>";
            }
        

        //end here
        } else {
            // echo '<script>alert("Already Answered!")</script>';
            echo '
            <script>
                showNotification("Already accepted requestor.");
            </script>
            ';
        }
    }

    function declineRequest($requestorId, $eventId){
        $notifJSON = "data/notif.json";
        $stored_notif = json_decode(file_get_contents($notifJSON), true);

        $lastNotif = end($stored_notif);
        $notifId = isset($lastNotif['notifId']) ? $lastNotif['notifId'] + 1 : 1;

        global $stored_events;
        $eventName;
        foreach($stored_events as $event){
            if($eventId == $event['eventId']){
                $eventName = $event['eventName'];
                break;
            }
        }

    
        $new_notif = [
            "notifId" => $notifId,
            "uid" => intval($requestorId),
            "eventId" => intval($eventId),
            "toAdmin" => false,
            "type" => "got-decline",
            "title" => "Request Decline",
            "body" => "Your request to be an organizer of " . $eventName . " has been decline. So sad." 
        ];
        
        if (!isNotificationExist($new_notif, $stored_notif)) {

            array_push($stored_notif, $new_notif);

            if (file_put_contents($notifJSON, json_encode($stored_notif, JSON_PRETTY_PRINT))) {
                //echo "<script>alert('Event creation successful')</script>";
            } else {
                echo "<script>alert('Failed to send request. Please try again.')</script>";
            }
        //end here
        } else {
            // echo '<script>alert("Already Answered!")</script>';
            echo '
            <script>
                showNotification("Already declined to requestor");
            </script>
            ';
        }
    }

    //to avoid duplication of information to the json file
    function isNotificationExist($new_notif, $stored_notif) {
        $type = true;
        $title = true;

        foreach ($stored_notif as $notif) {

            if ( $notif['type'] == $new_notif['type'] && $notif['type'] == "got-decline" || 
                 $notif['type'] == "got-accept" ){
                $type = false;
            }
            //you is da problem
            if ( $notif['title'] == $new_notif['title'] || 
                $notif['title'] == "Request Decline" || 
                $notif['title'] == "Request Approved"){
                $title = false;
            }

            if ($notif['eventId'] == $new_notif['eventId'] &&
                $notif['uid'] == $new_notif['uid'] && 
                $type && $title) {
                return true;
            }
        }
        return false; 
    }

?>