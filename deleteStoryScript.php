<?php
    // A script to delete a post, as long as this is OP.
    // Requires that story_id be $_GET variable.
    
    require('database.php');
    
    session_start();
    
    // If no story is selected, nothing to do here
    if(!isset($_GET['story_id'])) {
        //header("Location: home.php");
        echo "no story_id";
        exit;
    }
    
    // If no user logged in, leave
    if(!isset($_SESSION['user'])) {
        //header("Location: home.php");
        echo "no one logged in";
        exit;
    }

    $username = $_SESSION['user'];
    $id = (int)$_GET['story_id'];

    // Check the database to make sure this is OP editing the post
    $stmt = $mysqli->prepare("select username from CONTENT where id=$id;");
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
        echo "This is not your post to delete. Redirecting to the post\n";
        header( "refresh:5; url=story.php?story_id=$id" );
        exit;
    }
        
    // perform the update
    $stmt = $mysqli->prepare("update CONTENT set deleted=1 where id=?;");
    if(!$stmt){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }
     
    $stmt->bind_param('i', $id);
     
    $stmt->execute();
    $stmt->close();

    // Update complete. Drop user off at homepage
    header("Location: home.php");
?>
