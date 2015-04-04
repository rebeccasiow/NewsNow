<!DOCTYPE html>
<html>
    <?php
        require('database.php');
        // a script where we get the story's title from the database based on the
        // GET var for story id, get the story's contents, and all that
        
        // query database based on $_GET['story_id']
        if(!isset($_GET['story_id'])) {
            header("Location: home.php"); // no id: send them to home. Nothing to see here.
            exit;
        }
        
        // we know that they a story is selected. Query for it.
        $id = (int)$_GET['story_id'];
        
        //echo "Story with id $id selected\n";
        
        // TODO: is this filtered heavily enough?
        $stmt = $mysqli->prepare("select username,text,link,title,posted from CONTENT where id=$id;");
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
        
        $stmt->bind_result($poster,$story_text,$link,$title,$post_time);
        $stmt->fetch();
        $stmt->close();
    
    ?>

    <head><title><?php echo htmlspecialchars($title)." | News Site"; ?></title></head>
    
    <body>
        <div id="topBar">
            <!-- Sign in/out link, link to your account info if signed in, etc -->
            <?php
                
                session_start();
                if(isset($_SESSION['user'])) {
                    // If logged in: display greeting and logout link.
                    
                    echo "<p>Hello, ".$_SESSION['user']."! <a href=logout.php>Logout</a>\n";
                    
                } else {
                    // If not logged in: display links to go login or register
                    
                    echo "<a href=login.html>Login</a> | <a href=createuser.html>Register</a>\n";
                    
                }
                echo "<br><a href=home.php>Go back to homepage</a>\n";
            ?>
        </div>
        
        <div id="storyDiv">
            <!-- Title, with story underneath it -->
            <?php
                // set up the title, as a link if that field is filled
                $disp_title = htmlspecialchars($title);

                if(!is_null($link)) {
                    $disp_title = "<a href=".htmlentities($link).">".$disp_title."</a>";
                }
                
                // generate edit & delete links if this is OP
                $editLink = "";
                if($_SESSION['user']==$poster) {
                    $editLink = "<a href=editStory.php?story_id=$id>edit</a>";
                }
                
                // set up body text, with short message if there is none
                $disp_body = is_null($story_text) ? htmlentities("< No story text >") : htmlspecialchars($story_text);
                printf("<h1>%s</h1>\n<h5>Posted by %s at %s %s</h5>\n<p>\n%s</p>\n",
                       $disp_title,htmlspecialchars($poster),
                       htmlspecialchars($post_time),$editLink,$disp_body);
            ?>
            <hr>
        </div>
        
        <?php
            if(isset($_SESSION['user'])) {
                $token = $_SESSION['token'];
            echo <<<EOD
                <div id="commentBox">
                    <!-- TODO: must prevent people not logged in from doing this -->
                    <form action="createCommentScript.php" method="POST">	
                        <p>
                                <label for="commentInput">Write a comment:</label><br>
                                <textarea name="comment_text" id="commentInput"></textarea>	
                        </p>	
                        <p>
                                <input type="hidden" name="post_id" value=$id>
                                <input type="hidden" name="token" value="$token" /> 
                                <!--To prevent CSRF attacks-->
                        
                                <input type="submit" value="Submit">
                        </p>
                    </form>
                    
                </div>
EOD;
// this unfortunately must be unindented for HEREDOC syntax to work
            } else {
                echo "<p>Only logged in users can comment on posts.</p>\n";
            }
        ?>
        
        <div id="commentsDiv">
            <!-- List of comments -->
            <?php
                // script to fetch all this story's comments and display them formatted
                $stmt = $mysqli->prepare("select comment_text,username,posted from COMMENTS where post_id = $id;");
                if(!$stmt){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
                $stmt->execute();
                
                $stmt->store_result();
                
                if($stmt->num_rows == 0) {
                    echo "There are no comments on this story. Be the first!\n\n";
                }
                
                $stmt->bind_result($comment_text,$commenter,$comment_post_time);
                
                echo "<ul>\n";
                while($stmt->fetch()) {
                    // edit and delete comments if OP
                    $deleteComment = "";
                    $editComment = "";

                    if($_SESSION['user']==$commenter) {
                        $deleteComment = "<a href=deleteComments.php?comment_id=$id>delete</a>";
                        $editComment = "<a href=editComment.php?comment_id=$id>edit</a>";

                    }
                    
                    printf("<li>Comment by %s at %s %s %s</li>\n<p>\n%s\n</p>",
                           htmlspecialchars($commenter),
                           htmlspecialchars($comment_post_time),
                            $editComment, $deleteComment,
                           htmlspecialchars($comment_text)
                           );
                }
                echo "</ul>\n";

            ?>
        </div>
        
    </body>
</html>