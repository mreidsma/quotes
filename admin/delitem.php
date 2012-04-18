<?php
session_start(); 

include "../includes/mysqlconnect.php"; // Connect to database
include "../includes/login.php"; // Pull login script

if(!$logged_in) { // If user is not already logged in, show the login screen

	displayLogin();
 
} else { // If user is already logged in, show the page

	include "../includes/current_user.php"; // Get current user information
	
	$id = $_GET['id'];
	
	$result = mysql_query("UPDATE Items SET item_del='1' WHERE item_id='$id' LIMIT 1");
	if(!$result) {
		$r = 1;
	} else {
		$r = 2;
	}
	
	header("location: /admin/items.php?r=$r"); // redirect to the item page immediately

}
?>
<html>
<head>
<title>Quotes</title>
<meta http-equiv="refresh" content="0;url=/admin/items.php?r=<?php echo $r; ?>"/>
</head>
<body>

Deleting your item...

</body>
</html>