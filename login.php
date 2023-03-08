<?php
// This is a *good* example of how you can implement password-based user authentication in your web application.

require 'database.php';

// Use a prepared statement
$stmt = $mysqli->prepare("SELECT COUNT(*), username, hash_pass FROM users WHERE username=?");

// Bind the parameter
$user = $_POST['username'];
$stmt->bind_param('s', $user);
$stmt->execute();

// Bind the results
$stmt->bind_result($cnt, $username, $pwd_hash);
$stmt->fetch();

$pwd_guess = $_POST['password'];
// Compare the submitted password to the actual password hash

if($cnt == 1 && password_verify($pwd_guess, $pwd_hash)){
    session_start();
	// Login succeeded!
	$_SESSION['username'] = $username;
    $_SESSION['token'] = bin2hex(random_bytes(32));
	// Redirect to your target page
    header("Location: home.php");
} else{
	// Login failed; redirect back to the login screen
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="register.css">
    <title>Login Page</title>
</head>
<body>
    <h1>Welcome</h1>

    <div>

    <form method="post">
        <p><input type="text" placeholder="Username" name="username" required></p>
        <p><input type="password" placeholder="Password" name="password" required></p>
        <p><input type="submit" value="Login"></p>
    </form>

    <form action="register.php">
        <p><input type="submit" value="Register"></p>
    </form>

    <form action="guest_login.php">
        <p><input type="submit" value="Guest Login"></p>
    </form>

    </div>
    
</body>
</html>