<?php
    
    $userJSON = "data/user.json";
    $stored_users = json_decode(file_get_contents($userJSON), true);
    $new_user;

    function toLogin(){
        global $userJSON; 
        global $stored_users;
        foreach ($stored_users as $user) {
            if($user['username'] == $_POST['username']){
               if(password_verify($_POST['password'], $user['password'])){
                // You can set a session and redirect the user to his account.
                $_SESSION['username'] = $_POST['username'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['uid'] = $user['uid'];
                $_SESSION['isAdmin'] = false;
        
                echo "<script>alert('You are logged in. Hello {$_SESSION['username']}')</script>";
                header("Location: index-user.php");
                exit();
               }
            }
        }
        echo " <script>alert('Log in failed')</script> ";
    }

    function toRegister(){
        global $userJSON;
        global $stored_users;
        global $new_user;

        $password = $_POST['password'];
        $encrypted_password = password_hash($password, PASSWORD_DEFAULT);
        $lastUser = end($stored_users);
        $new_user = [
            "uid" => $lastUser['uid']+1,
            "name" => $_POST['name'],
            "username" => $_POST['username'],
            "email" => $_POST['email'],
            "password" => $encrypted_password
        ];
    
        $_SESSION['uid'] = $new_user['uid'];
        $_SESSION['name'] = $new_user['name'];
        $_SESSION['username'] = $new_user['username'];
        $_SESSION['email'] = $new_user['email'];
        $_SESSION['isAdmin'] = false;
        
        array_push($stored_users, $new_user);
        if(file_put_contents($userJSON, json_encode($stored_users, JSON_PRETTY_PRINT))){
            echo "<script>alert('Your registration was successful')</script>";
            header("Location: index-user.php");
            exit();
        } else {
            echo "<script>alert('Something went wrong, please try again')</script>";
        }
    }

    $eventJSON = "data/events.json";
    $stored_events = json_decode(file_get_contents($eventJSON), true);
    $stored_admin = json_decode(file_get_contents("data/admin.json"), true);

    function allEvents(){
        global $stored_users;
        global $stored_events;
        global $eventJSON;
        global $stored_admin;
        
        $reverseEvents = array_reverse($stored_events);

        foreach($reverseEvents as $event){

            global $orgName;
            global $adName;
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
            //para number of participants rani
            $lastP = end($event['participants']);
            $noParts = isset($lastP['partId']) ? $lastP['partId'] : 0;

            // searching if current user is already a participant
            $isPart = false;

            $eventKey = array_search($event['eventId'], array_column($stored_events, 'eventId'));
            if ($eventKey !== false) {
                $participants = $stored_events[$eventKey]['participants'];
                
                foreach ($participants as $participant) {
                    if ($participant['uid'] == $_SESSION['uid']) {
                        $isPart = true;
                        break;
                    }
                }
            }


            echo '
                <div class="all-events">
                    <center>
                        <a href="events.php?eventId='.$event['eventId'].'">
                            <h2 style="margin: 0;">'. $event['eventName'] .'</h2>
                        </a>
                    </center>
                    <hr style="margin-top:10;margin-bottom:10;">
                    <h3 style="margin: 0;">' .
                         $event['eventType'] . '<br><p style="font-size: 15px;margin-top: 0;">' . $event['eventDate'] . ' @ ' . $event['eventTime'].'</p></h3>
                    <p>Admin: '.$adName.'</p>
                    <p>Organizer: '.$orgName.'</p>
                    <p>Participants: '. $noParts .'</p>'. 
                    (($orgName == null) ?
                    '<input type="submit" name="submit" onclick="requestOrg(' . $event['eventId'] . ',' . $_SESSION['uid'] . ')" value="Request to be an Organizer">' :
                    (($orgName !== $_SESSION['name']) ?
                        (($isPart == false) ?
                            '<input type="submit" name="submit" onclick="requestJoin(' . $event['eventId'] . ',' . $_SESSION['uid'] . ')" value="Request to Join">' :
                            '<input type="submit" value="Already a Participant" disabled>') :
                        '<input type="submit" value="Already an Organizer" disabled>')
                )
                . '
                </div>';
        }
    }

    //careful kuno sa pag gamit sa ' and ",, maka destroy sa code labi na if naa sa sud sa echo

    // for request join/orginzer
    if(isset($_POST['functionName']) && function_exists($_POST['functionName'])) {
        $functionName = $_POST['functionName'];
        $eventId = $_POST['eventId'];
        $uid = $_POST['uid'];
        $functionName($eventId, $uid);
    }

    // $notifJSON = "data/notif.json";
    // $stored_notif = json_decode(file_get_contents($notifJSON), true);
    // di lng ni nako sila ipa-global2
    // ambot nganu mu gana sa uban pero dili kani,, perme nlng di mu gana si notif hays

    function joinRequest($eventId, $uid){
     
        $notifJSON = "data/notif.json";
        $stored_notif = json_decode(file_get_contents($notifJSON), true);

        $lastNotif = end($stored_notif);
        $notifId = isset($lastNotif['notifId']) ? $lastNotif['notifId'] + 1 : 1;
        
        $new_notif = [
            "notifId" => $notifId,
            "uid" => intval($uid),
            "eventId" => intval($eventId),
            "toAdmin" => false,
            "type" => "to-join",
            "title" => "Requesting to Join ",
            "body" => " is requesting to join the event."
        ];
        
        if(!isNotifExist($new_notif, $stored_notif)){
            array_push($stored_notif, $new_notif);

            if (file_put_contents($notifJSON, json_encode($stored_notif, JSON_PRETTY_PRINT))) {
                // echo "<script>alert('Event creation successful')</script>";
            } else {
                echo "<script>alert('Failed to send request. Please try again.')</script>";
            } 
        //end
        } else {
            // echo '<script>alert("Already sent a request")</script>';
            //basta mu gana nani sha oi
            //pagpa gwapa nlng sa alerts
            //same reason sa orgRequest
            echo 'sent-request';
        }
    }

    //request to organizer
    function orgRequest($eventId, $uid){
     
        $notifJSON = "data/notif.json";
        $stored_notif = json_decode(file_get_contents($notifJSON), true);

        $lastNotif = end($stored_notif);
        $notifId = isset($lastNotif['notifId']) ? $lastNotif['notifId'] + 1 : 1;
        
        $new_notif = [
            "notifId" => $notifId,
            "uid" => intval($uid),
            "eventId" => intval($eventId),
            "toAdmin" => true,
            "type" => "to-org",
            "title" => "Requesting to become an Event Organizer of ",
            "body" => " is requesting to become the organizer of the event."
        ];
        
        if (!isNotifExist($new_notif, $stored_notif)){
            array_push($stored_notif, $new_notif);

            if (file_put_contents($notifJSON, json_encode($stored_notif, JSON_PRETTY_PRINT))) {
                // echo "<script>alert('Event creation successful')</script>";
            } else {
                echo "<script>alert('Failed to send request. Please try again.')</script>";
            }
        //end
        } else {
            // echo '<script>alert("Already sent a request")</script>';
            //this function is called through javascript
            //if i echo nako, then mu console.log ni js ako output instead of displaying
            //this is to send pop-up notif
            echo 'sent-request';
        }
    }

    //dapat same unta ni sa katu ubos, pero nakalimot mn ko, so create nlng bago ehe
    function isNotifExist($new_notif, $stored_notif) {

        foreach ($stored_notif as $notif) {
            if ($notif['eventId'] == $new_notif['eventId'] &&
                $notif['uid'] == $new_notif['uid'] && 
                $notif['type'] == $new_notif['type']) {
                return true;
            }
        }
        return false; 
    }

    

    function eventOrganizer(){
        
        global $eventJSON;
        global $stored_events;
        global $stored_admin;
        
        $notifJSON = "data/notif.json";
        $stored_notif = json_decode(file_get_contents($notifJSON), true);

        $reverseEvents = array_reverse($stored_events);

        foreach ($reverseEvents as $event) {
            if ($event['orgId'] == $_SESSION['uid']) {

            $adminKey = array_search($event['adminId'], array_column($stored_admin, 'uid'));
            $notifKey = array_search($event['eventId'], array_column($stored_notif, 'eventId'));
            $adminDetail = $stored_admin[$adminKey];
            $notifDetail = $stored_notif[$notifKey];

            $lastP = end($event['participants']);
            $noParts = isset($lastP['partId']) ? $lastP['partId'] : 0;
?>

            <div class="events-inner-container">
                <h1><?= $event['eventName'] ?></h1>
                <hr>
                <div class="details-container">
                    <div class="event-details">
                        <h2><?= $event['eventType'] ?></h2>
                        <h4><?= $event['eventDate'] ?> @ <?= $event['eventTime'] ?></h4>
                        <p>Admin: <?= $adminDetail['name'] ?></p>
                        <p>Participants: <?= $noParts ?></p>
                    </div>
                    <div class="see-requests">
                        <h3>Requests to Join:</h3>
                        <div class="requests-container">
                            
                            <?= seeRequests($event['eventId']) ?>
                            
                        </div>
                    </div>
                </div>
            </div>

<?php
    }
}


       
    }

    function seeRequests($eventId){
        $notifJSON = "data/notif.json";
        $stored_notif = json_decode(file_get_contents($notifJSON), true);
        global $stored_users;
        
        $reverseNotif = array_reverse($stored_notif);

        foreach($reverseNotif as $requests) {
            if ($requests['type'] == "to-join"){
                    
                    $userKey = array_search($requests['uid'], array_column($stored_users, 'uid'));
                    $userDetail = $stored_users[$userKey];

                    if ($requests['eventId'] == $eventId){
                        echo '
                            <div class="requests-item">
                                <form method="POST" action="">
                                    <span>'. $userDetail['username'] .'</span>
                                    
                                    <input type="hidden" name="requestorId" value = " '. $userDetail['uid'] .' " >
                                    <input type="hidden" name="eventId" value = " '. $eventId .' " >

                                    <input type="submit" name="accept" value="Accept">
                                    <input type="submit" name="decline" value="Decline">
                                </form>
                             </div>
                        ';
                    }
                }
        }
    }

    function acceptRequest($requestorId, $eventId){
        global $eventJSON;
        global $stored_events;

        $notifJSON = "data/notif.json";
        $stored_notif = json_decode(file_get_contents($notifJSON), true);

        $lastNotif = end($stored_notif);
        $notifId = isset($lastNotif['notifId']) ? $lastNotif['notifId'] + 1 : 1;

        
        $eventName;
        $eventKey = array_search($eventId, array_column($stored_events, 'eventId'));
        $eventName = $stored_events[$eventKey]['eventName'];

        //sending notifs for accept request
        $new_notif = [
            "notifId" => $notifId,
            "uid" => intval($requestorId),
            "eventId" => intval($eventId),
            "toAdmin" => false,
            "type" => "got-accept",
            "title" => "Request to Join Approved",
            "body" => "Your request to join ". $eventName ." event has been accepted. Yey."
        ];

        if (!isNotificationExist($new_notif, $stored_notif)){

            array_push($stored_notif, $new_notif);

            if (file_put_contents($notifJSON, json_encode($stored_notif, JSON_PRETTY_PRINT))) {
                //echo "<script>alert('Event creation successful')</script>";
            } else {
                echo "<script>alert('Failed to send request. Please try again.')</script>";
            }

            removeNotif($new_notif['uid'], $new_notif['eventId']);

            //after sending notif kay diha paka mu add sa participants
            $participants = $stored_events[$eventKey]['participants'];

            $lastP = end($participants);
            $newPartId = isset($lastP['partId']) ? $lastP['partId'] + 1 : 1;

            $newParticipant = [
                "partId" => $newPartId,
                "uid" => intval($requestorId)
            ];

            $participants[] = $newParticipant;
            $stored_events[$eventKey]['participants'] = $participants;
        

            if (file_put_contents($eventJSON, json_encode($stored_events, JSON_PRETTY_PRINT))) {
                // Event participants updated successfully
            } else {
                echo "<script>alert('Failed to update event participants. Please try again.')</script>";
            }

        } else {
            // echo '<script>alert("Already Accept")</script>';
            echo '
                <script>
                    showNotification("Already accepted requestor")
                </script>';
        }
    }

    function declineRequest($requestorId, $eventId){

        global $eventJSON;
        global $stored_events;

        $notifJSON = "data/notif.json";
        $stored_notif = json_decode(file_get_contents($notifJSON), true);

        $lastNotif = end($stored_notif);
        $notifId = isset($lastNotif['notifId']) ? $lastNotif['notifId'] + 1 : 1;
        
        $eventName;

        $eventKey = array_search($eventId, array_column($stored_events, 'eventId'));
        if ($eventKey !== false) {
            $eventName = $stored_events[$eventKey]['eventName'];
        }


        $new_notif = [
            "notifId" => $notifId,
            "uid" => intval($requestorId),
            "eventId" => intval($eventId),
            "toAdmin" => false,
            "type" => "got-decline",
            "title" => "Request to Join Rejected",
            "body" => "Your request to join ". $eventName ." event has been decline. So sad."
        ];

        if (!isNotificationExist($new_notif, $stored_notif)){

            array_push($stored_notif, $new_notif);

            if (file_put_contents($notifJSON, json_encode($stored_notif, JSON_PRETTY_PRINT))) {
                //echo "<script>alert('Event creation successful')</script>";
            } else {
                echo "<script>alert('Failed to send request. Please try again.')</script>";
            }

            removeNotif($new_notif['uid'], $new_notif['eventId']);
                
            //end
        } else {
            // echo '<script>alert("Already Decline")</script>';
            //unlike sa orgRequest ug joinRequest, directly ni sha mu display
            //but since ako gi change ang function sa js, ako pd ni sha o change
            //same ramn sd ni sila attributes, lahi lng ang message
            echo '
                <script>
                    showNotification("Already declined to requestor");
                </script>';
        }
    }

    //to avoid duplication of info sa json file 
    function isNotificationExist($new_notif, $stored_notif) {
        $type = false;
        $title = false;

        foreach ($stored_notif as $notif) {

            if ($notif['eventId'] == $new_notif['eventId'] &&
                $notif['uid'] == $new_notif['uid']) {

                //check sa similarities sa type and title
                if ( 
                    (
                        $notif['type'] == "got-decline" || 
                        $notif['type'] == "got-accept"
                    ) &&
                    (
                        $new_notif['type'] == "got-decline" || 
                        $new_notif['type'] == "got-accept"
                    )
                    ){
                   $type = true;
               }
            //pwede walay type kay ma conflict sa pag accept/decline sa api-admin
            //same baya ni silag type
               if ( 
                    (
                        $notif['title'] == "Request to Join Rejected" || 
                        $notif['title'] == "Request to Join Approved"
                    ) &&
                    (
                        $new_notif['title'] == "Request to Join Rejected" || 
                        $new_notif['title'] == "Request to Join Approved"
                    )
                    ){
                   $title = true;
               }
               
                if ($title && $type){
                    return true;
                }
            }
        }
        return false; 
    }

    function removeNotif($uid, $eventId) {
        $notifJSON = "data/notif.json";
        $stored_notif = json_decode(file_get_contents($notifJSON), true);

        foreach ($stored_notif as $key => $notif) {
            if ($notif['uid'] == $uid && $notif['eventId'] == $eventId) {
                unset($stored_notif[$key]); // Remove the notification
                break;
            }
        }

        file_put_contents($notifJSON, json_encode(array_values($stored_notif), JSON_PRETTY_PRINT));
    }

    function createReview($uid, $eventId, $review){
        
        $reviewJSON = "data/review.json";
        $stored_reviews = json_decode(file_get_contents($reviewJSON), true);

        $lastReview = end($stored_reviews);
        $reviewId = isset($lastReview['reviewId']) ? $lastReview['reviewId'] + 1 : 1;
        
        $new_review = [
            "reviewId" => $reviewId,
            "uid" => intval($uid),
            "eventId" => intval($eventId),
            "content" => $review
        ];

        array_push($stored_reviews, $new_review);

        if (file_put_contents($reviewJSON, json_encode($stored_reviews, JSON_PRETTY_PRINT))) {
            //echo "<script>alert('Event creation successful')</script>";
        } else {
            echo "<script>alert('Failed to send request. Please try again.')</script>";
        }
    }

    function upVoteIncrement ($eventId, $uid, $vote){
        
        $votesJSON = "data/votes.json";
        $stored_votes = json_decode(file_get_contents($votesJSON), true);
        
        $voteKey = array_search($eventId, array_column($stored_votes, 'eventId'));
        
        //increment upvote
        $stored_votes[$voteKey]['upVote'] += 1;

        // details sa kinsa ni vote
        $newDone = [
            "uid" => intval($uid),
            "vote" => $vote
        ];

        // Append the new vote details
        $stored_votes[$voteKey]['done'][] = $newDone;
    

        if (file_put_contents($votesJSON, json_encode($stored_votes, JSON_PRETTY_PRINT))) {
            // Event participants updated successfully
        } else {
            echo "<script>alert('Failed to update event participants. Please try again.')</script>";
        }

    }

    function downVoteIncrement ($eventId, $uid, $vote){
        
        
        $votesJSON = "data/votes.json";
        $stored_votes = json_decode(file_get_contents($votesJSON), true);
        
        $voteKey = array_search($eventId, array_column($stored_votes, 'eventId'));
        //increment upvote
        $stored_votes[$voteKey]['downVote'] += 1;

        // details sa kinsa ni vote
        $newDone = [
            "uid" => intval($uid),
            "vote" => $vote
        ];

        // Append the new vote details
        $stored_votes[$voteKey]['done'][] = $newDone;
    

        if (file_put_contents($votesJSON, json_encode($stored_votes, JSON_PRETTY_PRINT))) {
            // Event participants updated successfully
        } else {
            echo "<script>alert('Failed to update event participants. Please try again.')</script>";
        }

    }
?>