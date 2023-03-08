<?php

require 'database.php';

session_start();

//grab data from inputs
$story_id = $_GET["story_id"];
$confirm = $_POST["confirm"];

//check session
if ($_POST['token']) {
    if(!hash_equals($_SESSION['token'], $_POST['token'])) {
        die("Request forgery detected");
    }
}

//check if radio button is 'Yes'
if ($confirm == "Yes") {

    //if so delete story from database
    $stmt = $mysqli->prepare("delete from stories where story_id=?");
    if(!$stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }

    $stmt->bind_param('i', $story_id);
    $stmt->execute();
    $stmt->close();

    header("Location: home.php");
} 

//otherwise return to home
else if ($confirm == "No") {
    header("Location: home.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Story Deleting Page</title>
</head>
<body>
    <p>Are you sure you want to delete this story?</p>
    <form method="post">
        <p>
        <label><input type="radio" name="confirm" value="Yes">Yes</label>
        <label><input type="radio" name="confirm" value="No">No</label>
        </p>
        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
        <input type="submit" value="Submit">
    </form>
</body>
</html>