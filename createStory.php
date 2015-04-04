<!DOCTYPE html>
<html>
    <head>
        <?php
            session_start();
        ?>
        <title>Submit a Text Story | News Site</title>
    </head>
    <body>
        <h1>Submit a Story</h1>
        <form action="createStoryScript.php" method="POST">
            <p>
                    <label for="titleInput">Title</label><br>
                    <input type="text" name="title_text" id="titleInput">	
            </p>	
            <p>
                    <label for="linkInput">Link (optional)</label><br>
                    <input type="text" name="link_text" id="linkInput">	
            </p>	
            <p>
                    <label for="storyInput">Story Text (optional)</label><br>
                    <textarea name="story_text" id="storyInput"></textarea>	
            </p>	
            <p>
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" /> 
                    <!--To prevent CSRF attacks-->
                    <input type="submit" value="Submit">
            </p>
        </form>
    </body>
</html>
