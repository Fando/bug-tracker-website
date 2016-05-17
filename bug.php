<?php
	
	require_once 'includes/functions.php';
	require_once 'includes/db.php';

	session_start();

	// redirect to login if not logged in
	if (!isset($_SESSION['login'])) {		
		header('Location:login.php');
		die();
	}
	
/* 	This screen is used to create, edit and view bug reports.
	It will have 2 modes of operation (Create, Edit). 
	The mode of behaviour is specifid by the 'mode' parameter in the query string.
	If 'mode' is not specified, assume 'edit' mode.
	
	When in 'edit' mode there must always exist a secodary parameter called 'bugid'
	which is also passed in the query string. In such a case, the page attempts to 
	load the details of this bug by looking up the bugid in the database. If no
	such bugid exists, or if the bugid is not specified, redirect to the home page, 
	otherwise retrieve the details of the bug from database and populate the fields.
	When in 'edit' mode, upon populating the fields, if the bug
	is marked as 'closed', disable editing, making fields 'read only'.

	When the 'mode' is specified as 'create', the fields are simply presented blank. 
*/
	// define mode constants
	define("MODE_CREATE", "create");
	define("MODE_EDIT", "edit");
	//define("MODE_VIEW", "view");
	
	// assign mode constant based on the value of 'mode' in the query string, the defalt is MODE_VIEW
	$mode = !isset($_GET['mode']) ? MODE_EDIT : $_GET['mode'];
	$mode = $mode == MODE_EDIT || $mode == MODE_CREATE ? $mode : MODE_EDIT;

	$error_msgs=""; // variable to store validation errors
	$info_msgs=""; // variable to store information messages

	// connect to database
	$db = new dbcon();
	$db = $db->db();

	// get default alues
	array_filter($_POST, 'trim_value');
	$number = $_POST['txt-number'];
	$summary = $_POST['txt-summary'];	
	$bug_id = isset($_GET['bugid']) ? $_GET['bugid'] : -1;
	$notify = isset($_POST['chk-notify']);
	$fixed = $_POST['chk-fixed']?1:0;
	$closed = $_POST['chk-closed']?1:0;

	$user_id = $_SESSION['login']['UserID']; // retrieve user's id from session
	

	// if the form is being submitted, execute this code	
	if (isset($_POST['txt-number'])) {
		// gather and sanitize input
		$number = mysqli_real_escape_string($db, $number);	
		$summary = mysqli_real_escape_string($db, $summary);	

		if ($mode == MODE_CREATE) {

			// create a bug entry in the database
			$query_result = $db->query("INSERT INTO Bugs (Number, Summary, CreatorID, DateCreated, Fixed, Closed, ClosedByUserID) VALUES($number, '$summary', $user_id, NOW(), 0, 0, 0)");
			if ($query_result) {
				$new_bug_id = $db->insert_id;
				$info_msgs .= "new BugID = " . $new_bug_id . "\n";

				if ($notify) $query_result2 = $db->query("INSERT INTO Notify (BugID, UserID) VALUES($new_bug_id, $user_id)");
				
				// success, now redirect to the same page, but set mode to 'view' this bug
				header("Location:index.php");
				die();
			}
			else {
				$error_msgs .= "Oops, could not create this bug, try again.\n";
			}
		}
		elseif ($mode == MODE_EDIT) {
			$bugid_valid = preg_match('/^(0|[1-9][0-9]{0,11})$/', $bug_id);
			
			if ($bugid_valid) { // retrieve from database	
				$fixed = $fixed?1:0;
				$closed = $closed?1:0;

				$closed_userd_id = $closed ? $user_id : 0;
				$query_result = $db->query("UPDATE Bugs SET Number=$number, Summary='$summary', Fixed=$fixed, Closed=$closed, ClosedByUserID=$closed_userd_id WHERE BugID=$bug_id");
				
				if (!$notify) $query_result2 = $db->query("DELETE FROM Notify WHERE BugID=$bug_id AND UserID=$user_id");
				else $query_result2 = $db->query("INSERT INTO Notify (BugID, UserID) VALUES($new_bug_id, $user_id");
				
				if ($query_result) {
					//header("Location:bug.php?mode=edit&bugid=" . $bug_id);
					header("Location:index.php");
					die();
				}
				else {
					$error_msgs .= "Oops, could not save this bug, try again.\n";
				}
			}
			else {
				$error_msgs .= "Invalid BugID '" . $bug_id . "'.\n";
			}
		}
	} // if the form is not being submitted
	else {
		if ($mode == MODE_CREATE) {

		}
		elseif ($mode == MODE_EDIT) {
			$bugid_valid = preg_match('/^(0|[1-9][0-9]{0,11})$/', $bug_id);
			
			if ($bugid_valid) { // retrieve from database				
				$query_result = $db->query("SELECT Number, Summary, Username, Fixed, Closed, ClosedByUserID, CreatorID, (SELECT Username  FROM Users, Bugs WHERE Bugs.BugID=$bug_id AND Bugs.ClosedByUserID=Users.UserID) as ClosedByUsername FROM Bugs, Users WHERE Bugs.BugID=$bug_id AND Users.UserID=Bugs.CreatorID");
				if ($query_result) {
					$bug = $query_result->fetch_assoc();
					$number = $bug['Number'];
					$summary = $bug['Summary'];
					$username = $bug['Username'];
					$fixed = $bug['Fixed']==1;
					$closed = $bug['Closed']==1;
					$creator_id = $bug['CreatorID'];
					$closed_by_user_id = $bug['ClosedByUserID'];
					$closed_by_username = $bug['ClosedByUsername'];

					$notify = mysqli_num_rows($db->query("SELECT * FROM Notify WHERE UserID=$user_id AND BugID=$bug_id"));
				}
				else {
					$error_msgs .= "Oops, BugID '" . $bug_id . "' cannot be retrieved from database.\n";
				}
			}
			else {
				$error_msgs .= "Error, BugID '" . $bug_id . "' is invalid.\n";
			}
		}
	}

	// set page title based on 'mode'
	$title = "Edit Bug";
	if ($mode == MODE_CREATE) $title = "Create New Bug";
	else if ($mode == MODE_EDIT && $closed) $title = "Closed Bug";
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php echo $title; ?></title>
	<?php include "includes/css.php" ?>
