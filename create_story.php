<?php

require 'database.php';

session_start();

//grab data from inputs
$username = $_SESSION["username"];
$title = $_POST["title"];
$body = $_POST["body"];
$link = $_POST["link"];

//make sure session is the same
if($_POST['token']) {
    if(!hash_equals($_SESSION['token'], $_POST['token'])){
        die("Request forgery detected");
    }
}

//check to make sure title and body fields have entries
if ($title && strlen(trim($body))) {

    //if a link is provided
    if ($link) {
        $stmt = $mysqli->prepare("insert into stories (username, title, body, link) values (?, ?, ?, ?)");

        if(!$stmt) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
    
        $username = htmlentities($username);
        $title = htmlentities($title);
        $body = htmlentities($body);
        $link = htmlentities($link);
    
        $stmt->bind_param('ssss', $username, $title, $body, $link);
        $stmt->execute();
        $stmt->close();
    } 
    //if link not provided
    else {
        $stmt2 = $mysqli->prepare("insert into stories (username, title, body) values (?, ?, ?)");

        if(!$stmt2) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
    
        $username = htmlentities($username);
        $title = htmlentities($title);
        $body = htmlentities($body);
    
        $stmt2->bind_param('sss', $username, $title, $body);
        $stmt2->execute();
        $stmt2->close(); 
    }
    
    //return to home
    header("Location: home.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Story Page</title>
</head>
<body>
    <form method="post">
        <p>Title: <input type="text" name="title" size="65" required></p>

        <p><textarea rows="25" cols="100" placeholder="Insert story..." name="body" required></textarea></p>

        <p>Link: <input type="text" name="link" placeholder="Optional" size="65"></p>


        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
        <input type="submit" value="Post">
    </form>
</body>
</html>

