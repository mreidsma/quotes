<?php
session_start(); 

include "../includes/mysqlconnect.php"; // Connect to database
include "../includes/login.php"; // Pull login script

if(!$logged_in) { // If user is not already logged in, show the login screen

	displayLogin();
 
} else { // If user is already logged in, show the page

	include "../includes/current_user.php"; // Get current user information

	$m = NULL; $e = 0; // Reset the error messages
		
?>

<!DOCTYPE html>

<html lang="en">
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Bindery Quotes : Administration</title>

<!-- For all browsers -->
<link rel="stylesheet" media="screen" href="../css/style.css">
<link rel="stylesheet" media="print" href="../css/print.css">
<!-- For progressively larger displays -->
<link rel="stylesheet" media="only screen and (min-width: 600px)" href="../css/600.css?v=1">
<link rel="shortcut icon" href="../img/apple-touch-icon.png">
<link rel="shortcut icon" href="../img/favicon.ico">

</head>

<body>

<header role="banner">
	
<h1>Bindery Quotes</h1>

<div class="return-link"><a href="/admin/index.php">&#8617;</a></div>

<div class="clear"></div>

</header>

<div class="content" role="main">

<h3>Manage Account</h3>

		<?php

			if($_POST['settings'])
			{
				$m = null;
				$update = array();

				if(strlen($_POST['new_password1']) > 0)
				{
					if($_POST['new_password1'] == $_POST['new_password2'])
					{
						$update_password = sha1($_POST['new_password1']);
					}
					else
					{
						$m = '<div class="alert">Passwords did not match, please try again.</div>';
					}
				}

				if($m != NULL) // If a problem was detected...
				{
					$m = $m;
				}
				else
				{
					$result = mysql_query("UPDATE users SET password='$update_password' WHERE user_id='$current_user_id' LIMIT 1");

					if($result)
					{
						$m = '<div class="alert">Settings successfully saved!</div>';
						include ($_SERVER['DOCUMENT_ROOT'] .  "/includes/current_user.php"); // Update local account info variables before loading the page
					}
				}
			}

		?>
				<div id="login">
					<form action="" method="post" id="settings">

						<?php if($m==NULL) { $m="<h4>Update Password</h4>"; } else { $m=$m; } 
								echo $m; ?>
							<div class="row" style="margin-bottom: 1em;"><label for="new_password1">Change my password to:</label><input type="password" id="new_password1" name="new_password1" placeholder="Change my password to:" style="font-size: 1.1em;" /></div>
							<div class="row" style="margin-bottom: 1em;"><label for="new_password2">New password (again):</label><input type="password" id="new_password2" name="new_password2" placeholder="New password (again):" style="font-size: 1.1em;"  /></div>
							<input type="submit" value="Save Settings" class="submit-button" style="float: none;" name="settings" />
						</div> 
					</form>
				</div>
		<?php } ?>

</div><!-- End .content -->

<?php include('../includes/navigation.php'); ?>

<footer role="contentinfo">

</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>
<script src="../js/modernizr.js" type="text/javascript"></script>
<script src="../js/jquery.validate.min.js"></script>
<script src="../js/respond.js"></script>
<script src="../js/script.js"></script>
<script>

// Scripts for just this page

$(document).ready(function(){
	$("#add-item-form").validate();

	if(!Modernizr.input.placeholder){ // Add placeholder text if HTML5 support broken

		$('[placeholder]').focus(function() {
		  var input = $(this);
		  if (input.val() == input.attr('placeholder')) {
			input.val('');
			input.removeClass('placeholder');
		  }
		}).blur(function() {
		  var input = $(this);
		  if (input.val() == '' || input.val() == input.attr('placeholder')) {
			input.addClass('placeholder');
			input.val(input.attr('placeholder'));
		  }
		}).blur();
		$('[placeholder]').parents('form').submit(function() {
		  $(this).find('[placeholder]').each(function() {
			var input = $(this);
			if (input.val() == input.attr('placeholder')) {
			  input.val('');
			}
		  })
		});
	}

$("label").hide(); // Hide labels if JS is available, since placeholders will show labels for form elements
});
</script>
</body>
</html>
