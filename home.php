<!DOCTYPE html>
<html>
    <head>
        <title>Home | News Site</title>
        
    </head>
    
    <body>
        
        <div id="topBar">
            <!-- login, logout, displaying username and stuff -->
            <h1> News Site </h1>
            <?php
                
                session_start();
                if(isset($_SESSION['user'])) {
                    // If logged in: display greeting and logout link.
                    
                    echo "<p>Hello, ".$_SESSION['user']."! <a href=logout.php>Logout</a>\n";
                    
                } else {
                    // If not logged in: display links to go login or register
                    
                    echo "<a href=login.html>Login</a> | <a href=createuser.html>Register</a>\n";
                    
                }
                echo "<br><a href=createStory.php>Submit a Story</a>\n";
            ?>

        </div>
        
        <div id="storiesDiv">
            <?php
                require('database.php');
                
                $stmt = $mysqli->prepare("select id,username,title,posted,deleted from CONTENT order by posted desc;");
                if(!$stmt){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
                $stmt->execute();
                
                //$stmt->store_result();
                $stmt->bind_result($story_id,$poster,$title,$post_time,$deleted);
                
                // For each story/link we find 
                while($stmt->fetch()) {
                    
                    // If the user wants the current story gone, respect that and don't show on page
                    if($deleted) continue;
                    
                    // generate edit & delete links if this is OP
                    $editLink = "";
                    if($_SESSION['user']==$poster) {
                        $editLink = "<a href=editStory.php?story_id=$story_id>edit</a> ".
                            "<a href=deleteStoryScript.php?story_id=$story_id>delete</a>";
                    }
                    
                    echo "<h3><a href='story.php?story_id=$story_id'>".htmlspecialchars($title)."</a></h3>\n";
                    echo "<p>Posted by ".htmlspecialchars($poster)." at ".htmlspecialchars($post_time)." $editLink</p>\n";
                }
            ?>
            <!-- list of links to stories using their titles and all that -->
            
        </div>
        
        
    </body>
</html>