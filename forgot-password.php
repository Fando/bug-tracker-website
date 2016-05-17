<?php
	require_once "includes/functions.php";
	require_once "includes/db.php";

	session_start();
	
	if (isset($_SESSION['login'])) {	
		header("Location:index.php");	
		die();
	}

	$error_msgs = "";
	$info_msgs = "";
	$temporary_login = isset($_SESSION['temporary_login']);
	
	// if the form was submitted, execute this code
	if (isset($_POST['btn-submit'])) {
		$email = $_POST["txt-email"];
		$email_valid = validate_email_format($email);

		// establish database connection
		$db = new dbcon();
		$db = $db->db();

		$query_result = $db->query("SELECT * FROM Users WHERE Email='$email'");
		$email_exists = (mysqli_num_rows($query_result) > 0);
		$row = $query_result->fetch_assoc();
		$username = $row["Username"];

		if ($email_valid && $email_exists) {
			// 1. lock out the user by setting the ResetFlag in the Users table for that user
			// 2. we also need to generate a temporary reset password, and save it in the ResetPassword column in the Users table for that user.
			// 3. Finaly, we need to send an email to the user
			$temporary_reset_password = generate_temporary_reset_password();
			$query_lockout_user = $db->query("UPDATE Users SET ResetFlag=1, ResetPassword='$temporary_reset_password' WHERE Email='$email'");
			
			// send reset email
			$email_result = send_password_recovery_email($username, $email, $temporary_reset_password);

			if ($email_result) $info_msgs .= "Recovery email sent successfully!\n";
			else $error_msgs .= "Oops, something went wrong, recovery email was not sent, try again.\n";
		}
		else {
			$error_msgs .=  (!$email_valid) ? "Email format is invalid.\n" : "";
			$error_msgs .=  (!$email_exists) ? "Email not found.\n" : "";
		}

	}
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Forgot password</title>
	<?php include "includes/css.php" ?>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<div id="content" class="content">
		<h1>Forgot Password</h1><br/>
		
		<form class="small-form" action="" id="frm-forgot-password" name="frm-forgot-password" method="post" role="form">
			<div class="form-group">
				<?php if ($error_msgs != "") : ?>
					<p id="response-label" class="form-label red"><?php echo $error_msgs; ?></p>
				<?php endif; ?>
				<?php if ($info_msgs != "") : ?>
					<p id="response-label" class="form-label green"><?php echo $info_msgs; ?></p>
				<?php endif; ?>
			</div>
			<p>Enter the email address you used to register for an account and a reset password will be sent to you.</p>
			<div class="form-group">
				<input id="txt-email" name="txt-email" class="form-control" type="email" placeholder="Email">
			</div>				
			<div class="form-group">
				<input id="btn-submit" name="btn-submit" class="btn btn-default" type="submit" value="Send Reset Password">
				<input id="btn-cancel" name="btn-cancel" class="btn" type="button" value="Back to Login" onclick="window.location='login.php'">
			</div>
		</form>			
		
	</div>		
	<?php include "includes/footer.php"; ?>
</body>
</html>