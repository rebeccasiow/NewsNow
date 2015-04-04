<!DOCTYPE html>
<html>
    <?php
        // A page to make a story editable. Takes a GET var story_id and lets the user edit it (if they are OP)
        require('database.php');
        

        
        // If no story is selected, maybe they just want to make a new story?
        if(!isset($_GET['story_id'])) {
            header("Location: createStory.php");
            //echo "No story selected";
            exit;
        }
        
        // query database to find out OP for this story_id, and make sure that's who's logged in,
        // and also to get the default values to start from on the form
        $id = (int)$_GET['story_id'];
        
        // If no user logged in, leave
        session_start();
        if(!isset($_SESSION['user'])) {
            header("Location: story.php?story_id=$id");
            //echo "No user logged in";
            exit;
        }
        
        $stmt = $mysqli->prepare("select username,text,link,title from CONTENT where id=$id;");
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
        
        $stmt->bind_result($poster,$story_text,$link,$title);
        $stmt->fetch();
        $stmt->close();
        
        // logic to make sure this is a valid edit
        if($poster != $_SESSION['user']) {
            echo "This is not your post to edit. Redirecting to the post\n";
            header( "refresh:5; url=story.php?story_id=$id" );
            exit;
        }
        
    ?>
    <head>
        <title>Edit Story | News Site</title>
    </head>
    <body>
        <h1>Submit a Story</h1>
        <form action="editStoryResubmitScript.php" method="POST" id="editStoryForm">
            <p>
                    <label for="titleInput">Title</label><br>
                    <input type="text" name="title_text" id="titleInput" value="<?php echo htmlspecialchars($title); ?>">	
            </p>	
            <p>
                    <label for="linkInput">Link (optional)</label><br>
                    <input type="text" name="link_text" id="linkInput" value="<?php echo htmlspecialchars($link); ?>">	
            </p>	
            <p>
                    <label for="storyInput">Story Text (optional)</label><br>
                    <textarea name="story_text" id="storyInput" form="editStoryForm"><?php echo htmlspecialchars($story_text); ?></textarea>	
            </p>	
            <p>
                    <input type="hidden" name="story_id" value="<?php echo $id;?>" />
                    <!-- Pass along which story this is that we're changing -->
                    
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" /> 
                    <!--To prevent CSRF attacks-->
                    <input type="submit" value="Submit">
            </p>
        </form>
    </body>
</html>
