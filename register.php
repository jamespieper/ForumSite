<?php

require 'database.php';

//grab input from fields
$username = $_POST["username"];
$password = $_POST["password"];
$reenter_password = $_POST["reenter"];

//var to see if username exists
$taken = false;

//makes sure every user input is valid
if ($username != NULL && $password != NULL && $reenter_password != NULL) {

    //grab existing usernames
    $stmt = $mysqli->prepare("select username from users");

    if(!$stmt) {
        printf("Query Prep Failed: %\n", $mysqli->error);
        exit;
    }

    $stmt->execute();

    $stmt->bind_result($registered_users);
    
    //see if taken
    while($stmt->fetch() && $taken == false) {
        if (strcmp($registered_users, $username) == 0) {
            $taken = true;
        }
    }

    $stmt->close();

    //if taken, then don't create the user
    if ($taken) {
        printf("Username taken");
    } 
    
    //if not, make sure password and reenter are the same
    else {
        if (strcmp($password, $reenter_password) == 0) {

            //and put user into database
            $stmt2 = $mysqli->prepare("insert into users (username, hash_pass) values (?, ?)");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }

            //hash up the password for secure storage in database
            $hash_pass = password_hash($password, PASSWORD_BCRYPT);

            $stmt2->bind_param('ss', $username, $hash_pass);
            $stmt2->execute();

            $stmt2->close();

            //redirect to the login page
            header("Location: login.php");
        } 
        
        //make sure passwords match
        else {
            printf("Passwords must match");
        }
        
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="register.css">    
    <title>Registration Page</title>
</head>
<body>
    <h1>Welcome</h1>
    <div>
    
    <form method="post">
        <p><input type="text" placeholder="Username" name="username" required></p>
        <p><input type="password" placeholder="Password" name="password" required></p>
        <p><input type="password" placeholder="Re-enter password" name="reenter" required></p>
        <p><input type="submit" value="Sign-up"></p>
    </form>

    <form action="login.php">
        <p><input type="submit" value="Already registered?"></p>
    </form>

    <form action="guest_login.php">
        <p><input type="submit" value="Guest Login"></p>
    </form>



    </div>
    
</body>
</html>