<?php
    session_start();

    include('header-user.php');
    include('api-user.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        if (isset($_POST['reviewSubmit'])){
            $eventId = $_GET['eventId'];
            $review = $_POST['review'];
            createReview($_SESSION['uid'], $eventId, $review);
        }

        if (isset($_POST['upvote'])){
            // echo '<script>console.log("called");</script>';
            upVoteIncrement($_GET['eventId'], $_SESSION['uid'], "up");
        }

        if (isset($_POST['downvote'])){
            downVoteIncrement($_GET['eventId'], $_SESSION['uid'], "down");
        }

        //di mu gana ang request org and request to join buttons sa ani na file TTOTT
        //i tried eveything, but it wont work

        // if (isset($_POST['reqOrg'])){
        //     $eventId = $_GET['eventId'];
        //     $uid = $_SESSION['uid'];
        //     echo'<script src="script.js"> requestOrg( '. $eventId  .', '. $uid  .') </script>';
        // }

        // if (isset($_POST['reqJoin'])){
        //     $eventId = $_GET['eventId'];
        //     $uid = $_SESSION['uid'];
        //     echo '<script src="script.js"> requestJoin( '. $eventId  .', '. $uid  .') </script>' ;
        // }
    }
    
?>

<style>
    input[type="submit"]{
        width:80%;
    }

</style>

