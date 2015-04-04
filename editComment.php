<!DOCTYPE html>
<html>
    <?php

 /*
 * Use GET variable to prepare comments for deletion. 
 * For deleted comments, update table on server to changed 'deleted' column to 1 for boolean. 
 * 
 * Show a message 'This comment has been deleted.' in place of deleted comments
 * Use an if statement somewhere to display message if 1
 */
 
//start session
        // A page to make a story editable. Takes a GET var story_id and lets the user edit it (if they are OP)
        require('database.php');
        

        
        // If no story is selected, maybe they just want to make a new story?
        if(!isset($_GET['comment_id'])) {
            header("Location: home.php");
            //echo "No story selected";
            exit;
        }
        
        // query database to find out OP for this story_id, and make sure that's who's logged in,
        // and also to get the default values to start from on the form
        $id = (int)$_GET['comment_id'];
        
        // If no user logged in, leave
        session_start();
       


        $stmt = $mysqli->prepare("select post_id, comment_text,username,deleted from COMMENTS where id=$id;");
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
        
        $stmt->bind_result($post_id,$comment_text,$username,$deleted);
        $stmt->fetch();
        $stmt->close();

        if(!isset($_SESSION['user'])) {
            header("Location: story.php?story_id=$post_id");
            //echo "No user logged in";
            exit;
        }
        // logic to make sure this is a valid edit
        if($username != $_SESSION['user']) {
            echo "This is not your comment to edit. Redirecting to the post\n";
            header( "refresh:5; url=story.php?story_id=$post_id" );
            exit;
        }
        
    ?>
    <head>
        <title>Edit Story | News Site</title>
    </head>
    <body>
        <h1>Submit a Story</h1>
        <form action="editCommentResubmitScript.php" method="POST" id="editCommentForm">
            <p>
                    <label for="commentInput">Comment Text</label><br>
                    <textarea name="comment_text" id="commentInput" form="editCommentForm"><?php echo htmlspecialchars($comment_text); ?></textarea>	
            </p>	
            <p>
                    <input type="hidden" name="username" value="<?php echo $username;?>" />
                    <!-- Pass along which comment this is that we're changing -->
                    <input type="hidden" name="post_id" value="<?php echo $post_id;?>" />
                    
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" /> 
                    <!--To prevent CSRF attacks-->
                    <input type="submit" value="Submit">
            </p>
        </form>
    </body>
</html>
