<?php
 	session_start();

	if (isset($_SESSION['login'])) {		
		header('Location:index.php');
		die();
	}	
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Register an account</title>
	<?php include "includes/css.php" ?>	
</head>
<body>
	<?php  include "includes/header.php"; ?>
	<div id="content" class="content">
		<h1>Register an Account</h1><br/>
		<form class="small-form" action="#" id="frm-register" name="frm-register" role="form" method="post">
			<div class="form-group">
				<p id="response-label" class="form-label"></p>
			</div>			
			<div class="form-group">
				<input id="txt-employee-number" name="txt-employee-number" class="input-medium form-control" type="text" placeholder="Employee Number">
			</div>
			<div class="form-group">
				<input id="txt-username" name="txt-username" class="form-control" type="text" placeholder="Username">
			</div>
			<div class="form-group">
				<input id="txt-email" name="txt-email" class="form-control" type="email" placeholder="Email">
			</div>
			<div class="form-group">
				<input id="txt-password" name="txt-password" class="form-control" type="password" placeholder="Password">
			</div>
			<div class="form-group">
				<input id="txt-retype-password" name="txt-retype-password" class="form-control" type="password" placeholder="Re-type Password">
			</div>
			<div class="form-group">
				<input id="btn-submit" name="btn-submit" class="btn btn-default" type="button" value="Create Account">
				<input id="btn-cancel" name="btn-cancel" class="btn" type="button" value="Cancel" onclick="window.location='index.php'">
			</div>
			<div class="form-group">
				<input id="btn-test" name="btn-test" class="btn" type="button" value="Register dummy user">
				<input id="btn-delete-db" name="btn-test" class="btn" type="button" value="Delete all users">
			</div>		
		</form>
	</div>
	<?php  include "includes/footer.php"; ?>
	<script src="js/functions.js"></script>
	<script src="js/register.js"></script>
</body>
</html>