<div class="overview-container">
    <br>
    <?php
        $eventJSON = "data/events.json";
        $adminJSON = "data/admin.json";
        $userJSON = "data/user.json";
        $votesJSON = "data/votes.json";
        $stored_event = json_decode(file_get_contents($eventJSON), true);
        $stored_admin = json_decode(file_get_contents($adminJSON), true);
        $stored_users = json_decode(file_get_contents($userJSON), true);
        $stored_votes = json_decode(file_get_contents($votesJSON), true);

        if (isset($_GET['eventId'])) {
            // Get the value of eventId from the URL
            $eventId = $_GET['eventId'];
            
            //get event by eventId
            $eventKey = array_search($eventId, array_column($stored_event, 'eventId'));
            $eventDetail = $stored_event[$eventKey];


            //for upvotes/downvotes
            $voteKey = array_search($eventId, array_column($stored_votes, 'eventId'));
            $voteDetail = $stored_votes[$voteKey];

            //check if current user already voted
            //if naa, ang array inside ['done'] kay ma gamit
            //this is to determine if unsa na vote ang gi pili sa current user, if naka pili mn
            $isVoted = null;
            foreach($voteDetail['done'] as $vote){
                if ($vote['uid'] ==  $_SESSION['uid']){
                    $isVoted = $vote;
                    break;
                }
            }

            $adminKey = array_search($eventDetail['adminId'], array_column($stored_admin, 'uid'));
            $orgKey = array_search($eventDetail['orgId'], array_column($stored_users, 'uid'));

            $orgName = null;

            //deal with orgkey if walay sud
            if ($orgKey !== false){
                $orgName = $stored_users[$orgKey]['name'];
            }

            $adminName = $stored_admin[$adminKey]['name'];

            //to check if current user already joined the event or not
            $isPart = false;
            $participants = $eventDetail['participants'];
            
            foreach ($participants as $participant) {
                if ($participant['uid'] == $_SESSION['uid']) {
                    $isPart = true;
                    break;
                }
            }

            // echo "Event ID: " . $eventId;
            
?>
                <h1><?= $eventDetail['eventName'] ?></h1>
                <hr color="black">
                <div class="overview-inner-container">
                    
                    <div class="overview-details">
                        <h2><?=  $eventDetail['eventType'] ?></h2>
                        <div class="with-votes">
                            <h4><?=  $eventDetail['eventDate'] ?> @ <?=  $eventDetail['eventTime'] ?></h4>
                            <form method="POST" action="">
                                <center>
                                    <label>
                                        <?php if ($isVoted == null) {
                                                // echo '<input type="image" name="upvote" src="images/background/upvote.png" method="POST">';
                                                echo '
                                                    <button type="submit" name="upvote">
                                                        <img src="images/background/upvote.png">
                                                    </button>
                                                ';
                                            } else {
                                                if ($isVoted['vote'] == 'up'){
                                                    // echo '<input type="image" src="images/background/upvote-check.png" disabled>';
                                                    echo '
                                                        
                                                            <img src="images/background/upvote-check.png">
                                                        
                                                    ';
                                                } else {
                                                    // echo '<input type="image" name="upvote" src="images/background/upvote.png" method="POST">';
                                                    echo '
                                                        
                                                            <img src="images/background/upvote.png">
                                                        
                                                    ';
                                                }
                                            } ?>
                                        <span>
                                            <?= $voteDetail['upVote'] ?>
                                        </span>
                                    </label>
                                    <label>
                                        <?php if ($isVoted == null) {
                                                // echo '<input type="image" name="downvote" src="images/background/downvote.png" method="POST">';
                                                echo '
                                                    <button type="submit" name="downvote">
                                                        <img src="images/background/downvote.png">
                                                    </button>
                                                ';
                                            } else {
                                                if ($isVoted['vote'] == 'down'){
                                                    // echo '<input type="image" src="images/background/downvote-check.png" disabled>';
                                                    echo '
                                                        
                                                            <img src="images/background/downvote-check.png">
                                                        
                                                    ';
                                                } else {
                                                    // echo '<input type="image" name="downvote" src="images/background/downvote.png" method="POST">';
                                                    echo '
                                                        
                                                            <img src="images/background/downvote.png">
                                                        
                                                    ';
                                                }
                                            } ?>
                                        <span> <?= $voteDetail['downVote'] ?> </span>
                                    </label>
                                </center>
                            </form>
                        </div>
                        <hr>
                        <p>Admin: <?=  $adminName ?></p>
                        <p>Organizer: <?=  $orgName ?></p>
                        <p>Participants: </p>
                        <div class="list-participants">
                            <?= participants() ?>
                        </div>  
                    </div>
                    
                    <div class="overview-excess">
                        <script src="script.js"></script>
                        <form method="POST" action="">
                        <?=  
                            (($orgName == null) ?
                                '<input type="submit" name="reqOrg" onclick="requestOrg(' . $eventId . ',' . $_SESSION['uid'] . ')" value="Request to be an Organizer">' :
                                (($orgName !== $_SESSION['name']) ?
                                    (($isPart == false) ?
                                        '<input type="submit" name="reqJoin" onclick="requestJoin(' . $eventId . ',' . $_SESSION['uid'] . ')" value="Request to Join">' :
                                        '<input type="submit" value="Already a Participant" readonly>') :
                                    '<input type="submit" value="Already an Organizer" readonly>')
                            )
                         ?>
                        </form>
                        <hr>
                        <h4>Reviews:</h4>
                        <div class="list-reviews">
                            <?= reviews() ?>
                        </div>
                        <form method="POST" action="">
                            <textarea name="review" method="POST" placeholder="What can you say about the event..."></textarea>
                            <input id="submit-review" type="submit" name="reviewSubmit" value="Submit Review">
                        </form>
                    </div>
                </div>
<?php 
        } else {
            //sasabihin ko to kapag sayop code ko
            echo "Event ID is missing!";
        }
        //to display participants
        function participants(){
            global $eventDetail;
            global $stored_users;

            //testing if ma work
            // echo $eventDetail['eventName'];
            foreach($eventDetail['participants'] as $parts){
                //get the names of parts first
                $partKey = array_search($parts['uid'], array_column($stored_users, 'uid'));
                $partUsername = $stored_users[$partKey]['username'];
            
                if ($parts['uid'] == $_SESSION['uid']){
                    echo '
                        <div class="the-parts"><b>'.
                            $partUsername
                        .'</b></div>
                    ';
                } else {
                    echo '
                        <div class="the-parts">'.
                            $partUsername
                        .'</div>
                    ';
                } 
            }
        }

        function reviews(){
            global $stored_users;
            $eventId = $_GET['eventId'];

            $reviewJSON = "data/review.json";
            $stored_reviews = json_decode(file_get_contents($reviewJSON), true);
            
            $reverseReviews = array_reverse($stored_reviews);

            foreach($reverseReviews as $review){
                if ($review['eventId'] == $eventId){
                    //get the usernames of user who gave a review
                    $userKey = array_search($review['uid'], array_column($stored_users, 'uid'));
                    $username = $stored_users[$userKey]['username'];
                    
                    echo '
                        <div class="review-item">
                            <span>
                            '. $username .'
                            <hr>
                            </span>
                            <p>'. $review['content'] .'</p>
                        </div>
                    ';
                }
            }
        }
    ?>
</div>

<!-- <script src="script.js"></script> -->