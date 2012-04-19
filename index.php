<?php
session_start(); 

include "includes/mysqlconnect.php"; // Connect to database
include "includes/login.php"; // Pull login script

if(!$logged_in) { // If user is not already logged in, show the login screen

	displayLogin();
 
} else { // If user is already logged in, show the page
	
	$m = NULL;

	include "includes/current_user.php"; // Get current user information
	require "includes/functions.php";
	
	if(isset($_POST['save_est'])) {
	
	// Save estimate to table
	
	// Nab client information
	$client_job_name = mysql_real_escape_string($_POST['clientjob_name']);
	$client_job_jobno = mysql_real_escape_string($_POST['clientjob_jobno']);
	$client_job_notes = mysql_real_escape_string($_POST['clientjob_notes']);
	$client_job_user = $_POST['clientjob_user'];
	$client_job_project = $_POST['project_type'];
	$client_job_total = $_POST['client_job_total'];
	
	$mysqldate = date( 'Y-m-d H:i:s', time());
	
	// Ok, let's write it all to the database
	
	$save_result = mysql_query("INSERT INTO client_jobs VALUES ('','$client_job_name','$client_job_jobno','$client_job_notes','$client_job_total','$mysqldate','$client_job_user','$client_job_project','')");
	if($save_result) {
		
		$m = '<div class="success">Your estimate has been saved.</div>';
		$est_id = mysql_insert_id();
		
	}
	
	// Now get all the items in each category
	
	$c = 0;
	
	while($c < 3) {
		
	$checkFunc = $c . '_check';
	$totalFunc = $c . '_total';
	
	echo $checkFunc;
	
	if(isset($_POST[$checkFunc])) {
		
		$t = 0;
		
		foreach ($_POST[$checkFunc] as $check_total) { // Iterate through the checkboxes
				
			$_data = explode("|", $check_total);
			
			$updated_price = number_format($_POST[$totalFunc][$t], 2, '.', ',');
			$_price = number_format($_data[0], 2, '.', ',');
			$_name = $_data[1];
			$_id = $_data[2];
			
			$new_total_price = $new_total_price + $updated_price;
			
			// Write these to database
			
		$item_result = mysql_query("INSERT INTO estimates VALUES ('','$_id','$updated_price','$est_id','1')");
			if(!$item_result) {
				$m = "Oh boy. Something went wrong.";
			}
			
			$t++;	
		}
		
	} $c++;
	}
// Send to the new form

// header('Location: http://' . $_SERVER['SERVER_NAME'] . '/estimates/?no=' .$est_id .'');

}
	
include "includes/header.php";

if($m != NULL) { echo $m; }

?>

<form action="" method="post" id="binderyquotes" name="binderyquotes">
	
<?php
	
	$new_project = $_POST['project_type'];

	// Pull all projects from the database

	$project_result = mysql_query("SELECT * FROM projects WHERE project_del!=1 ORDER BY project_name ASC");
	if($project_result) {
		
		echo '<label for="project_type" style="display:block; width:100%;">Select Project:</label>';
		echo '<select id="project_type" name="project_type">';
		echo '<option value="#">---------</option>';
		
			while ($project_row = mysql_fetch_assoc($project_result)) {
				
				if($project_row['project_id'] == $new_project) {
					
					echo '<option value="'. $project_row['project_id'] . '" selected="selected">'. $project_row['project_name'] . '</option>';
					
				} else {
				
					echo '<option value="'. $project_row['project_id'] . '">'. $project_row['project_name'] . '</option>';
				
				}
			}
		
		echo '</select>';
		
		if(isset($_POST['new_project']) || ($_POST['update_total'])) {
			
			echo '<input class="submit-button-gray" name="new_project" style="position:relative;bottom:.4em;" type="submit" value="Change" />';
			
		} else {
		
			echo '<input class="submit-button" name="new_project" style="position:relative;bottom:.4em;" type="submit" value="Create" />';
		}
	}

?>
	
<?php

if(isset($_POST['new_project'])) {
	
?>

<div class="msg_head"></div>

<div class="msg_body">

<div class="third">
<h4>Labor</h4>

<?php

buildTestDropdown(1);

newCheckboxes(1, $est_id); 

?>
	
</div>

<div class="third">
<h4>Materials</h4>

<?php 

buildTestDropdown(0); 

newCheckboxes(0, $est_id); 

?>
	
</div>

<div class="third">
<h4>Add-ons</h4>

<?php 

buildTestDropdown(2); 

newCheckboxes(2, $est_id); 

?>
	
</div>
	
<div class="clear"><input class="submit-button" type="submit" name="update_total" style="position:relative;bottom:.4em;" value="Update" /></div>

</div>

<?php 

$total_result = mysql_query("SELECT * FROM projects WHERE project_id='$new_project' && project_del!=1 LIMIT 1");
if($total_result) {
	
	while ($total_row = mysql_fetch_assoc($total_result)) {
		
		$total_price = number_format($total_row['project_total'], 2, '.', ',');
		
		echo '<h3 class="total_price">Total: $' . $total_price . '</h3>';
	}
}

} // End new project

if(isset($_POST['update_total'])) { // The details are being updated
	
	$new_total_price = 0;
	
?>

<div class="msg_head"></div>

<div class="msg_body">
	
<div class="third">
<h4>Labor</h4>

<?php

buildTestDropdown(1);

checkCheckBoxes(1);

?>

</div>

<div class="third">
<h4>Materials</h4>

<?php

buildTestDropdown(0);

checkCheckBoxes(0);

?>

</div>

<div class="third">
<h4>Materials</h4>

<?php

buildTestDropdown(2);

checkCheckBoxes(2);

?>

</div>
<br />
</div>

<div class="clear"><input class="submit-button" type="submit" name="update_total" style="position:relative;bottom:.4em;" value="Update" /></div>
		
</div>

<?php
		
		// Calculate new total
		
		$new_total_price = number_format($new_total_price, 2, '.', ',');
		echo '<h3 class="total_price">Total: $' . $new_total_price . '</h3>';
	
}

// Function to save estimate for later	

if(isset($_POST['update_total']) || ($_POST['new_project'])) {
	
	echo '<div style="clear:left;"></div>';
	echo '<div class="msg_head save_link">Save This Estimate</div>';
	
	echo '<div class="msg_body save_body">';
	
	echo '<label for="clientjob_name">Client Name</label>';
	echo '<input class="client_name" name="clientjob_name" placeholder="Client Name" type="text" />';
	
	echo '<label for="clientjob_jobno">Job No.</label>';
	echo '<input class="client_jobno" name="clientjob_jobno" placeholder="Job No." type="text" />';
	
	echo '<label for="clientjob_notes">Notes:</label>';
	echo '<textarea class="client_notes" name="clientjob_notes"></textarea><br />';
	
	echo '<input type="hidden" name="clientjob_user" value="' . $current_user_id . '" />';
		echo '<input type="hidden" name="client_job_total" value="' . $new_total_price . '" />';
	
	echo '<input class="submit-button" name="save_est" style="margin-bottom:1em;"type="submit" value="Save" />';
	echo '</div>';
		
}

?>

</form>

<?php

if(!($_POST['update_total']) && !($_POST['new_project'])) {
		
$saved_est_result = mysql_query("SELECT * FROM client_jobs WHERE client_job_user='$current_user_id' && client_job_del!=1 ORDER BY client_job_id DESC");
if($saved_est_result) {
	
	echo '<section id="old_estimates">';
	echo '<h4>Review Saved Estimates</h4>';
	echo '<ol>';
	
	while($saved_est_row = mysql_fetch_assoc($saved_est_result)) {
		
		// Get project name
		
		$project_name_id = $saved_est_row['client_project_type'];
				
		$project_result = mysql_query("SELECT * FROM projects WHERE project_id='$project_name_id' && project_del!=1 LIMIT 1");
		if($project_result) {
			while($project_name_row = mysql_fetch_assoc($project_result)) {
				$client_project = $project_name_row['project_name'];
		}		
			}
		
		// Format date
		
		$client_date = date("M d, Y", strtotime($saved_est_row['client_job_timestamp']));
		
		echo '<li><a href="/estimates/?no=' . $saved_est_row['client_job_id'] . '">' . $saved_est_row['client_job_name'] . '</a> <a href="/estimates/?no=' . $saved_est_row['client_job_id'] . '" class="client_hide">' . $client_date . '</a> <a href="/estimates/?no=' . $saved_est_row['client_job_id'] . '" class="client_hide">' . $client_project . '</a> <a href="/estimates/?no=' . $saved_est_row['client_job_id'] . '">$' . $saved_est_row['client_job_amount'] . '</a></li>';
		
	}
	
	echo '</ol>';
	echo '</section>';
	
} } 

include "includes/footer.php";

}
?>