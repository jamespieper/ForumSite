<?php

require 'database.php';

session_start();

$username = $_SESSION["username"];

//variable keeps track of whether to show certain edit, delete, etc. features
$button_type;

//checks to see if we are using the guest login (see guest_login.php), if so we want to hide features
if ($username == NULL) {
    $button_type = "hidden";
} else {
    $button_type = "submit";
}

echo '<h1 style="text-align:center">News Forum</h1>';
echo '<br>';

// query to get stories from database
$stmt = $mysqli->prepare("select story_id, username, title from stories order by title");
if(!$stmt) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}

$stmt->execute();
$stmt->bind_result($story_id, $writer, $title);


//print each one out one at a time
while($stmt->fetch()) {
    echo '<div class="story_panel">';
    echo "Title: <a href='view_story.php?story_id=$story_id'>" . htmlentities($title) . "</a>". '<br>';
    echo "By: " . htmlentities($writer) . '<br>';
    echo '</div>';

    echo '<br>';
}

$stmt->close();

//check session
if ($_POST['token']) {
    if(!hash_equals($_SESSION['token'], $_POST['token'])) {
        die("Request forgery detected");
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="home.css">
    <title>Home Page</title>
</head>
<body>
    

    <form style="text-align: center" action="create_story.php" method="post">
        <p><input type="<?php echo $button_type; ?>" value="Write a story"></p>
        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
    </form>

    <form style="text-align: center" action="logout.php">
        <p><input type="submit" value="Logout"></p>
    </form>
    
</body>
</html>