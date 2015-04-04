<?php
    // A script to submit a story to the database.
    // Requires that title_text, link_text, and post_text be $_POST variables.
    
    require('database.php');
    
    session_start();
    if(!isset($_POST['token']) || $_SESSION['token'] !== $_POST['token']){
        die("Request forgery detected");
    }
    
    // If vital data is missing, leave
    if(!isset($_POST['title_text'])) {
        // there has to be a title. Send them back to the creation page.
        header("Location: createStory.php");
        exit;
    }
    
    session_start();
    
    // If no user logged in, leave
    if(!isset($_SESSION['user'])) {
        header("Location: createStory.php");
        exit;
    }
    
    $username = $_SESSION['user'];
    $story_text = (string)$_POST['story_text'];
    $title_text = (string)$_POST['title_text'];
    $link_text = (string)$_POST['link_text'];
    
    // TODO: should filter all these strings more
    
    // perform the insert
    $stmt = $mysqli->prepare("insert into CONTENT (username, text, title, link) values (?, ?, ?, ?)");
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
     
    $stmt->bind_param('ssss', $username, $story_text, $title_text, $link_text);
     
    $stmt->execute();
    $stmt->close();
    
    // Insert complete. Drop user off at home page (where their new story is at the top)
    header("Location: home.php");
?>
