<?php 
    // This page is reached via simple link, and logs the user out of their session at the site

    session_start();
    
    // whether or not the user was logged in before, we just made a session, so end it.
    session_destroy();
    header("Location: home.php");
    exit;
?>
