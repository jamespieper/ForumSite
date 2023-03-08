<?php

require 'database.php';

session_start();

$username = $_SESSION['username'];

//get story
$story_id = $_GET['story_id'];

//grab from database
$stmt = $mysqli->prepare("SELECT COUNT(*) FROM likes WHERE username=? and story_id=?");
if(!$stmt) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}

$stmt->bind_param('si', $username, $story_id);
$stmt->execute();
$stmt->bind_result($liked);
$stmt->fetch();

$stmt->close();

//check to see if liked already, if so unlike
if($liked == 1) {

        //delete from likes table
        $stmt2 = $mysqli->prepare("delete from likes where username=?");
        if(!$stmt2) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }

        $stmt2->bind_param('s', $username);
        $stmt2->execute();
        $stmt2->close();

        //update in stories table
        $stmt3 = $mysqli->prepare("update stories set likes=likes-1 where story_id=?");
        if(!$stmt3) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }

        $stmt3->bind_param('i', $story_id);
        $stmt3->execute();
        $stmt3->close();

        //tell user they unliked and return to home
        echo "You've unliked this story";
        header("Refresh:2 url=home.php");
        exit;

} 
//if not already liked, then like it
else {

        //put into likes database
        $stmt4 = $mysqli->prepare("insert into likes (username, story_id) values (?, ?)");
        if(!$stmt4) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }

        $username = htmlentities($username);

        $stmt4->bind_param('si', $username, $story_id);
        $stmt4->execute();
        $stmt4->close();

        //update likes in stories database
        $stmt5 = $mysqli->prepare("update stories set likes=likes+1 where story_id=?");
        if(!$stmt5) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }

        $stmt5->bind_param('i', $story_id);
        $stmt5->execute();
        $stmt5->close();

        
        echo "You've liked this story";
        header("Refresh:2 url=home.php");
    }



?>
