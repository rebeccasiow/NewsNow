<?php
// Script to connect us to database, for use on other pages
// mostly borrowed from class wiki example
$mysqli = new mysqli('localhost','newsiteadmin','newsiteadmin','newsite');

if($mysqli->connect_errno) {
    printf("Connection Failed: %s\n", $mysqli->connect_error);
    exit;
}

?>