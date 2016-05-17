<?php 
	require_once "includes/functions.php";
	require_once "includes/password_compat.php";
	require_once "includes/db.php";

	session_start();

	$error_msgs = "";
	$info_msgs = "";

	if (!isset($_SESSION['login'])) {		
		header('Location:login.php');
		die();
	}

	$temporary_login = isset($_SESSION['temporary_login']);

	if (isset($_POST['btn-submit'])) {

		// get posted password values and validate them
		$old_password = $_POST["txt-old-password"];
		$new_password = $_POST["txt-new-password"];
		$retype_new_password = $_POST["txt-retype-new-password"];

		$new_password_secure = validate_password_security($new_password);
		$new_passwords_match = strcmp($new_password, $retype_new_password) === 0;
		$old_and_new_passwords_different = strcmp($old_password, $new_password) != 0;

		$old_passwords_match = true;
		if (!$temporary_login) { // check for old password match if we ar not temporarily logged in
			$old_hash = $_SESSION['login']['PasswordHash'];
			$old_passwords_match = password_verify($old_password, $old_hash);
		}

		if ($new_passwords_match && $new_password_secure && $old_passwords_match && $old_and_new_passwords_different) {
			$new_hash = password_hash($new_password, PASSWORD_BCRYPT);
			
			$db = new dbcon();
			$db = $db->db();

			// update password in database
			$user_id = $_SESSION['login']['UserID'];
			$query_update_password = $db->query("UPDATE Users SET PasswordHash='$new_hash', ResetFlag=0, ResetPassword='', LoginAttempts=0 WHERE UserID='$user_id'");

			if ($query_update_password) {
				$info_msgs .= "Password changed successfully!\n";				

				if ($temporary_login) {
					unset($_SESSION['temporary_login']);
				}

				// pull the new login information into session
				$query_result = $db->query("SELECT * FROM Users WHERE UserID='$user_id'");

				if (mysqli_num_rows($query_result)>0) {
					$_SESSION['login'] = $query_result->fetch_assoc();
				}
				else {
					// if for some reason we end up in this code block, end the session and force user to login using the new password
					$_SESSION = array(); // unset session variables
					session_destroy();  // destroy session 					
				}		
			}
			else {
				$error_msgs .= "Oops, there was a problem, please try again.\n";
			}
		}
		else {
			$error_msgs .=  (!$old_passwords_match) ? "Old password is incorrect.\n" : "";
			$error_msgs .=  (!$old_and_new_passwords_different) ? "New password must be different from old.\n" : "";			
			$error_msgs .=  ($old_passwords_match && !$new_password_secure) ? "A new password must be at 4-12 characters and have at least 1 digit.\n" : "";
			$error_msgs .=  ($new_password_secure && !$new_passwords_match) ? "New passwords do not match.\n" : "";
			
			//$error_msgs = preg_replace('/\n\n/g', '\n', $error_msgs);
			//$error_msgs = preg_replace('/\n/g', '<br/>', $error_msgs);
		}
	}
	elseif ($temporary_login) {
		$info_msgs = "You have to enter a new password to unlock your account.";
	}
	
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Change password</title>
	<?php include "includes/css.php" ?>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<div id="content" class="content">
		<h1>Change Password</h1><br/>
		
		<form class="small-form" action="" id="frm-change-password" method="post" name="frm-change-password" role="form">
			<div class="form-group">
				<?php if ($error_msgs != "") : ?>
					<p id="response-label" class="form-label red"><?php echo $error_msgs; ?></p>
				<?php endif; ?>
				<?php if ($info_msgs != "") : ?>
					<p id="response-label" class="form-label green"><?php echo $info_msgs; ?></p>
				<?php endif; ?>
			</div>
			<div class="form-group">
				<?php if (!isset($_SESSION['temporary_login'])) : ?>
					<input id="txt-old-password" name="txt-old-password" class="form-control" type="password" placeholder="Current password"> 
				<?php endif; ?>
			</div>
			<div class="form-group">
				<input id="txt-new-password" name="txt-new-password" class="form-control" type="password" placeholder="New Password">
			</div>
			<div class="form-group">
				<input id="txt-retype-new-password" name="txt-retype-new-password" class="form-control" type="password" placeholder="Re-type New Password">
			</div>
			<div class="form-group">
				<input id="btn-submit" name="btn-submit" class="btn btn-default" type="submit" value="Submit">
				<input id="btn-cancel" name="btn-cancel" class="btn" type="button" value="Cancel" onclick="window.location='index.php'">
			</div>
		</form>			
		
	</div>		
	<?php include "includes/footer.php"; ?>
</body>
</html>