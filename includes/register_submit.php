<?php
	require_once 'password_compat.php';
	require_once 'functions.php';
	require_once 'db.php';

	session_start();

	if (isset($_SESSION['login'])) {		
		die();
	}

	$db = new dbcon();
	$db = $db->db();

	$error_msgs = ""; // error messages	
	$info_msgs = ""; // info messages	
	
	if (mysqli_connect_errno($db)) {
		$error_msgs .= "Failed to connect to MySQL: " . mysqli_connect_error();
	}	
	else {
		// trim all posted values
		array_filter($_POST, 'trim_value');
		
		$employee_number = $_POST['employee_number']; 
		$name = $_POST['username']; 
		$email = $_POST['email'];
		$password =$_POST['password']; 
		$retype_password = $_POST['retype_password']; 

		// Check if e-mail address syntax is valid or not
		$email = filter_var($email, FILTER_SANITIZE_EMAIL); // Sanitizing email(Remove unexpected symbol like <,>,?,#,!, etc.)
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$error_msgs .= "This email address is invalid\n";
		}
		else {	
			// perform validation of input
			$employee_number_valid = preg_match('/^[0-9]{4}$/', $employee_number) == 1;
			$username_valid = preg_match('/^[a-zA-Z0-9]{4,12}$/', $name) == 1;
		
			$email_exists = (mysqli_num_rows($db->query("SELECT * FROM users WHERE Email='$email'")) > 0);
			$name_exists = (mysqli_num_rows($db->query("SELECT * FROM users WHERE Username='$name'")) > 0);
			$employee_number_exists = (mysqli_num_rows($db->query("SELECT * FROM users WHERE EmployeeNumber='$employee_number'")) > 0);
			
			$email_valid = validate_email_format($email);
			// check whether password conforms to security policy
			$password_secure = validate_password_security($password);
			
			// check whether passwords match
			$passwords_match = strcmp($password, $retype_password) === 0;
			
			if($employee_number_valid && !$email_exists && !$name_exists && $username_valid &&
				!$employee_number_exists &&	$passwords_match && 
				$password_secure && $email_valid) {
	
				// genereate a salted password hash. We are using BCRYPT, a proven and secure implementation for this sort of thing. This functionality is provided in a compatibility library - password_compat.php
				$hash = password_hash($password, PASSWORD_BCRYPT);		
				
				// mysqli real escape values before writing to db
				//array_filter(array($employee_number, $name, $email), 'mysqli_escape_value');
								
				$query_register_user = $db->query("INSERT INTO Users (EmployeeNumber, Username, Email, PasswordHash) VALUES ('$employee_number', '$name', '$email', '$hash')"); 
				if(!$query_register_user) {
					$error_msgs .= "Oops, something went wrong. Please wait a moment and try again.\n";
				}
			} 
			else {
				$error_msgs .=  (!$employee_number_valid) ? "Employee number must be 4 digits long.\n" : "";
				$error_msgs .=  (!$username_valid) ? "Username must be 4-12 characters and comprised of only letters and digits.\n" : "";
				$error_msgs .=  (!$email_valid) ? "Email format is invalid.\n" : "";
				$error_msgs .=  ($employee_number_exists) ? "Employee number is in use.\n" : "";
				$error_msgs .=  ($name_exists) ? "Username is already in use.\n" : "";
				$error_msgs .=  ($email_exists) ? "Email address is in use\n" : "";
				$error_msgs .=  (!$password_secure) ? "Password must be at 4-12 characters and have at least 1 digit.\n" : "";
				$error_msgs .=  ($password_secure && !$passwords_match) ? "Passwords do not match.\n" : "";
			}	
		}
		mysqli_close ($db);		
	}
	echo create_json_response_string($error_msgs, $info_msgs);
	
	
?>