<?php session_start(); 

	require_once 'includes/functions.php';
	require_once 'includes/db.php';

	$error_msgs=""; // variable to store validation errors
	$info_msgs=""; // variable to store information messages

	// connect to database
	$db = new dbcon();
	$db = $db->db();

	$bugs = array(); // will hold all the bugs
	$user_id = $_SESSION['login']['UserID']; // retrieve user's id from session

	// get bugs
	if (isset($_SESSION['login'])) {
		//FROM (SELECT us.UserID as CloseByID, us.Username as ClosedBy FROM Users as us,Bugs as bu WHERE bu.BugID=b.BugID AND bu.ClosedByUserID=us.UserID) as x 
		$bugs = $db->query("SELECT 
								b.Number,b.Summary,b.Fixed,b.Closed,b.BugID,b.CreatorID,u.Username as Creator, x.ClosedByUserID, y.Username as ClosedByUsername 
							FROM 
								Users as u INNER JOIN Bugs as b ON u.userID=b.CreatorID 
								INNER JOIN Bugs as x ON b.BugID=x.BugID 
								LEFT OUTER JOIN Users as y ON x.ClosedByUserID = y.UserID");

/*SELECT 
   b.BugID,b.CreatorID,u.Username,x.ClosedByUserID,y.Username
FROM 
  Users as u    
INNER JOIN   
  Bugs as b
ON
    u.userID=b.CreatorID
INNER JOIN Bugs as x ON b.BugID=x.BugID
LEFT OUTER JOIN Users as y ON x.ClosedByUserID=y.UserID*/

		$number_of_bugs = mysqli_num_rows($bugs);
		$number_of_bugs_open = mysqli_num_rows($db->query("SELECT * FROM Users, Bugs WHERE Users.UserID=Bugs.CreatorID AND Closed=0"));
		$number_of_bugs_closed = $number_of_bugs - $number_of_bugs_open;		
	}
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Bug Tracker</title>
	<?php include "includes/css.php" ?>
</head>
<body>
	<?php  include "includes/header.php"; ?>
	<div id="content" class="content">
		<h1></h1>

		<?php
			// show members area if user is logged in
			if (isset($_SESSION['login'])) {
					echo "<h3>Welcome, there are <b>" . $number_of_bugs_open . "</b> open bugs, <b>" . $number_of_bugs_closed . "</b> closed, <b>" . $number_of_bugs ."</b> total.</h3><br/>";

				$i=0;
				//$num = mysqli_num_rows($bugs);

				echo "<table class='table'><thead>";				
				echo "<tr><th>" . "Bug Number" . "</th><th>" . "Bug Summary" . "</th><th>" . "Creator" . "</th><th>" . "Action" . "</th><th>" . "Status" . "</th><th>" . "Closed By" . "</th></tr>";
				echo "</thead><tbody>"; 

					// echo "<p>" . mysqli_fetch_assoc($bugs) . "</p>";

					while($row = mysqli_fetch_array($bugs)) { 
						$status_colour = "orange"; // set colour of status label depending on status
						if ($row['Closed']==1 && $row['Fixed']==1) $status_colour="green";
						if ($row['Closed']==1 && $row['Fixed']==0) $status_colour="red";
						
						$status_text = ($row['Closed']==1?"Closed, ":"Open") . ($row['Closed']==1?($row['Fixed']==1?"fixed":"not fixed"):"");
						$action_text = ($row['Closed'])?"View":"Edit";

						$creator_text = strcmp($user_id,$row['CreatorID'])===0 ? "<span class='orange'>"."you"."</span>":"<span class=''>".$row['Creator']."</span>";

						$closed_by_label = $row['ClosedByUserID'] > 0 ? ( (strcmp($user_id,$row['ClosedByUserID'])===0) ? "<span class='orange'>"."you"."</span>" : $row['ClosedByUsername'] ) : "";

						echo "<tr>" .

						"<td>" . $row['Number'] . "</td>".
						"<td>" . $row['Summary'] . 
						"</td><td>" . $creator_text . 
						"</td><td>" .  "<a href='bug.php?mode=edit&bugid=". $row['BugID']."'>".$action_text."</a>" .  
						"</td><td><span class='" . $status_colour . "'>" . $status_text . "</span>" .
						"</td><td>" . $closed_by_label . "</td>" .

						"</tr>";
					}
				echo "</tbody></table>";
			}
		?>

	</div>
	<?php  include "includes/footer.php"; ?>	
</body>	
</html>

