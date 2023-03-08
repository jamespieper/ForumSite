<?php

//logout
session_destroy();

header("Location: login.php");
?>