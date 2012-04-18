<?php
session_start(); 

include "../includes/mysqlconnect.php"; // Connect to database
include "../includes/login.php"; // Pull login script

if(!$logged_in) { // If user is not already logged in, show the login screen

	displayLogin();
 
} else { // If user is already logged in, show the page

	include "../includes/current_user.php"; // Get current user information

	if($_GET['m'] == NULL) { $m = NULL; } else { $m = $_GET['m']; } 
	if($_GET['e'] == NULL) { $e = 0; } else { $e = $_GET['e']; } // Reset the error messages
	
	if($m == 15) { $m = "<div class=\"alert\">You must include a name for the project.</div>"; }
	if($m == 16) { $m = "<div class=\"alert\">There was a problem adding your project.</div>"; }
	
	$r = $_GET['r']; // Get the response code from the delete page
	
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

<div class="return-link"><a href="index.php">&#8617;</a></div>

<div class="clear"></div>

</header>

<div class="content" role="main">
<?php
	
	if($r != NULL) { // Get deleteed item messages
		if($r == 1) { 
			$m = "<div class=\"alert\">There was a problem deleting your project.</div>"; 
		}

		if($r == 2) {
			$m = "<div class=\"alert\">Your project was deleted.</div>";		
		}
	}

	echo $m; // Show any messages
?>

<h3>Add a new project</h3>

<form class="add-form" name="add-project-form" id="add-project-form" action="editproject.php?add=true" method="post">
<fieldset>
<div>
<label for="add-project-name">Name:</label><input type="text" class="name-input" name="add-project-name" id="add-project-name"<?php if($e != 0) { echo " value=\"" . $project_name . "\""; } ?> placeholder="Name" class="required" required />

<label for="add-project-notes" id="add-project-notes-label">Notes:</label>
<textarea class="add-notes" name="add-project-notes" id="add-project-notes" placeholder="Notes"><?php if(($e == 1) && ($m != NULL)) { echo $project_notes; } ?></textarea><br />
<input type="hidden" name="add-project-user" id="add-project-user" value="<?php echo $current_user_id; ?>" />

<!--div class="radio-list" style="width: 50%; float: left;"><span class="radio"><input type="checkbox" name="add-project-private" id="radio-private" value="1" <?php if($project_private == 1) { echo "checked"; } ?> />&nbsp;Private?</span></div-->

<input type="submit" name="post" class="submit-button" value="Create Project" />
</div>
</fieldset>
</form>

<div id="edit-item-list">
<h3>Edit Projects</h3>
<ul>
<?php

$edit_result = mysql_query("SELECT * FROM projects WHERE project_del!=1");
	if($edit_result) {
		while ($row = mysql_fetch_assoc($edit_result)) {
?>
<li><a href="editproject.php?id=<?php echo $row['project_id']; ?>"><?php echo $row['project_name']; ?></a>&nbsp;<a href="delproject.php?id=<?php echo $row['project_id']; ?>" title="Delete this project" onclick="alert('This project will be removed. Are you sure you want to delete it? (There is no undo)');return true" class="delete_key">X</a></li>
<?php
		}
	}
	
?>
</ul>
</div><!-- End #edit-project-list -->



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
	$("#add-project-form").validate();

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