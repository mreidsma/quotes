<?php
session_start(); 

include "../includes/mysqlconnect.php"; // Connect to database
include "../includes/login.php"; // Pull login script

if(!$logged_in) { // If user is not already logged in, show the login screen

	displayLogin();
 
} else { // If user is already logged in, show the page

	include "../includes/current_user.php"; // Get current user information

	$m = NULL; $e = 0; // Reset the error messages

	if(isset($_POST["post"])) {

	// Set up variables
	$item_id = $_POST['item_id'];
	$item_type = $_POST['item_type'];
	$item_name = mysql_real_escape_string($_POST['item_name']);
	$item_amount = $_POST['item_amount'];
	$item_qty = $_POST['item_qty'];
	$ud_item_name = mysql_real_escape_string($_POST['ud_item_name']);
	$ud_item_type = $_POST['ud_item_type'];
	
	if($item_type != $ud_item_type) {
		if(($item_type == 1) && ($ud_item_type != 1)) { // Item is no longer labor
			$ud_item_qty = 1;
		}
		if(($item_type != 1) && ($ud_item_type == 1)) { // Item is now labor
			$time_length = $_POST['time-length'];
			$time_span = $_POST['time-span'];
			
			$ud_item_qty = $time_length/$time_span;
		}
	} else {
		$ud_item_qty = $item_qty;
	}
	
	$ud_item_amount = $_POST['ud_item_amount'];
	$ud_item_notes = $_POST['ud_item_notes']; // Don't run mysql-real-escape-string yet, need markdown.php first
	$ud_item_user = $_POST['ud_item_user'];

	// Server-side validation
	
	if($ud_item_name == NULL) { $ud_item_name = $item_name; }
	if($ud_item_amount == NULL) { $ud_item_amount = $item_amount; }

	if($m == NULL) { // No errors.
	
	// Let's format and clean the notes field
	
	$ud_item_notes = mysql_real_escape_string($ud_item_notes);
	// Now we'll write is all to the database
	
	$query = "UPDATE Items SET item_name='$ud_item_name', item_type='$ud_item_type', item_amount='$ud_item_amount', item_qty='$ud_item_qty', item_user='$ud_item_user', item_notes='$ud_item_notes' WHERE item_id='$item_id'";
	$result = mysql_query($query) or die(mysql_error());
		if($result) {
			
			$m = "<div class=\"alert\">Your item has been updated.</div>"; $e = 0;
		}
	}
}

?>
<!DOCTYPE html>

<html lang="en">
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">

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

<div class="return-link"><a href="items.php">&#8617;</a></div>

<div class="clear"></div>

</header>

<div class="content" role="main">

<?php echo $m; ?>

<h3>Edit item</h3>

<form name="edit-item-form" id="edit-item-form" action="" method="post" class="add-form">
<fieldset>

<?php

// Grab selected item for editing

$item_id = $_GET['id'];

$query = "SELECT * FROM Items WHERE item_id='$item_id' LIMIT 1";
$result = mysql_query($query) or die(mysql_error());

$num = mysql_numrows($result);

$i = 0;
while($i < 1) {

$item_name = mysql_result($result,$i,"item_name");
$item_type = mysql_result($result,$i,"item_type");
$item_amount = mysql_result($result,$i,"item_amount");
$item_qty = mysql_result($result,$i,"item_qty");
$item_notes = stripslashes(mysql_result($result,$i,"item_notes"));
$item_user = mysql_result($result,$i,"item_user");

$i++; }

?>

<div>
<label for="ud_item_name">Name:</label> <input type="text" name="ud_item_name" id="ud_item_name" value="<?php echo $item_name; ?>" class="required" required placeholder="Name" />
<div class="radio-list"><span class="radio"><input type="radio" name="ud_item_type" id="radio-materials" value="0" <?php if ($item_type == 0) { echo "checked"; } ?> />&nbsp;Materials</span>&nbsp;<span class="radio"><input type="radio" name="ud_item_type" id="radio-labor" value="1" <?php if($item_type==1) { echo "checked"; } ?>/>&nbsp;Labor</span>&nbsp;<span class="radio"><input type="radio" name="ud_item_type" id="radio-addon" value="2" <?php if($item_type == 2) { echo "checked"; } ?>/>&nbsp;Add-On</span></div>
<label for="ud_item_amount">Price:</label> <span class="dollar">$</span><input type="text" name="ud_item_amount" id="ud_item_amount" value="<?php echo $item_amount; ?>" placeholder="Amount" class="required" required /> <?php if($item_type != 1) { ?><span id="qty">each</span><?php } else { ?><span id="qty">/hr @ </span><input class="required" id="time-length" name="time-length" required type="text" value="<?php echo $item_qty; ?>"/>&nbsp;<select name="time-span" id="time-span"><option value="60">Minutes</option><option value="1" selected>Hours</options></select><?php } ?><br />

<label for="ud_item_notes" id="ud-item-notes-label">Notes:</label>
<textarea name="ud_item_notes" id="ud_item_notes" placeholder="Notes"><?php echo $item_notes; ?></textarea><br />
<input type="hidden" name="ud_item_user" id="ud_item_user" value="<?php echo $current_user_id; ?>" />

<input type="hidden" name="item_name" id="item_name" value="<?php echo $item_name; ?>" />
<input type="hidden" name="item_id" id="item_id" value="<?php echo $item_id; ?>" />
<input type="hidden" name="item_amount" id="item_amount" value="<?php echo $item_amount; ?>" />
<input type="hidden" name="item_qty" id="item_qty" value="<?php echo $item_qty; ?>" />

<input type="submit" name="post" class="submit-button" value="Update Item" />
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
<li><a href="edititem.php?id=<?php echo $row['item_id']; ?>"><?php echo $row['item_name']; ?></a>&nbsp;<a href="delitem.php?id=<?php echo $row['item_id']; ?>" title="Delete this item" onclick="alert("This item will be removed from all projects. Are you sure you want to delete this item? (There is no undo)")" class="delete_key">X</a></li>
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