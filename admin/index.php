<?php
session_start(); 

include "../includes/mysqlconnect.php"; // Connect to database
include "../includes/login.php"; // Pull login script

if(!$logged_in) { // If user is not already logged in, show the login screen

	displayLogin();
 
} else { // If user is already logged in, show the page

	include "../includes/current_user.php"; // Get current user information

	$m = NULL; $e = 0; // Reset the error messages
	
	$r = $_GET['r']; // Get the response code from the delete page

	if(isset($_POST["post"])) {

	// Set up variables

	$item_name = mysql_real_escape_string($_POST['add-item-name']);
	$item_type = $_POST['add-item-type'];
	$item_amount = $_POST['add-item-amount'];
	$item_notes = $_POST['add-item-notes']; // Don't run mysql-real-escape-string yet, need markdown.php first
	$item_user = $_POST['add-item-user'];

	// Server-side validation
	
	if($item_name == NULL) { $m = "<div class=\"alert\">You must include a name for the item.</div>"; $e = 1; }
	if($item_amount == NULL) { $m = "<div class=\"alert\">You must set a price for this item.</div>"; $e = 1; }

	if($m == NULL) { // No errors.
	
	// Let's format and clean the notes field
	
	include "../includes/smartypants.php";
	include "../includes/markdown.php";
	
	$item_notes = mysql_real_escape_string(SmartyPants(Markdown($item_notes)));
	// Now we'll write is all to the database
	
	$query = "INSERT INTO Items VALUES ('','$item_name','$item_type','$item_amount','$item_notes','$current_user_id','0')";
	$result = mysql_query($query) or die(mysql_error());
		if($result) {
			$m = "<div class=\"alert\">Your item has been added.</div>"; $e = 0;
		} else {
			$m = "<div class=\"alert\">There was a problem adding your item.</div>";
		}
	}
}

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

<div class="return-link"><a href="../index.php">&#8617;</a></div>

<div class="clear"></div>

</header>

<div class="content" role="main">



<h3>Manage</h3>

<ul id="manage-navigation">
<li><a href="items.php">Manage Items</a></li>
<li><a href="projects.php">Manage Products</a></li>
<li><a href="account.php">Change Password</a></li>
</ul>

</div><!-- End .content -->

<?php include('../includes/navigation.php'); ?>

<footer role="contentinfo">

</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>
<script src="../js/modernizr.js" type="text/javascript"></script>
<script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
<script src="../js/jquery.validate.min.js"></script>
<script src="../js/respond.js"></script>
<script src="../js/script.js"></script>
<script>

// Scripts for just this page


//<![CDATA[
  bkLib.onDomLoaded(function() {
	new nicEditor({buttonList : ['bold','italic','underline','link','unlink']}).panelInstance('add-item-notes');
 	
 });
//]]>

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
<?php
}
?>