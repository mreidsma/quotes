<?php
session_start(); 

include "../includes/mysqlconnect.php"; // Connect to database
include "../includes/login.php"; // Pull login script

if(!$logged_in) { // If user is not already logged in, show the login screen

	displayLogin();
 
} else { // If user is already logged in, show the page

	include "../includes/current_user.php"; // Get current user information

	$m = NULL; $e = 0; // Reset the error messages
	
	if((isset($_POST["post"])) && ($_GET['add'] == "true")) { // New project

		// Set up variables

		$project_name = mysql_real_escape_string($_POST['add-project-name']);
		$project_private = $_POST['add-project-private'];
		$project_notes = $_POST['add-project-notes']; // Don't run mysql-real-escape-string yet, need markdown.php first

		// Server-side validation

		if($project_name == NULL) { echo '<meta http-equiv="refresh" content="0;url=projects.php?e=1&amp;m=15">'; } // Eventually pass the preentered stuff?

		if($m == NULL) { // No errors.

		// Let's format and clean the notes field

		$project_notes = mysql_real_escape_string($project_notes);
		// Now we'll write is all to the database

		$query = "INSERT INTO projects VALUES ('','$project_name','$project_notes','','$current_user_id','$project_private','0')";
		$result = mysql_query($query) or die(mysql_error());
			if($result) {
				$m = "<div class=\"alert\">Your product has been added. </div>"; $e = 0;
				$project_id = mysql_insert_id();
			} else {
				echo '<meta http-equiv="refresh" content="0;url=projects.php?e=1&amp;m=16">';
			}
		}		
	}

	if((isset($_POST["post"])) && ($_GET['add'] != "true")) { // Project info has been edited

	// Set up variables
	$project_id = $_POST['project_id'];
	$project_name = $_POST['project_name'];
	$ud_project_name = mysql_real_escape_string($_POST['ud_project_name']);
	$ud_project_notes = mysql_real_escape_string($_POST['ud_project_notes']);
	$ud_project_user = $_POST['ud_project_user'];
	$project_private = $_POST['project_private'];
	

	// Server-side validation
	
	if($ud_project_name == NULL) { $ud_project_name = $project_name; }

	if($m == NULL) { // No errors.
	
	// Now we'll write it all to the database
	
	$query = "UPDATE projects SET project_name='$ud_project_name', project_notes='$ud_project_notes', project_user='$ud_project_user', project_private='$project_private' WHERE project_id='$project_id'";
	$result = mysql_query($query) or die(mysql_error());
		if($result) {
			
			$m = "<div class=\"alert\">Your product has been updated.</div>"; $e = 0;
		}
	}
}

if(isset($_POST["add-item"])) {
	
	// Get variables
	
	$pitem_project = $_POST['pitem_project'];
	$pitem_total = $_POST['project-total'];
	$pitem_qty = $_POST['qty'];
	$item = $_POST['item'];
	$item_details = explode("|",$item);
	$pitem_item = $item_details[0];
	$pitem_type = $item_details[1];
	$pitem_amount = $item_details[2];
	$item_qty = $item_details[3];
	
	if($item_qty) { // Labor
		$pitem_qty = $pitem_qty * $item_qty;
	}
	
	// Do validation
	
	if($pitem_qty == 0) { $m = "<div class=\"alert\">How much of this item?</div>"; }
	
	$newtotal = $pitem_total + ($pitem_amount * $pitem_qty);
	
	// Write to database
	
	if($m == NULL) {
	
	$update_result = mysql_query("INSERT INTO project_items VALUES ('','$pitem_item','$pitem_type','$pitem_project','$pitem_qty','','0')");
	if($update_result) {
		$project_update_results = mysql_query("UPDATE projects SET project_total='$newtotal' WHERE project_id='$pitem_project'");
		if($project_update_results) {}
	} else {
		$m = "<div class=\"alert\">There was a problem adding this item.</div>";
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

<div class="return-link"><a href="projects.php">&#8617;</a></div>

<div class="clear"></div>

</header>

<div class="content" role="main">

<?php echo $m; ?>

<h3>Edit Product</h3>

<?php

// Grab selected item for editing

if($project_id == NULL) { $project_id = $_GET['id']; } else { $project_id = $project_id; }

$query = "SELECT * FROM projects WHERE project_id='$project_id' && project_del!=1 LIMIT 1";
$result = mysql_query($query) or die(mysql_error());

$num = mysql_numrows($result);

$i = 0;
while($i < 1) {

$project_name = stripslashes(mysql_result($result,$i,"project_name"));
$project_notes = stripslashes(mysql_result($result,$i,"project_notes"));
$project_total = mysql_result($result,$i,"project_total");
$project_user = mysql_result($result,$i,"project_user");
$project_private = mysql_result($result,$i,"project_private");

$i++; }
 
?>
<div class="project-title"><?php echo $project_name; ?> <span class="panel-trigger submit-button">Edit</span></div>
<div class="running-total" style="margin: .5em 0; padding-left: 1em; font-size: 1.3em; background: #ddd; color: #333; ">$<?php echo $project_total; ?></div>
<div style="clear: both;"></div>
<div class="panel-hidden">
<form class="add-form" name="edit-item-form" id="edit-item-form" action="?add=false" method="post">
<fieldset>
<div>
<label for="ud_project_name">Name:</label> <input class="name-input" type="text" name="ud_project_name" id="ud_project_name" value="<?php echo $project_name; ?>" class="required" required placeholder="Name" /><br />

<label for="ud_project_notes" id="ud-item-project-label">Notes:</label>
<textarea class="add-notes" name="ud_project_notes" id="ud_project_notes" placeholder="Notes" /><?php echo $project_notes; ?></textarea><br />
<input type="hidden" name="ud_project_user" id="ud_project_user" value="<?php echo $current_user_id; ?>" />

<input type="hidden" name="project_name" id="project_name" value="<?php echo $project_name; ?>" />
<input type="hidden" name="project_id" id="project_id" value="<?php echo $project_id; ?>" />
<div style="clear: both;"></div>
<!--div class="radio-list" style="width: 50%; float: left;"><span class="radio"><input type="checkbox" name="project-private" id="radio-private" value="1" <?php if($project_private == 1) { echo "checked"; } ?> />&nbsp;Private?</span></div-->

<input type="submit" name="post" class="submit-button" value="Update Product" />
</div>
</fieldset>
</form>
</div>

<div class="project-item-list">
<?php

$mat_result = mysql_query("SELECT * FROM project_items WHERE pitem_project=$project_id && pitem_type=0 && pitem_del!=1");
if($mat_result) {
	$num = mysql_num_rows($mat_result);
	if($num > 0) {
	echo "<h5>Materials</h5>\n<ul class=\"item_listing\">";
		while ($mat_row = mysql_fetch_assoc($mat_result)) {
			
			$mat_detail_result = mysql_query("SELECT * FROM Items WHERE item_id=$mat_row[pitem_item] && item_del!=1 LIMIT 1");
			if($mat_detail_result) {
				while ($mat_detail_row = mysql_fetch_assoc($mat_detail_result)) {
					echo "<li>$mat_detail_row[item_name] : $mat_row[pitem_qty] @ $mat_detail_row[item_amount]ea <a href=\"delprojitem.php?p=$project_id&amp;id=$mat_row[pitem_id]&amp;t=$project_total&amp;a=$mat_detail_row[item_amount]&amp;q=$mat_row[pitem_qty]\" onclick=\"alert(\"This item will be removed from this product. Are you sure you want to delete this? (There is no undo)\")\" class=\"delete_key\">X</a>";
				}
			}
			
		}
	}
	echo "</ul>";
}

$material_result = mysql_query("SELECT * FROM Items WHERE item_del!=1 && item_type=0 ORDER BY item_name ASC");
if($material_result) {
$num = mysql_num_rows($material_result);
if($num > 0) { ?>
	<form class="add-form" action="" method="post" style="padding-top: .5em;">
	<input type="hidden" name="project" value="<?php echo $project_id; ?>" />
	<input type="hidden" name="project-total" value="<?php echo $project_total; ?>" />
	<label for="qty">Qty:</label> <input type="text" name="qty" id="qty" placeholder="Qty" required class="required" />
	<!-- Select field with all items listed -->
	<select name="item" id="item" required class="required">
	<option value="#">--Add Materials--</option>
<?php 
while ($material_row = mysql_fetch_assoc($material_result)) {
?>
<option value="<?php echo $material_row['item_id'] . "|" . $material_row['item_type'] . "|" . $material_row['item_amount']; ?>"><?php echo $material_row['item_name']; ?> @ $<?php echo $material_row['item_amount']; ?> each</option>
<?php
	} ?>
	</select>
	

	<input type="hidden" name="pitem_project" value="<?php echo $project_id; ?>" />
	<input type="submit" class="submit-button add-item-button" style="float: none; position: relative;top:.1em;" value="+" name="add-item" />
	</form>
<?php }
} ?>


<div style="clear: both;"></div>
<?php

$lab_result = mysql_query("SELECT * FROM project_items WHERE pitem_project=$project_id && pitem_type=1 && pitem_del!=1");
if($mat_result) {
	$num = mysql_num_rows($lab_result);
	if($num > 0) {
	echo "<h5>Labor</h5>\n<ul class=\"item_listing\">";
		while ($lab_row = mysql_fetch_assoc($lab_result)) {
			
			$lab_detail_result = mysql_query("SELECT * FROM Items WHERE item_id=$lab_row[pitem_item] && item_del!=1 LIMIT 1");
			if($lab_detail_result) {
				while ($lab_detail_row = mysql_fetch_assoc($lab_detail_result)) {
					
					// Convert hours to minutes
					
					$minutes = number_format($lab_detail_row['item_qty'] * 60);
					
					echo "<li>$lab_detail_row[item_name] : $minutes min. @ \$$lab_detail_row[item_amount]/hour <a href=\"delprojitem.php?p=$project_id&amp;id=$lab_row[pitem_id]&amp;t=$project_total&amp;a=$lab_detail_row[item_amount]&amp;q=$lab_row[pitem_qty]\" onclick=\"alert(\"This item will be removed from this product. Are you sure you want to delete this? (There is no undo)\")\" class=\"delete_key\">X</a>";
				}
			}
			
		}
	}
		echo "</ul>";
	} 

$labor_result = mysql_query("SELECT * FROM Items WHERE item_del!=1 && item_type=1 ORDER BY item_name ASC");
	if($labor_result) {
	$num = mysql_num_rows($labor_result);
	if($num > 0) { ?>
		<form class="add-form" action="" method="post" style="padding-top: .5em;">
		<input type="hidden" name="project" value="<?php echo $project_id; ?>" />
		<input type="hidden" name="project-total" value="<?php echo $project_total; ?>" />
		
		<label for="qty">Qty:</label> <input type="text" name="qty" id="qty" placeholder="Qty" required class="required" />
		<!-- Select field with all items listed -->
		<select name="item" id="item" required class="required">
		<option value="#">--Add Labor--</option>
	<?php 
	while ($labor_row = mysql_fetch_assoc($labor_result)) {
		$minutes = number_format($labor_row['item_qty'] * 60);
		
	?>
	<option value="<?php echo $labor_row['item_id'] . "|" . $labor_row['item_type'] . "|" . $labor_row['item_amount'] . "|" . $labor_row['item_qty']; ?>"><?php echo $labor_row['item_name']; ?> <?php echo $minutes; ?> min. @ $<?php echo $labor_row['item_amount']; ?>/hour</option>
	<?php
		} ?>
		</select>
		

		<input type="hidden" name="pitem_project" value="<?php echo $project_id; ?>" />
		<input type="submit" class="submit-button add-item-button" style="float: none; position: relative;top:.1em;" value="+" name="add-item" />
		</form>
	<?php }
	} ?>
	

	<div style="clear: both;"></div>
<?php
$add_result = mysql_query("SELECT * FROM project_items WHERE pitem_project=$project_id && pitem_type=2 && pitem_del!=1");
if($add_result) {
	$num = mysql_num_rows($add_result);
	if($num > 0) {
	echo "<h5>Add-Ons</h5>\n<ul class=\"item_listing\">";
		while ($add_row = mysql_fetch_assoc($add_result)) {
			
			$add_detail_result = mysql_query("SELECT * FROM Items WHERE item_id=$add_row[pitem_item] && item_del!=1 LIMIT 1");
			if($add_detail_result) {
				while ($add_detail_row = mysql_fetch_assoc($add_detail_result)) {
					echo "<li>$add_detail_row[item_name] : $add_row[pitem_qty] @ $add_detail_row[item_amount]ea <a href=\"delprojitem.php?p=$project_id&amp;id=$add_row[pitem_id]&amp;t=$project_total&amp;a=$add_detail_row[item_amount]&amp;q=$add_row[pitem_qty]\" onclick=\"alert(\"This item will be removed from this product. Are you sure you want to delete this? (There is no undo)\")\" class=\"delete_key\">X</a>";
				}
			}
			
		}
	}
		echo "</ul>";
	}
	$addon_result = mysql_query("SELECT * FROM Items WHERE item_del!=1 && item_type=2 ORDER BY item_name ASC");
		if($addon_result) {
		$num = mysql_num_rows($addon_result);
		if($num > 0) { ?>
			<form class="add-form" action="" method="post" style="padding-top: .5em;">
			<input type="hidden" name="project" value="<?php echo $project_id; ?>" />
			<input type="hidden" name="project-total" value="<?php echo $project_total; ?>" />
			<label for="qty">Qty:</label> <input type="text" name="qty" id="qty" placeholder="Qty" required class="required" />
			<!-- Select field with all items listed -->
			<select name="item" id="item" required class="required">
			<option value="#">--Add an Add-On--</option>
		<?php 
		while ($addon_row = mysql_fetch_assoc($addon_result)) {
		?>
		<option value="<?php echo $addon_row['item_id'] . "|" . $addon_row['item_type'] . "|" . $addon_row['item_amount']; ?>"><?php echo $addon_row['item_name']; ?> @ $<?php echo $addon_row['item_amount']; ?> each</option>
		<?php
			} ?>
			</select>


			<input type="hidden" name="pitem_project" value="<?php echo $project_id; ?>" />
			<input type="submit" class="submit-button add-item-button" style="float: none; position: relative;top:.1em;" value="+" name="add-item" />
			</form>
		<?php }
		} ?>
		

		<div style="clear: both;"></div>

</div>
<div id="edit-item-list">
<h3>Edit Products</h3>
<ul>
<?php

$edit_result = mysql_query("SELECT * FROM projects WHERE project_del!=1");
	if($edit_result) {
		while ($row = mysql_fetch_assoc($edit_result)) {
?>
<li><a href="editproject.php?id=<?php echo $row['project_id']; ?>"><?php echo $row['project_name']; ?></a>&nbsp;<a href="delproject.php?id=<?php echo $row['project_id']; ?>" title="Delete this project" onclick="alert("This product will be removed. Are you sure you want to delete this? (There is no undo)")" class="delete_key">X</a></li>
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
	$(".add-form").validate();
	
	$(".panel-hidden").hide(); // Hide dropdown panel

	$(".panel-trigger").click(function() {
		$(".panel-hidden").slideToggle('slow');
	});

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