<?php
	require_once 'functions.php';
	require_once "db.php";

	$db = new dbcon();
	$db = $db->db();
	$error_msgs = "";
	
	if (mysqli_connect_errno($db)) {
		$error_msgs .= "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	else {
		if (!($db->query("DELETE FROM Users WHERE 1"))) {
			$error_msgs .= "Failed to delete db";
		}		
	}	
	
	echo create_json_response_string($error_msgs, "");

?>