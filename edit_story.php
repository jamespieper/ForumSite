<?php

require 'database.php';

session_start();

//get story
$story_id = $_GET["story_id"];

//grab from database
$stmt = $mysqli->prepare("select title, body, link from stories where story_id=?");
if(!$stmt) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}

$stmt->bind_param('i', $story_id);
$stmt->execute();
$stmt->bind_result($prev_title, $prev_body, $prev_link);
$stmt->fetch();
$stmt->close();

//get updated data from inputs
$updated_title = $_POST["updated_title"];
$updated_body = $_POST["updated_body"];
$updated_link = $_POST["updated_link"];

//check session
if ($_POST['token']) {
    if(!hash_equals($_SESSION['token'], $_POST['token'])) {
        die("Request forgery detected");
    }
}

//make sure updated story is valid and update it in the database
if (strlen(trim($updated_body))) {
        $stmt2 = $mysqli->prepare("update stories set title=?, body=?, link=? where story_id=?");

        if(!$stmt2) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
    
        $updated_title = htmlentities($updated_title);
        $updated_body = htmlentities($updated_body);
        $updated_link = htmlentities($updated_link);
    
        $stmt2->bind_param('sssi', $updated_title, $updated_body, $updated_link, $story_id);
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
    <title>Edit Story Page</title>
</head>
<body>
    <form method="post">
        <p>Title: <input type="text" name="updated_title" value="<?php echo $prev_title;?>" size="65" required></p>
        <p><textarea rows="25" cols="100" placeholder="Insert story..." name="updated_body" required><?php echo $prev_body;?></textarea></p>
        <p>Link: <input type="text" name="updated_link" placeholder="Optional" value="<?php echo $prev_link;?>" size="65"></p>
        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
        <input type="submit" value="Post">
    </form>
</body>
</html>