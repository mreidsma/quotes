<?php
session_start(); 

include "../includes/mysqlconnect.php"; // Connect to database
include "../includes/login.php"; // Pull login script

if(!$logged_in) { // If user is not already logged in, show the login screen

	displayLogin();
 
} 
else { // If user is already logged in, show the page
	
	$m = NULL;
	$est_id = $_GET['no'];

	include "../includes/current_user.php"; // Get current user information
	
if(isset($_POST['update_total'])) {
	
	global $new_total_price;
	
	$new_total_price = 0;
	
	if(isset($_POST['_check'])) {
		
		$t = 0;
		
		$unSetQuery = mysql_query("UPDATE estimates SET estimate_active=0 WHERE estimate_item_clientjob='$est_id'");
		if(!$unSetQuery) {
			$m = "Whoopsie.";
		}
		
		foreach ($_POST['_check'] as $check_total) {
				
			$_data = explode("|", $check_total);
			
			$updated_price = number_format($_POST['_total'][$t], 2, '.', ',');
			$_price = number_format($_data[0], 2, '.', ',');
			$_name = $_data[1];
			$_id = $_data[2];
			
			$new_total_price = $new_total_price + $updated_price;
			
			// Write these to database
			
			$checkQuery = mysql_query("UPDATE estimates SET estimate_item_amount='$updated_price', estimate_active=1 WHERE estimate_item_id='$_id' && estimate_item_amount='$_price' && estimate_item_clientjob='$est_id'");
			if(!$checkQuery) {
				$m = "Oh boy. Something went wrong.";
			}
			
			$t++;	
		}
	}
	
	if(isset($_POST['new_item'])) {
		
		$x = 0;
		
		foreach ($_POST['new_item'] as $_newItem) {
		
			$new_id = $_newItem;
		
			$new_item_result = mysql_query("SELECT * FROM Items WHERE item_id='$new_id' && item_del!=1 LIMIT 1");
		
			if($new_item_result) {
			
				while($new_item_row = mysql_fetch_assoc($new_item_result)) {
				
				$new_total = number_format($new_item_row['item_amount'], 2, '.', ',');
			
				// Write these to database
				
				$addQuery = mysql_query("INSERT INTO estimates VALUES ('','$new_id','$new_total','$est_id','1')");
					if(!$addQuery) {
						$m = "There was an error";
					}
				
				$new_total_price = $new_total_price + $new_total;
				
				}
			}
			
			$x++;
		}		
	}
	
	// Update project total and details
	
	$updated_name = mysql_real_escape_string($_POST['updated_clientjob_name']);
	$updated_jobno = mysql_real_escape_string($_POST['updated_clientjob_jobno']);
	$updated_notes = mysql_real_escape_string($_POST['updated_clientjob_notes']);
	
	$newTotalQuery = mysql_query("UPDATE client_jobs SET client_job_name='$updated_name', client_job_jobno='$updated_jobno', client_job_notes='$updated_notes', client_job_amount='$new_total_price' WHERE client_job_id='$est_id'");
	if(!$newTotalQuery) {
		$m = "Oof. Something went wrong.";
	}
}

function buildDropdown($ty) {
	
	echo '<label for="new_item">Add:</label>
	<select name="new_item[]">
	<option value="#">----------</option>';

	$result = mysql_query("SELECT * FROM Items WHERE item_type='$ty' && item_del!=1 ORDER BY item_name ASC");
	if($result) {
		
		while ($item_row = mysql_fetch_assoc($result)) {
			
			echo '<option value="'. $item_row['item_id'] . '">'. $item_row['item_name'] . ' : 1 @ $' . $item_row['item_amount'] . '</option>';
			
		}
	}
	
	echo '</select>
	<br />';
}

function buildCheckboxes($ty, $id) {

	$checkResult = mysql_query("SELECT * FROM estimates WHERE estimate_item_clientjob='$id' && estimate_active!=0");
	if($checkResult) {
		$checkNum = mysql_num_rows($checkResult);
	
		if($checkNum > 0) {
			
		$i = 0;
		
		while($row = mysql_fetch_assoc($checkResult)) {
			
			$pitem_id = $row['estimate_item_id'];
			
			$pitem_amount = $row['estimate_item_amount'];
			
			$item_result = mysql_query("SELECT * FROM Items WHERE item_id='$pitem_id' && item_type='$ty' && item_del!=1 LIMIT 1");
			if($item_result) {
				
				while ($item_row = mysql_fetch_assoc($item_result)) {
					
					$item_total_price = number_format($pitem_amount, 2, '.', ',');
			
					echo '<input type="checkbox" value="' . $item_total_price . '|' . $item_row['item_name'] . '|' . $pitem_id . '" name="_check[]" checked="checked" /> <label for="_check">' . $item_row['item_name'] . '</label> $<input type="text" value="' . $item_total_price . '" name="_total[]" /><br />';
					
				}
			} $i++;
		}
		
	}}

}
?>

<!DOCTYPE html>

<html lang="en">
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Bindery Quotes</title>

<!-- For all browsers -->
<link rel="stylesheet" media="screen" href="../css/style.css" />
<link rel="stylesheet" media="print" href="../css/print.css" />
<!-- For progressively larger displays -->
<link rel="stylesheet" media="only screen and (min-width: 600px)" href="../css/600.css?v=1" />
<link rel="shortcut icon" href="../img/apple-touch-icon.png">
<link rel="shortcut icon" href="../img/favicon.ico">

</head>

<body>

<header role="banner">
	
<h1>Bindery Quotes</h1>

</header>

<div class="content" role="main">
	
<?php

if($m != NULL) { echo $m; }

// Pull estimate information

$client_est_result = mysql_query("SELECT * FROM client_jobs WHERE client_job_id='$est_id' && client_job_del!=1 LIMIT 1");
if($client_est_result) {
	while ($client_est_row = mysql_fetch_assoc($client_est_result)) {
	
	$project_name = $client_est_row['client_job_name'];
	$project_no = $client_est_row['client_job_jobno'];
	$project_notes = $client_est_row['client_job_notes'];
	$project_amount = $client_est_row['client_job_amount'];
	$project_user = $client_est_row['client_job_user'];
	$project_type = $client_est_row['client_project_type'];
			
	$project_name_result = mysql_query("SELECT * FROM projects WHERE project_id='$project_type' && project_del!=1 LIMIT 1");
	if($project_name_result) {
		while($project_name_row = mysql_fetch_assoc($project_name_result)) {
			$project_type_name = $project_name_row['project_name'];
		}		
	}
	
	echo '<h3>' . $project_type_name . ' for ' . $project_name . '</h3>';
	
	}
}

?>

<div class="msg_head"></div>

<form action="" id="binderyquotes" method="post">

<div class="msg_body">

<div class="third">
<h4>Labor</h4>

<?php 

buildDropdown(1); 

buildCheckboxes(1, $est_id); 

?>
	
</div>

<div class="third">
<h4>Materials</h4>

<?php 

buildDropdown(0); 

buildCheckboxes(0, $est_id); 

?>
	
</div>

<div class="third">
<h4>Add-ons</h4>

<?php 

buildDropdown(2); 

buildCheckboxes(2, $est_id); 

?>
	
</div>

<div class="clear"><input class="submit-button" type="submit" name="update_total" style="position:relative;bottom:.4em;" value="Update" /></div>

</div>

<div style="clear:left;"></div>
<div class="msg_head save_link">Edit This Estimate</div>

<div class="msg_body save_body">

<label for="updated_clientjob_name">Client Name</label>
<input class="client_name" name="updated_clientjob_name" type="text" value="<?=$project_name;?>" />

<label for="clientjob_jobno">Job No.</label>
<input class="client_jobno" name="updated_clientjob_jobno" type="text" value="<?=$project_no;?>"/>

<label for="clientjob_notes">Notes:</label>
<textarea class="client_notes" name="updated_clientjob_notes"><?=$project_notes;?></textarea><br />

<input type="hidden" name="clientjob_user" value="<?=$current_user_id;?>" />

<!--input class="submit-button" name="save_est" style="margin-bottom:1em;"type="submit" value="Save" / -->
</div>

</form>

<?php

// Calculate new total

if($new_total_price <= 0) {
	$new_total_price = $project_amount;
}

$new_total_price = number_format($new_total_price, 2, '.', ',');

echo '<h3 class="total_price">Total: $' . $new_total_price . '</h3>';

?>

		
</div><!-- End .content -->

<?php include('../includes/navigation.php'); ?>

<footer role="contentinfo">

</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>
<script src="../js/modernizr.js" type="text/javascript"></script>
<script src="../js/jquery.validate.min.js"></script>
<script src="../js/respond.js"></script>
<script>
$(function() {
	$(".save_body").find("label").hide();
	
	$(".save_body").hide();
	
	$(".save_link").click(function() {
		$(this).next(".save_body").slideToggle(400);
	});
});
</script>
</body>
</html>
<?php
}
?>
