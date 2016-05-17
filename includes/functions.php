 <?php

require 'phpmailer/PHPMailerAutoload.php';

function load_user_session($username) {
	return 1;
}

// given a string of error messages (separated by new line characters)
// this function creates and returns a json string representing the same error messages.
// The advantage of returning a json string is that Javascript can easily parse it into an Object,
// extract all error messages and present them to the user.
// The structure of the response json string is as follows: 
// "{ status: "OK" or "ERROR", messages: ["error_msg_1","err_msg_2", ... ] }"
// $error_msgs parameter contains a list of 
// the parameter info_msgs works the same way, are are simply added to the json object's array
function create_json_response_string($error_msgs, $info_msgs) {		
	$response = "{\"status\":" . (($error_msgs == "") ? "\"OK\"," : "\"ERROR\",") . "\"messages\": [";
	
	$all_msgs = $error_msgs . $info_msgs; // combine erros and info msgs into one string
	
	if ($all_msgs != "") { // add all errors and info msgs to json response
		$lines = explode("\n", $all_msgs);
		foreach ($lines as $line) {
			if (trim($line) == "") continue;
			$response .= "\"" . trim($line) . "\", ";			
		}
		// remove last comma (,) that was added in the loop and close json
		$response = substr($response, 0, strlen($response)-2) . "]}" ;		
	}
	else { $response .= "]}"; }
	
	return $response;
}		
	
// trims a given value (warning, value passed by reference, indicated by & symbol)
function trim_value(&$value) {
	$value = trim($value); 		
}	

// mysqli-real-escapes a given value (warning, value passed by reference, indicated by & symbol)
function mysqli_escape_value($db, &$value) {
	$value = mysqli_real_escape_string($db, $value); 		
}	

function create_json_response_redirect_string($url) {
	return '{ "status": "OK", "redirect": "' . $url . '"}';
}

// this email is sent when user clicks 'fogot-password' link
function send_password_recovery_email($username, $email, $temporary_reset_password) {
	$subject = 'Bug Tracker - Password Recovery';

	$body = 	"Hello " . $username . ",\n\n" .
				"Your account was locked and your password reset.\n\n" . 
				"The reason for this is that, someone requested a password recovery email for your Bug Tracker account." .
				"To unlock your account, you will need to log in with a temporary password provided below." .
				"After loggin in, you will need to set a new password." .
				"\n\n" .
				"Your temporary reset password is: " . $temporary_reset_password . 
				"\n\n" .
				"Regards, \n\nBug Tracker Team";

	return send_mail($username, $email, $subject, $body);
}

// this email is sent when the user gets locked out of their account from too many login attempts.
function send_reset_password_email($username, $email, $temporary_reset_password) {
	$subject = 'Bug Tracker - Account was Locked';

	$body = 	"Hello " . $username . ",\n\n" .
				"For security reasons, your Bug Tracker account was locked and your password reset." .
				"\n\n" .
				"The reason for this is that, you or someone else, attempted to unsuccessfully log " . 
				"in and exceeded the maximum allowed number of loggin attempts. " . 
				"We are sending you a temporary reset password which you may use to log in. " . 
				"You will be asked to change you password after you log in." .
				"\n\n" .
				"Your temporary reset password is: " . $temporary_reset_password . 
				"\n\n" .
				"Regards, \n\nBug Tracker Team";

	return send_mail($username, $email, $subject, $body);
}

// generic helper method to send email
function send_mail($receipient_name, $to_address, $subject, $body) {
	$mail = new PHPMailer;
	//$mail->SMTPDebug = 3;                               // Enable verbose debug output
	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->Host = 'aspmx.l.google.com';  					// Specify main and backup SMTP servers
	$mail->Port = 25;                                    // TCP port to connect to
	$mail->From = 'services@bugtracker.com';
	$mail->FromName = 'Bug Tracker Team';
	$mail->addAddress($to_address, $receipient_name);     // Add a recipient
	$mail->Subject = $subject;
	$mail->Body    = $body;
	return $mail->send();
}

// validates a given password against the password policy
function validate_password_security($password) {
	return ((strlen($password) >= 4) &&  // min length = 4 characters
			(strlen($password) <= 12) && // max length = 12 characters 
			(preg_match('/[0-9]/', $password) == 1)); // contains at least 1 digit
}

// function helps validate an email formatted string using a regular expression
function validate_email_format($email) {
	return preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $email);
}

// this function generates a 4 digit reset password. For testing however, it returns a constant allways.
function generate_temporary_reset_password() {
	return "1111";
}

?>