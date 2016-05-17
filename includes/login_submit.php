<?php
	require_once 'functions.php';
	require_once 'password_compat.php';
	require_once "db.php";

	session_start();
	
	if (isset($_SESSION['login'])) {		
		die();
	}

	$error_msgs=""; // variable to store validation errors
	$info_msgs=""; // variable to store information messages
	
	if (!isset($_SESSION['login'])) {
		
		if (!isset($_POST['username']) || !isset($_POST['password'])) {
			$error_msgs = "Username and password are invalid";
		}
		else
		{
			// extract username and password values from posted values array
			$username=$_POST['username'];
			$password=$_POST['password'];

			// validate username format
			$username_valid = preg_match('/^[a-zA-Z0-9]{4,12}$/', $username) == 1;
			// validate password security
			$password_secure = ((strlen($password) >= 4) &&  // min length = 4 characters
								(strlen($password) <= 12) && // max length = 12 characters 
								(preg_match('/[0-9]/', $password)==1)); // contains at least 1 digit

			// sanitize values before executing db query
			$username = stripslashes($username);
			$password = stripslashes($password);
			$username = mysql_real_escape_string($username);
			$password = mysql_real_escape_string($password);
			
			if ($username_valid && $password_secure) {

				// get db connection from session
				$db = new dbcon();
				$db = $db->db();

				// db query which will select users with corresponding name and password
				$query_result = $db->query("SELECT * FROM Users WHERE Username='$username'");
				$user_exists = (mysqli_num_rows($query_result)>0);				

				if ($user_exists) {
					$row = $query_result->fetch_assoc();
					$hash = $row["PasswordHash"];
					$user_id = $row["UserID"];
					$account_locked = $row["ResetFlag"] == 1;
					$reset_password = $row["ResetPassword"];

					// before performing the regular login procedure,
					// check whether the account has been locked, in which case we need to check
					// for the reset password instead of the regular password.					
					if ($account_locked) {
						if (strcmp($password, $reset_password) === 0) {
							$_SESSION['login']=$row;
							$_SESSION['temporary_login']=true; // create a session variable which will indicate that although the user was logged in, they need to reset their password before doing anything else.
							echo create_json_response_redirect_string("change-password.php");
							die();
						}
						else {
							$error_msgs .= "This account has been locked.\nAn email with instructions on how to reset the account was sent to the registerd email address.\n";
						}	
					}
					else {
						if (password_verify($password, $hash)) {
							$_SESSION['login']=$row; // store user's credentials in the session object for future use			

							// create a good login attempt in the databse			
							$query_record_login_attempt = $db->query("INSERT INTO LoginAttempts (UserID, Time, Success) VALUES ('$user_id', NOW(), 1)");
							
							// rest the unsuccessful-login-attempt counter for the user
							$query_reset_login_attempt = $db->query("UPDATE Users SET LoginAttempts=0 WHERE UserID='$user_id'");

							echo create_json_response_redirect_string("index.php");
							die();		
						}
						else {
							// create a bad login attempt in the database
							$query_record_login_attempt = $db->query("INSERT INTO LoginAttempts (UserID, Time, Success) VALUES ('$user_id', NOW(), 0)");

							// increment the bad login attempts counter in the User's table
							$query_increment_login_attempt = $db->query("UPDATE Users SET LoginAttempts=LoginAttempts+1 WHERE UserID='$user_id'");

							$error_msgs .= "Username and password do not match" . "\n";

							// check how many unsuccessful login attempts were made in the last hour
							$query_login_attempts = $db->query("SELECT * FROM Users WHERE UserID='$user_id' AND LoginAttempts>3 AND (UNIX_TIMESTAMP(NOW())-(SELECT MAX(UNIX_TIMESTAMP(Time)) FROM LoginAttempts WHERE UserID='$user_id' AND Success=0) < 60)");
							$login_attempts_exceeded = mysqli_num_rows($query_login_attempts)>0;

							// if login_attempts_exceeded, lock the user out, and send reset password email
							if ($login_attempts_exceeded) {
								// 1. lock out the user by setting the ResetFlag in the Users table for that user
								// 2. we also need to generate a temporary reset password, and save it in the ResetPassword column in the Users table for that user.
								// 3. Finaly, we need to send an email to the user
								$temporary_reset_password = generate_temporary_reset_password();
								$query_lockout_user = $db->query("UPDATE Users SET ResetFlag=1, ResetPassword='$temporary_reset_password' WHERE UserID='$user_id'");
															
								$email = $row["Email"];
								$username = $row["Username"];

								// send reset email

								$email_result = send_reset_password_email($username, $email, $temporary_reset_password);

								if (!$email_result) $error_msgs .= "Error, password recovery email not sent, try again.\n";								

								$error_msgs .= "Login attempts exceeded!\nThis account has been locked.\n\nAn email with instructions on how to reset the account was sent to the registerd email address.\n";
							}
						}
					}
				} 
				else {
					$error_msgs .= "Username and password are invalid" . "\n";
				}
			}
			else {
				$error_msgs .=  (!$username_valid) ? "Username must be 4-12 characters and comprised of only letters and digits.\n" : "";
				$error_msgs .=  (!$password_secure) ? "Password must be at 4-12 characters and have at least 1 digit.\n" : "";
			}			
		}

		echo create_json_response_string($error_msgs, $info_msgs);
		die();
	}
	else {
		header('Location:index.php'); // redirect user to members area
		die();		
	}
?>