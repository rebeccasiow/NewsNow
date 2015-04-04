<?php
    // A script to submit a comment to the database.
    // Requires that comment_text, username, and post_id be set _POST variables.
    
    require('database.php');
    
    session_start();
    if(!isset($_POST['token']) || $_SESSION['token'] !== $_POST['token']){
        die("Request forgery detected");
    }
    
    // If vital data is missing, leave
    if(!isset($_POST['post_id']) || !is_numeric($_POST['post_id']) || !isset($_POST['comment_text'])) {
        // We are missing vital information to make a comment.
        // Go back to the post page if possible, and if not, to home
        if(isset($_POST['post_id']) && is_numeric($_POST['post_id'])) {
            echo "bad text";
            header("Location: story.php?story_id=".(int)$_POST['post_id']);
            exit;
        } else {
            // we don't know how we got here; go home
            echo "bad post_id".$_POST['post_id'];
            header("Location: home.php"); // no id: send them to home. Nothing to see here.
            exit;
        }
    }
    
    session_start();
    
    // If no user logged in, leave
    if(!isset($_SESSION['user'])) {
        header("Location: story.php?story_id=".$_POST['post_id']);
        exit;
    }
    
    $username = $_SESSION['user'];
    $comment_text = (string)$_POST['comment_text'];
    $post_id = (int)$_POST['post_id'];
    
    // TODO: should filter the comment_text more
    
    // perform the insert
    $stmt = $mysqli->prepare("insert into COMMENTS (post_id, comment_text, username) values (?, ?, ?)");
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
     
    $stmt->bind_param('iss', $post_id, $comment_text, $username);
     
    $stmt->execute();
    $stmt->close();
    
    // Insert complete. Drop user off at story page they were at
    header("Location: story.php?story_id=".$_POST['post_id']);
?>