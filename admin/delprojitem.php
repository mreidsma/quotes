<?php
session_start(); 

include "../includes/mysqlconnect.php"; // Connect to database
include "../includes/login.php"; // Pull login script

if(!$logged_in) { // If user is not already logged in, show the login screen

	displayLogin();
 
} else { // If user is already logged in, show the page

	include "../includes/current_user.php"; // Get current user information
	
	$id = $_GET['id'];
	$p = $_GET['p'];
	$t = $_GET['t'];
	$a = $_GET['a'];
	$q = $_GET['q'];
	
	$result = mysql_query("UPDATE project_items SET pitem_del='1' WHERE pitem_id='$id' LIMIT 1");
	if(!$result) {
		$r = 1;
	} else {
		$r = 2;
		$newtotal = $t - ($q * $a);

		$total_result = mysql_query("UPDATE projects SET project_total='$newtotal' WHERE project_id='$p'");
		if($total_result) {}
	}
	
	header("location: /admin/editproject.php?r=$r&id=$p"); // redirect to the item page immediately

}
?>
<html>
<head>
<title>Quotes</title>
<meta http-equiv="refresh" content="0;url=/admin/editproject.php?r=<?php echo $r; ?>&amp;id=<?php echo $p; ?>"/ >
</head>
<body>

Deleting your item...

</body>
</html>