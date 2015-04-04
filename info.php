<!DOCTYPE html>
	<link rel="stylesheet" type="text/css" href="style.css" />
<html>
<head>
    <title>Logging in!</title>
</head>
<body>
    <?php
	// After registering, user will be redirected to this page with POST variables set 
	 require('database.php');
		
		// If no user specified, redirect to login page
        if(!isset($_POST['username'])) {
            header("Location: createuser.html");
            exit;
        }

       	if(!isset($_POST['userpassword'])) {
            header("Location: createuser.html");
            exit;
        }

	//inserts username and hashed password into newsite.USERS

	$username = $_POST['username'];
	$userpassword = $_POST['userpassword'];
 	$hashedpassword = crypt($userpassword);
     
        echo "<p>Logging in!\n</p>";

        //Checks newsite.USERS table to see if username exists already

		//if not in system, will create a new entry into the USERS table
		$stmt = $mysqli->prepare("select hashedpassword from USERS where username=?");
		
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		 
		$stmt->bind_param('s', $username);
		 
		$stmt->execute();
		
		// make sure that there is a row in the result (that is, that username even exists in system).
		// if not, back to login screen after delay
		$stmt->store_result();
        if($stmt->num_rows != 1) { // since we're searching based on primary key, there can only be 1
            $mysqli->prepare("insert into USERS (username, hashedpassword) values (?, ?)");
           
			$stmt = $mysqli->prepare("insert into USERS (username, hashedpassword) values (?, ?)");
		
			if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
			}
 
			$stmt->bind_param('ss', $username, $hashedpassword );
 
			$stmt->execute();
		
			$stmt->close();

			printf("<p>Welcome, %s!</p>",htmlentities($username));
       		session_start();
       		$_SESSION['user'] = $username;
            $_SESSION['token'] = substr(md5(rand()), 0, 10);

        	header( "refresh:2; url=home.php" );

       		echo 'Please wait to be redirected... If page does not load, click <a href="home.php">here</a>.';
			
            exit; 
        }
		
		//if already in system, will redirect to createuser.html page

		else{	
			header( "refresh:2; url=createuser.html" );
       		echo 'Username Taken, You\'ll be redirected in about 5 secs. If not, click <a href="createuser.html">here</a>.';
        		}

		$destination_username = $_POST['dest'];
		$amount = $_POST['amount'];
		if($_SESSION['token'] !== $_POST['token']){
			die("Request forgery detected");
		}
		$mysqli->query(/* perform transfer */);


    ?>
</body>
</html>