<?php
    // A script to re-submit an edited story to the database.
    // Requires that title_text, link_text, and post_text be $_POST variables.
    
    require('database.php');
    
    session_start();
    // If token's messed up, get out of here
    if(!isset($_POST['token']) || $_SESSION['token'] !== $_POST['token']){
        die("Request forgery detected");
    }
    
    // If no story is selected, maybe they just want to make a new story?
    if(!isset($_POST['story_id'])) {
        header("Location: createStory.php");
        exit;
    }
    
    // If vital data is missing, leave
    if(!isset($_POST['title_text'])) {
        // there has to be a title. Send them back to the creation page.
        header("Location: editStory.php?story_id=$_POST");
        exit;
    }
    
    // If no user logged in, leave
    if(!isset($_SESSION['user'])) {
        header("Location: home.php");
        exit;
    }

    $username = $_SESSION['user'];
    $story_id = (int)$_POST['story_id'];
    $story_text = (string)$_POST['story_text'];
    $title_text = (string)$_POST['title_text'];
    $link_text = (string)$_POST['link_text'];
    
    // TODO: should filter all these strings more
    
    // Check the database to make sure this is OP editing the post
    $stmt = $mysqli->prepare("select username from CONTENT where id=$story_id;");
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
    $stmt->execute();
    
    $stmt->store_result();
    //echo $stmt->num_rows;
    if($stmt->num_rows != 1) { // since we're searching based on primary key, there can only be 1
        echo "No such story exists.\n";
        exit; // no need to write anything else (TODO: account for how this makes page validate wrong)
    }
    
    $stmt->bind_result($poster);
    $stmt->fetch();
    $stmt->close();
    
    // non-OP is thrown out
    if($poster != $_SESSION['user']) {
        echo "This is not your post to edit. Redirecting to the post\n";
        header( "refresh:5; url=story.php?story_id=$id" );
        exit;
    }
        
    // perform the update
    $stmt = $mysqli->prepare("update CONTENT set text=?, title=?, link=? where id=?;");
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
     
    $stmt->bind_param('sssi', $story_text, $title_text, $link_text, $story_id);
     
    $stmt->execute();
    $stmt->close();

    // Update complete. Drop user off at edited story page
    header("Location: story.php?story_id=$story_id");
?>
