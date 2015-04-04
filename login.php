<!DOCTYPE html> 
<?php
<head>
	<title>Logging in... | News Site</title>
</head>
<body>
	<?php
		// a script called from login.html, with post variables called username and userpassword.
		require('database.php');
		
		// if a required post variable does not exist, go back to login form
		if(!isset($_POST['username']) || !isset($_POST['userpassword'])) {
            header("Location: login.html");
            exit;
        }
		
		$username = (string)$_POST['username'];
		$passwd_guess = (string)$_POST['userpassword'];
		
		// check database
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
            echo "Your username is incorrect.\n";
			header( "refresh:5; url=login.html" );
            exit; // no need to write anything else (TODO: account for how this makes page validate wrong)
        }
		
		$stmt->bind_result($hashed_pass);
		$stmt->fetch();
		
		
		if(crypt($passwd_guess, $hashed_pass)==$hashed_pass) {
			// login success! Set up session
			session_start();
			$_SESSION['user'] = $username;
			$_SESSION['token'] = substr(md5(rand()), 0, 10); // generate a 10-character random string
			
			header("Location: home.php");
			
		} else {
			// wrong password
			echo "Your password is incorrect.\n";
			header( "refresh:5; url=login.html" );
            exit;
		}

		$destination_username = $_POST['dest'];
		$amount = $_POST['amount'];
		if($_SESSION['token'] !== $_POST['token']){
			die("Request forgery detected");
		}
		
	?>
</body>
?>