</head>
<body>
	<?php include "includes/header.php"; ?>
	<div id="content" class="content">
		<h1><?php echo $title; ?></h1><br/>
		
		<form class="small-form" action="" id="frm-bug" name="frm-bug" role="form" method="post">
			<div class="form-group">
			
			<?php 				

				$created_by_user_id_label = strcmp($user_id, $creator_id)===0 ? "<span class='orange'>"."you"."</span>":"<span class=''>".$username."</span>";
				$closed_by_user_id_label = strcmp($user_id, $closed_by_user_id)===0 ? "<span class='orange'>"."you"."</span>":"<span class=''>".$closed_by_username."</span>";
			?>
				<?php if ($mode==MODE_CREATE) : ?>
					<p id="response-label" class="form-label">Create a new bug by filling out the form below</p>
				<?php else : ?>
					<p>Created by: <b><?php echo $created_by_user_id_label; ?></b></p>
				
					<?php if ($closed) : ?>
						<p> Status: 
							<?php if ($fixed) : ?>	
								<span class="green">closed, fixed</span> (by <b><?php echo $closed_by_user_id_label; ?></b>)
							<?php else : ?>
								<span class="red">closed, not fixed</span> (by <b><?php echo $closed_by_user_id_label; ?></b>)
							<?php endif; ?>
						</p>
						
					<?php else : ?>
						<p>Status: <span class="orange">Open</span></p>
					<?php endif; ?>
				<?php endif; ?>
				<p><?php echo $error_msgs; ?></p>
				<p><?php echo $info_msgs; ?></p>
			</div>				
			<div class="form-group">
				<input id="txt-number" name="txt-number" class="form-control" type="text" value="<?php echo $number; ?>" placeholder="Bug Number">
			</div>
			<div class="form-group">
				<textarea id="txt-summary" name="txt-summary" class="form-control" rows="5" placeholder="Bug Summary"><?php echo $summary; ?></textarea>
			</div>

			<div class="checkbox">
				<label>
					<input id="chk-notify" name="chk-notify" type="checkbox" <?php echo ($notify==1?"checked":"");?> >Notify me of all comments
				</label>
			</div>
			<div class="checkbox">
				<label>
					<input id="chk-fixed" name="chk-fixed" type="checkbox" style="visibility:hidden;">
				</label>
			</div>
			<div class="checkbox">
				<label>
					<input id="chk-closed" name="chk-closed" type="checkbox" style="visibility:hidden;">
				</label>
			</div>
			<div class="form-group">
				<?php if (!$closed) : ?>
					<input id="btn-submit" name="btn-submit" class="btn btn-default" type="submit" value="Save">
					<input id="btn-close" name="btn-close" class="btn" type="submit" value="Close Bug">				
				<?php //elseif () : ?>
				<?php endif; ?>
				<input id="btn-cancel" name="btn-cancel" class="btn" type="button" value="Back" onclick="window.location='index.php'">
			</div>
		</form>	
		
	</div>	
	<?php include "includes/footer.php"; ?>
	<script src="js/bug.js"></script>	
</body>
</html>