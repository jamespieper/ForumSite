<?php

require 'database.php';

session_start();

$username = $_SESSION["username"];
$story_id = $_GET["story_id"];
$comment = $_POST["comment"];

$sort_by = $_POST["sortBy"];

//variable keeps track of whether to show certain edit, delete, etc. features
$button_type;

//check session
if ($_POST['token']) {
    if(!hash_equals($_SESSION['token'], $_POST['token'])) {
        die("Request forgery detected");
    }
}

//grab story's data
$stmt = $mysqli->prepare("select username, title, body, link, likes from stories where story_id=?");
if(!$stmt) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}

$stmt->bind_param('i', $story_id);
$stmt->execute();
$stmt->bind_result($writer, $title, $body, $link, $story_likes);
$stmt->fetch();
$stmt->close();


//grab likes
$stmt4 = $mysqli->prepare("SELECT COUNT(*) FROM likes WHERE username=? and story_id=? and comment_id=? order by like_num DESC");
if(!$stmt4) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}

$stmt4->bind_param('sii', $username, $story_id, $comment_id);
$stmt4->execute();
$stmt4->bind_result($liked);
$stmt4->fetch();

$stmt4->close();


echo '<div class="main">';

//show the story
printf("<h3>%s</h3>", htmlentities($title));
echo "<p>" . htmlentities($body) . '</p>';
echo "<p>Link: " . htmlentities($link) . '</p>';
echo "<p>By: " . htmlentities($writer) . '</p>';

//see if the current user was the writer, if so allow them to edit and delete
if ($writer == $username) {
    echo "<p style='text-align:center'><a href='edit_story.php?story_id=$story_id'>Edit</a>     ";
    echo "<a href='delete_story.php?story_id=$story_id'>Delete</a>", '</p>';
}

//check to see if liked already
if($username) {
    if($liked == 1) {
        echo "<p style='text-align:center'>" . htmlentities($story_likes) . " people like this.     " . "<a href='like_story.php?story_id=$story_id'>Dislike</a>" . '</p>', '<br>';
    } else {
        echo "<p style='text-align:center'>" . htmlentities($story_likes) . " people like this.     " . "<a href='like_story.php?story_id=$story_id'>Like</a>" . '</p>', '<br>';
    }
}

echo '</div>';

echo "<p style='text-align:center'><a href='home.php'>Main</p></a>";
echo '<br>';


echo "<strong>Comments:</strong>", '<br>';
echo '<br>';
echo '<br>';


//check to see if guest user
if ($username == NULL) {
    $button_type = "hidden";
} else {
    $button_type = "submit";
}

//grab comments in ORDER by likes
$stmt2 = $mysqli->prepare("select comment_id, username, comment, likes from comments where story_id=? order by likes DESC");

if(!stmt2) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}

$stmt2->bind_param('i', $story_id);
$stmt2->execute();
$stmt2->bind_result($comment_id, $comment_user, $actual_comment, $comment_likes);


//print each comment out nicely
while($stmt2->fetch()) {

    echo '<div class="comment_panel">';

    echo '<strong>' . htmlentities($comment_user) . '</strong>';
    echo '<br>';
    echo '<p class="comment_text">' . htmlentities($actual_comment) . '</p>';
    
    echo '<br>';

    //allow registered users to like
    if($username) {
        echo htmlentities($comment_likes) . " people like this.     " . "<a href='like_comment.php?comment_id=$comment_id'>Like</a>", '<br>';
    } else {
        echo htmlentities($comment_likes) . " people like this.     ", '<br>';
    }

    //if user was the writer of the comment, allow them to edit and delte their comment
    if ($comment_user == $username) {
        echo "<a href='edit_comment.php?comment_id=$comment_id'>Edit</a>       ";
        echo "<a href='delete_comment.php?comment_id=$comment_id'>Delete</a>", '</p>';
    }

    echo '</div>';

    echo '<br>';

    
}

$stmt2->close();


//see if comment is full and put it into the database to be displayed later
if (strlen(trim($comment))) {
    $stmt3 = $mysqli->prepare("insert into comments (story_id, username, comment) values (?, ?, ?)");
    if(!$stmt3) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }

    $username = htmlentities($username);
    $comment = htmlentities($comment);

    $stmt3->bind_param('iss', $story_id, $username, $comment);
    $stmt3->execute();
    $stmt3->close();

    //refresh the page so user can see their newly posted comment
    header("Refresh:0");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="view_story.css">
    <title>Story Home Page</title>
</head>
<body>

    <form method="post">
        <p><textarea rows="5" cols="50" placeholder="Registered users comment here..." name="comment" required></textarea></p>
        <p><input type="<?php echo $button_type;?>" value="Post"></p>
        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
    </form>

    

</body>
</html>