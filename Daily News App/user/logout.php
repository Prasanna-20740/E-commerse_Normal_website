<?php

session_start();


// Destroy all session data

$_SESSION = [];


// Destroy session

session_destroy();


// Redirect to home

header("Location: ../index.php");

exit;

?>