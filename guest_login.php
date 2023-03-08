<?php

session_start();

//only guest user is null
$_SESSION['username'] = NULL;

$_SESSION['token'] = bin2hex(random_bytes(32));

header("Location: home.php");

?>