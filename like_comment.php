<?php

require 'database.php';

session_start();

$username = $_SESSION['username'];

//get the comment from input
$comment_id = $_GET['comment_id'];

//grab from database
$stmt = $mysqli->prepare("SELECT COUNT(*) FROM likes WHERE username=? and comment_id=?");
if(!$stmt) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}

$stmt->bind_param('si', $username, $comment_id);
$stmt->execute();
$stmt->bind_result($liked);
$stmt->fetch();

$stmt->close();

//if liked == 1, it means user already liked this, so clicking it again will unlike it
if($liked == 1) {

        //delete from likes database
        $stmt2 = $mysqli->prepare("delete from likes where username=?");
        if(!$stmt2) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }

        $stmt2->bind_param('s', $username);
        $stmt2->execute();
        $stmt2->close();

        //update number of likes in comments
        $stmt3 = $mysqli->prepare("update comments set likes=likes-1 where comment_id=?");
        if(!$stmt3) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }

        $stmt3->bind_param('i', $comment_id);
        $stmt3->execute();
        $stmt3->close();

        //tell the user they unliked the comment and return to home
        echo "You've unliked this comment";
        header("Refresh:2 url=home.php");
        exit;
    
} 

//this means user hasn't already liked it -- so we like it
else {

        //if user hasn't liked, put into likes database
        $stmt4 = $mysqli->prepare("insert into likes (username, comment_id) values (?, ?)");
        if(!$stmt4) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }

        $username = htmlentities($username);

        $stmt4->bind_param('si', $username, $comment_id);
        $stmt4->execute();
        $stmt4->close();


        //and update in comments
        $stmt5 = $mysqli->prepare("update comments set likes=likes+1 where comment_id=?");
        if(!$stmt5) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }

        $stmt5->bind_param('i', $comment_id);
        $stmt5->execute();
        $stmt5->close();

        echo "You've liked this comment";
        header("Refresh:2 url=home.php");
    }



?>
