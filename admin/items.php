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
	
	if($item_type == 1) { // Item type is labor, calculate the "qty" based on dollars per minute/hour
	
		$time_length = $_POST['time-length'];
		$time_span = $_POST['time-span'];
		
		$item_qty = $time_length/$time_span; // Make the qty a value of hours
		
	} else { // Item is either material or add-on. Qty is set by user
		$item_qty = $_POST['add-item-qty'];
	}
	
	$item_amount = $_POST['add-item-amount'];
	$item_notes = $_POST['add-item-notes']; // Don't run mysql-real-escape-string yet, need markdown.php first
	$item_user = $_POST['add-item-user'];

	// Server-side validation
	
	if($item_name == NULL) { $m = "<div class=\"alert\">You must include a name for the item.</div>"; $e = 1; }
	if($item_amount == NULL) { $m = "<div class=\"alert\">You must set a price for this item.</div>"; $e = 1; }

	if($m == NULL) { // No errors.
	
	// Let's format and clean the notes field
	
	$item_notes = mysql_real_escape_string($item_notes);
	// Now we'll write this all to the database
	
	$query = "INSERT INTO Items VALUES ('','$item_name','$item_type','$item_amount','$item_qty','$item_notes','$current_user_id','0')";
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

<div class="return-link"><a href="index.php">&#8617;</a></div>

<div class="clear"></div>

</header>

<div class="content" role="main">

<?php 

if($r != NULL) { // Get deleteed item messages
	if($r == 1) { 
		$m = "<div class=\"alert\">There was a problem deleting your item.</div>"; 
	}
	
	if($r == 2) {
		$m = "<div class=\"alert\">Your item was deleted.</div>";		
	}
}

echo $m; // Show any messages

?>

<h3>Add a new item</h3>

<form name="add-item-form" id="add-item-form" action="items.php" method="post" class="add-form">
<fieldset>
<div>
<label for="add-item-name">Name:</label><input type="text" name="add-item-name" id="add-item-name"<?php if($e != 0) { echo " value=\"" . $item_name . "\""; } ?> placeholder="Name" class="required" required />
<div class="radio-list"><span class="radio"><input type="radio" name="add-item-type" id="radio-materials" value="0" <?php if(($e == 0) || ($item_type == 0)) { echo "checked"; } ?> />&nbsp;Materials</span>&nbsp;<span class="radio"><input type="radio" name="add-item-type" id="radio-labor" value="1" <?php if(($e == 1) && ($item_type == 1)) { echo "checked"; } ?>/>&nbsp;Labor</span>&nbsp;<span class="radio"><input type="radio" name="add-item-type" id="radio-addon" value="2" <?php if(($e == 1) && ($item_type == 2)) { echo "checked"; } ?>/>&nbsp;Add-On</span></div>
<label for="add-item-amount">Price:</label><span class="dollar">$</span><input type="text" name="add-item-amount" id="add-item-amount"<?php if($e != 0) { echo " value=\"" . $item_amount . "\""; } ?> placeholder="Price" class="required" required /> <span id="qty">each<input id="add-item-qty" name="add-item-qty" type="hidden" value="1" /></span><br />

<label for="add-item-notes" id="add-item-notes-label">Notes:</label>
<textarea name="add-item-notes" id="add-item-notes" placeholder="Notes"><?php if(($e == 1) && ($m != NULL)) { echo $item_notes; } ?></textarea><br />
<input type="hidden" name="add-item-user" id="add-item-user" value="<?php echo $current_user_id; ?>" />

<input type="submit" name="post" class="submit-button" value="Add Item" />
</div>
</fieldset>
</form>

<div id="edit-item-list">
<h3>Edit Items</h3>
<ul>
<?php

$edit_result = mysql_query("SELECT * FROM Items WHERE item_del!=1");
	if($edit_result) {
		while ($row = mysql_fetch_assoc($edit_result)) {
?>
<li><a href="edititem.php?id=<?php echo $row['item_id']; ?>"><?php echo $row['item_name']; ?></a>&nbsp;<a href="delitem.php?id=<?php echo $row['item_id']; ?>" title="Delete this item" onclick="alert('This item will be removed from all projects. Are you sure you want to delete this item? (There is no undo)');return true" class="delete_key">X</a></li>
<?php
		}
	}
	
?>
</ul>
</div><!-- End #edit-item-list -->

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

$("#radio-labor").click(function() { // Show labor-only options
	$("span#qty").replaceWith('<span id="qty">/hr @ <input class="required" id="time-length" name="time-length" required type="text" />&nbsp;<select name="time-span" id="time-span"><option value="60">Minutes</option><option value="1">Hours</options></select></span>');
});
$("#radio-addon").click(function() {
	$("span#qty").replaceWith('<span id="qty">each</span>');
});
$("#radio-materials").click(function() {
	$("span#qty").replaceWith('<span id="qty">each</span>');
});
});
</script>
</body>
</html>
<?php
}
?>