<?php

require 'database.php';

session_start();

//grab data from input
$comment_id = $_GET["comment_id"];

//query from database to get comment
$stmt = $mysqli->prepare("select comment from comments where comment_id=?");
if(!$stmt) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}

$stmt->bind_param('i', $comment_id);
$stmt->execute();
$stmt->bind_result($prev_comment);
$stmt->fetch();
$stmt->close();

//get updated comment text from user input
$updated_comment = $_POST["updated_comment"];

//check session
if ($_POST['token']) {
    if(!hash_equals($_SESSION['token'], $_POST['token'])) {
        die("Request forgery detected");
    }
}

//make sure user entered input and update the comment in database
if (strlen(trim($updated_comment))) {
    $stmt2 = $mysqli->prepare("update comments set comment=? where comment_id=?");
    if(!$stmt2) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }

    $updated_comment = htmlentities($updated_comment);

    $stmt2->bind_param('si', $updated_comment, $comment_id);
    $stmt2->execute();
    $stmt2->close();

    header("Location: home.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comment Editing Page</title>
</head>
<body>
    <form method="post">
        <p><textarea rows="10" cols="60" placeholder="Update comment..." name="updated_comment" required><?php echo $prev_comment?></textarea></p>
        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
        <input type="submit" value="Post">
    </form>
</body>
</html>