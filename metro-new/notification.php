<?php
    session_start();
    
    if ($_SESSION['isAdmin'] == true){
        include('header-admin.php');
        
        include('api-admin.php');

        if ($_SERVER["REQUEST_METHOD"] == "POST"){
            $requestorId = $_POST['requestorId'];
            $eid = $_POST['eventId'];

            if (isset($_POST['accept'])){
                acceptRequest($requestorId, $eid);
            }
            if (isset($_POST['decline'])){
                declineRequest($requestorId, $eid);
            }
        }

    } else {
        //no need api for user
        //displaying ramn sa notif
        //no function to call in api-user
        include('header-user.php');
    }
?>

<style>
    input[type="submit"]{
        width: 25%;
        height: 25px;
        font-size: 12px;
    }
    /* .notif-item {
        position:relative;
        padding-bottom: 4%;
      } */

      .notif-item form{
        position: relative;
        bottom: -10px;
        left: 25%;
      }
</style>

<div class="notif-container">
    <?php
        $notifJSON = "data/notif.json";
        $stored_notif = json_decode(file_get_contents($notifJSON), true);
        $stored_users = json_decode(file_get_contents("data/user.json"), true);
        $stored_admin = json_decode(file_get_contents("data/admin.json"), true);
        $stored_events = json_decode(file_get_contents("data/events.json"), true);

        $reverseNotifs = array_reverse($stored_notif);

        // notif para ni admin
        if ($_SESSION['isAdmin'] == true){
            
            foreach($reverseNotifs as $notif){
                $username;
                $reqId;
                $eventName;
                $eventId;
                global $adminId;

                //get name of requestor
                foreach($stored_users as $user){
                    if($notif['uid'] == $user['uid']){
                        $username = $user['username'];
                        $reqId = $user['uid'];
                        // break;
                    }
                }

                //receive notif of request to org ni
                if($notif['toAdmin'] == true){

                    $eventDetails = getEventDetails($notif['eventId'], $stored_events);

                    if ($eventDetails && $eventDetails['adminId'] == $_SESSION['uid']){
                        echo '
                            <div class="notif-item">
                                <h3>
                                    '.$notif['title']. '"' .$eventDetails['eventName']. '"' .'
                                </h3>
                                <p>'.$username.$notif['body'].'</p>
                                <form action="" method="POST">
                                    <input type="hidden" name="requestorId" value = '.$reqId.' >
                                    <input type="hidden" name="eventId" value = '.$eventDetails['eventId'].' >
                                    
                                    <input type="submit" name="accept" value="Accept">
                                    <input type="submit" name="decline" value="Decline">
                                </form>
                            </div>
                        ';
                    }
                }

            }

        } else {
            //for users to receive notifications
            foreach($reverseNotifs as $notif){
                $username;
                $eventName;
                global $orgId;

                //get name of requestor
                foreach($stored_users as $user){
                    if($notif['uid'] == $user['uid']){
                        $username = $user['username'];
                        break;
                    }
                }

                //get name of the event
                foreach($stored_events as $event){
                    if($notif['eventId'] == $event['eventId']){
                        $eventName = $event['eventName'];
                        $orgId = $event['orgId'];
                        break;
                    }
                }
                
                //under this kay seperated by different types of notif
                //since lahi2 output niya in each type
                if ($notif['toAdmin'] == false){
                    
                    //for organizers
                    //users requesting to join the event 
                    if ($orgId == $_SESSION['uid'] && $notif['type'] == "to-join"){
                        echo '
                            <div class="notif-item">
                                <h3>
                                    '.$notif['title'].'"'.$eventName. '"' .'
                                </h3>
                                <p>'.$username.$notif['body'].'</p>
                            </div>
                        ';
                    }
                    
                    //for all users
                    //for cancel events
                    //literal para tanan, regardless if naka join or org ang user or wala
                    //an awareness to all user that an event is cancelled, char ambot
                    if($notif['type'] == "event-cancel"){
                        echo '
                            <div class="notif-item">
                                <h3>
                                    '.$notif['title'].'
                                </h3>
                                <p>'.$notif['body'].'</p>
                            </div>
                        ';
                    }
                    
                    //for accept/decline requests to org
                    //i add nlng nako dire ang request to join ( receive accept / decline)
                    //same ra ang notif type para sa request to org and request to join
                    if($notif['type'] == "got-accept" || $notif['type'] == "got-decline"){
                        if ($notif['uid'] == $_SESSION['uid']){
                            echo '
                                <div class="notif-item">
                                    <h3>
                                        '.$notif['title'].'
                                    </h3>
                                    <p>'.$notif['body'].'</p>
                                </div>
                            ';
                        }
                    }
                }

            }
        }

        function getEventDetails($eventId, $stored_events) {
            foreach ($stored_events as $event) {
                if ($eventId == $event['eventId']) {
                    return $event; // Return the event details
                }
            }
            return null; // Return null if event details not found
        }
    ?>
</div>