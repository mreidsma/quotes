<?php 

if($_POST['newpass']) {
	require "includes/mysqlconnect.php"; // connect to db
	$query = "SELECT * FROM users WHERE user_email='$_POST[email]' && user_del!=1 LIMIT 1";
	$result = mysql_query($query);
	$num = mysql_numrows($result);
	if ($num == 0) { // Email address is not in database
		$display_message = "<div class=\"alert\">That email address is not in our database. Perhaps you used another?</div>";
	} else { // Found email address

$i=0;
			while ($i < 1) { // Limit to one record in case there are multiple people with the same email

			$app_usr_id=mysql_result($result,$i,"user_id");
			$app_user_name=mysql_result($result,$i,"username");
			$app_first_name=mysql_result($result,$i,"user_fn");
			$app_last_name=mysql_result($result,$i,"user_ln");
			$app_email=mysql_result($result,$i,"user_email");
			$app_admin=mysql_result($result,$i,"user_admin");
			$new_pass = substr(sha1(uniqid(microtime(), true)), 0, 8); // This is their new password
			$md5pass = sha1($new_pass);
			
			$i++; }

			$sql = "UPDATE users SET password='$md5pass' WHERE user_id=$app_usr_id"; 
			$result = mysql_query($sql);
			if($result){ // Password was changed, send email and refresh page
			$to=$app_email;
			$subject="New Password request from Bindery Quotes";
			$message="Dear " . $app_first_name . ",\n\nYou have requested a new password for the Bindery Quotes website. Your new password is:\n\nPassword: " . $new_pass . "\n\nPlease keep this is a safe place since we cannot retrieve it for you.\n\nIf you think this was an error, please email the webmaster at mreidsma@gmail.com.\n\n---\nThe Bindery Quotes Robot\nhttp://quotes.wwbindery.com";
			$headers = 'From: mreidsma@gmail.com' . "\r\n" .
   			'Reply-To: mreidsma@gmail.com' . "\r\n" .
   			'X-Mailer: PHP/' . phpversion();
   			mail($to, $subject, $message, $headers);
   			$display_message="<div class=\"alert\">\n\n<p><strong>We have emailed you a new password.</strong> Please check your email and then <a href=\"index.php\">sign in</a> with your new password. You can always change it again once you're signed in.</p></div>";
   			} }} ?>
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

</header>

		<div id="login">
			<h4>Forgot your password?</h4>
<?php echo "\t\t\t\t\t\t" . $display_message; ?>
		
		<div id="login_form">
			<form action="" method="post">
				Email Address: <input type="text" name="email" size="30" /><br /><br />
				<input type="submit" name="newpass" id="newpass" value="Send New Password" class="submit-button" style="float: none;" />
			</form>
			<div id="login_help">
				<p><a href="index.php" title="Back to login" tabindex="5">&laquo;&nbsp;Sign in</a></p>
			</div>
		</div>
		</div>

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