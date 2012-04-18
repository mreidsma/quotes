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
	require "../includes/functions.php";
	
if(isset($_POST['update_total'])) {
	
	global $new_total_price;
	
	$new_total_price = 0;
	
	if(isset($_POST['_check'])) {
		
		$t = 0;
		
		// Make all checkboxes for this estimate inactive. We're going to update the active ones below.
		// My "hack" for getting uncheck to work with this stupid database setup I created.
		
		$unSetQuery = mysql_query("UPDATE estimates SET estimate_active=0 WHERE estimate_item_clientjob='$est_id'"); 
		if(!$unSetQuery) {
			$m = "Whoopsie.";
		}
		
		foreach ($_POST['_check'] as $check_total) { // Iterate through the checkboxes
				
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
	
	if(isset($_POST['new_item'])) { // Did we get any new items from dropdowns?
		
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

include "../includes/header.php";

if($m != NULL) { echo $m; } // Show any of my clever (and largely unhelpful) error messages

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

<div class="clear"><input class="submit-button" type="submit" name="update_total" style="position:relative;bottom:.4em;" value="Update &amp; Save" /></div>

</div>

<div style="clear:left;"></div>
<div class="msg_head save_link">Edit This Estimate</div>

<div class="msg_body save_body">

<label for="updated_clientjob_name">Client Name</label>
<input class="client_name" name="updated_clientjob_name" type="text" value="<?=$project_name;?>" />

<label for="clientjob_jobno">Job No.</label>
<input class="client_jobno" name="updated_clientjob_jobno" type="text" value="<?=$project_no;?>" />

<label for="clientjob_notes">Notes:</label>
<textarea class="client_notes" name="updated_clientjob_notes"><?=$project_notes;?></textarea><br />

<input type="hidden" name="clientjob_user" value="<?=$current_user_id;?>" />

</div>

</form>

<?php

// Calculate and show new total

if($new_total_price <= 0) {
	$new_total_price = $project_amount;
}

$new_total_price = number_format($new_total_price, 2, '.', ',');

echo '<h3 class="total_price">Total: $' . $new_total_price . '</h3>';

include "../includes/footer.php";

}
?>
