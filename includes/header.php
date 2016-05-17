<?php 
	session_start();
?>

<div class="header" id="header">	
	<img class="floatl" src="images/ladybug.png" alt="bug-squash-logo" width="200">	
	<h1>Bug Tracker</h1>

	<?php 
		if (!isset($_SESSION['login'])) {
			echo '<a href="index.php" class="link" id="register">Home</a>';
			echo '<a href="register.php" class="link" id="register">Register</a>';
			echo '<a href="login.php" class="link" id="login">Login</a>';
		}
		else {
			echo '<a href="index.php" class="link" id="home">Home</a>';
			echo '<a href="bug.php?mode=create" class="link" id="create-bug">Create Bug</a>';
			// echo '<a href="index.php" class="link" id="home">Find Bug</a>';
			echo '<a href="change-password.php" class="link" id="change-password">Change password</a>';
			echo '<span><a href="logout.php" class="link" id="logout">Logout</a>(logged in as <b>' . $_SESSION['login']['Username'] . '</b>)</span>';
		}
	?>

	<br class="clearb"\>
</div>

