<?php
session_start(); 
	include ($_SERVER['DOCUMENT_ROOT'] .  "/includes/mysqlconnect.php"); // Connect to database
	include ($_SERVER['DOCUMENT_ROOT'] . "/includes/login.php"); // Pull login script

$m = null;

/**
 * Delete cookies - the time must be in the past,
 * so just negate what you added when creating the
 * cookie.
 */
if(isset($_COOKIE['cookname']) && isset($_COOKIE['cookpass'])){
   setcookie("cookname", "", time()-60*60*24*100, "/");
   setcookie("cookpass", "", time()-60*60*24*100, "/");
}

if($logged_in){

   /* Kill session variables */
   unset($_SESSION['username']);
   unset($_SESSION['password']);
   $_SESSION = array(); // reset session array
   session_destroy();   // destroy session.

   $m = "<h1>Logged Out</h1>\n";
   $m .= "You have successfully <b>logged out</b>. Back to <a href=\"/login.php\">the login page</a>";
   
}

header("location: /admin/index.php"); // redirect to the index page immediately

?>
<html>
<head>
<title>Logging Out</title>
<meta http-equiv="refresh" content="0;url=/index.php"/>
</head>
<body>

<? echo $m; ?>

</body>
</html>
