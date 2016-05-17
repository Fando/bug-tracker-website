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
	<title>Sign in</title>
	<?php include "includes/css.php" ?>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<div id="content" class="content">
		<h1>Sign in</h1><br/>
		
		<form class="small-form" action="#" id="frm-login" name="frm-login" role="form" method="post">
			<div class="form-group">
				<p id="response-label" class="form-label"></p>
			</div>				
			<div class="form-group">
				<input id="txt-username" name="txt-username" class="form-control" type="text" value="alex" placeholder="Username">
			</div>
			<div class="form-group">
				<input id="txt-password" name="txt-password" class="form-control" type="password" value="1234" placeholder="Password"> <a href="forgot-password.php" id="forgot-password" class="floatr">forgot password</a>
			</div>
			<div class="form-group">
				<input id="btn-submit" name="btn-submit" class="btn btn-default" type="submit" value="Sign in">
				<input id="btn-cancel" name="btn-cancel" class="btn" type="button" value="Cancel" onclick="window.location='index.php'">
			</div>
		</form>	
		
	</div>	
	<?php include "includes/footer.php"; ?>
	<script src="js/functions.js"></script>
	<script src="js/login.js"></script>
</body>
</html>