<?php
session_start(); 

include "includes/mysqlconnect.php"; // Connect to database
include "includes/login.php"; // Pull login script

if(!$logged_in) { // If user is not already logged in, show the login screen

	displayLogin();
 
} else { // If user is already logged in, show the page
	
	$m = NULL;

	include "includes/current_user.php"; // Get current user information
	
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
	
	$save_result = mysql_query("INSERT INTO client_jobs VALUES ('','$client_job_name','$client_job_jobno','$client_job_notes','','$mysqldate','$client_job_user','$client_job_project','')");
	if($save_result) {
		
		$m = '<div class="success">Your estimate has been saved.</div>';
		$est_id = mysql_insert_id();
		
	}
	
	// Now get all the items in each category
	
	$t = 0;
	
	foreach ($_POST['labor_check'] as $labor_total) {
			
		$labor_data = explode("|", $labor_total);
		
		$labor_price = number_format($labor_data[0], 2, '.', ',');
		$labor_id = $labor_data[2];
		
		// Check the text box to see if the value has changed
			
			$labor_updated_price = $_POST['labor_total'][$t];
			
			$labor_updated_price = number_format($labor_updated_price, 2, '.', ',');
			
			if($labor_update_price == $labor_price) {
				$new_labor_total = $labor_price;
			} else {
				$new_labor_total = $labor_updated_price;
			}
	
			$item_result = mysql_query("INSERT INTO estimates VALUES ('','$labor_id','$new_labor_total','$est_id')");
			if(!$item_result) {
				$m = '<div class="error">There was a problem saving the estimate.</div>';
			} $t++;
}

$u = 0;

foreach ($_POST['mat_check'] as $mat_total) {
		
	$mat_data = explode("|", $mat_total);
	
	$mat_price = number_format($mat_data[0], 2, '.', ',');
	$mat_id = $mat_data[2];
	
	// Check the text box to see if the value has changed
		
		$mat_updated_price = $_POST['mat_total'][$u];
		
		$mat_updated_price = number_format($mat_updated_price, 2, '.', ',');
		
		if($mat_update_price == $mat_price) {
			$new_mat_total = $mat_price;
		} else {
			$new_mat_total = $mat_updated_price;
		}

		$item_result = mysql_query("INSERT INTO estimates VALUES ('','$mat_id','$new_mat_total','$est_id')");
		if(!$item_result) {
			$m = '<div class="error">There was a problem saving the estimate.</div>';
		} $t++;
}

$y = 0;

foreach ($_POST['add_check'] as $add_total) {
		
	$add_data = explode("|", $add_total);
	
	$add_price = number_format($add_data[0], 2, '.', ',');
	$add_id = $add_data[2];
	
	// Check the text box to see if the value has changed
		
		$add_updated_price = $_POST['add_total'][$y];
		
		$add_updated_price = number_format($add_updated_price, 2, '.', ',');
		
		if($add_update_price == $add_price) {
			$new_add_total = $add_price;
		} else {
			$new_add_total = $add_updated_price;
		}

		$item_result = mysql_query("INSERT INTO estimates VALUES ('','$add_id','$new_add_total','$est_id')");
		if(!$item_result) {
			$m = '<div class="error">There was a problem saving the estimate.</div>';
		} $t++;
}

// Send to the new form

header('Location: http://quotes.wwbindery.com/estimate/?no=' .$est_id .'');

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
	
<h1><a href="/">Bindery Quotes</a></h1>

</header>

<div class="content" role="main">
	
<?php

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

<?php

	echo '<div class="third">';
	echo '<h4>Labor</h4>';
	
		echo '<label for="new_labor_item">Add:</label>';
		echo '<select name="new_labor_item">';
		echo '<option value="#">----------</option>';
		
		$newlabor_result = mysql_query("SELECT * FROM Items WHERE item_type=1 && item_del!=1 ORDER BY item_name ASC");
		if($newlabor_result) {
			
			while ($newlabor_item_row = mysql_fetch_assoc($newlabor_result)) {
				
				echo '<option value="'. $newlabor_item_row['item_id'] . '">'. $newlabor_item_row['item_name'] . ' : 1 @ $' . $newlabor_item_row['item_amount'] . '</option>';
				
			}
		}
		
		echo '</select>';
		echo '<br />';

	$labor_result = mysql_query("SELECT * FROM project_items WHERE pitem_type='$new_project' && pitem_type=1 && pitem_del!=1");
	if($labor_result) {
		$labor_num = mysql_num_rows($labor_result);
	
		if($labor_num > 0) {
			
		$i = 0;
		
		while ($labor_row = mysql_fetch_assoc($labor_result)) {
			
			$pitem_id = $labor_row['pitem_item'];
			
			$labor_item_result = mysql_query("SELECT * FROM Items WHERE item_id='$pitem_id' && item_del!=1 LIMIT 1");
			if($labor_item_result) {
				
				while ($labor_item_row = mysql_fetch_assoc($labor_item_result)) {
					
					$item_total_price = $labor_row['pitem_qty'] * $labor_item_row['item_amount'];
					$item_total_price = number_format($item_total_price, 2, '.', ',');
			
					echo '<input type="checkbox" value="' . $item_total_price . '|' . $labor_item_row['item_name'] . '|' . $pitem_id . '" name="labor_check[]" checked="checked" /> <label for="labor_' . $i . '_check">' . $labor_item_row['item_name'] . '</label> $<input type="text" value="' . $item_total_price . '" name="labor_total[]" /><br />';
					
				}
			} $i++;
		}
		
	}}
	
	
	echo '</div>';	
	
	echo '<div class="third">';
	echo '<h4>Materials</h4>';
	
	echo '<label for="new_mat_item">Add:</label>';
	echo '<select name="new_mat_item">';
	echo '<option value="#">----------</option>';
	
	$newmat_result = mysql_query("SELECT * FROM Items WHERE item_type=0 && item_del!=1 ORDER BY item_name ASC");
	if($newmat_result) {
		
		while ($newmat_item_row = mysql_fetch_assoc($newmat_result)) {
			
			echo '<option value="'. $newmat_item_row['item_id'] . '">'. $newmat_item_row['item_name'] . ' : 1 @ $' . $newmat_item_row['item_amount'] . '</option>';
			
		}
		
	echo '</select>';
	echo '<br />';
		
	}
	
	$mat_result = mysql_query("SELECT * FROM project_items WHERE pitem_type='$new_project' && pitem_type=0 && pitem_del!=1");
	if($mat_result) {
		
		$mat_num = mysql_num_rows($mat_result);
		
		if($mat_num > 0) {
		
		$i = 0;
		
		while ($mat_row = mysql_fetch_assoc($mat_result)) {
			
			$pitem_id = $mat_row['pitem_item'];
			
			$mat_item_result = mysql_query("SELECT * FROM Items WHERE item_id='$pitem_id' && item_del!=1 LIMIT 1");
			if($mat_item_result) {
				
				while ($mat_item_row = mysql_fetch_assoc($mat_item_result)) {
					
					$item_total_price = $mat_row['pitem_qty'] * $mat_item_row['item_amount'];
					$item_total_price = number_format($item_total_price, 2, '.', ',');
			
					echo '<input type="checkbox" value="' . $item_total_price . '|' . $mat_item_row['item_name'] . '|' . $pitem_id . '" name="mat_check[]" checked="checked" /> <label for="mat_' . $i . '_check">' . $mat_item_row['item_name'] . '</label> $<input type="text" value="' . $item_total_price . '" name="mat_total[]" /><br />';
					
				}
			} $i++;
		}
		
		
		}
	}
	
		
	
	echo '</div>';
	
	echo '<div class="third">';
	echo '<h4>Add-Ons</h4>';
	
	echo '<label for="new_add_item">Add:</label>';
	echo '<select name="new_add_item">';
	echo '<option value="#">----------</option>';
	
	$newadd_result = mysql_query("SELECT * FROM Items WHERE item_type=2 && item_del!=1 ORDER BY item_name ASC");
	if($newadd_result) {
		
		while ($newadd_item_row = mysql_fetch_assoc($newadd_result)) {
			
			echo '<option value="'. $newadd_item_row['item_id'] . '">'. $newadd_item_row['item_name'] . ' : 1 @ $' . $newadd_item_row['item_amount'] . '</option>';
			
		}
		
	echo '</select>';
	echo '<br />';
		
	}
	
	$add_result = mysql_query("SELECT * FROM project_items WHERE pitem_type='$new_project' && pitem_type=2 && pitem_del!=1");
	if($add_result) {
		
		$add_num = mysql_num_rows($add_result);
	
		if($add_num > 0) {
		
		$i = 0;
		
		while ($add_row = mysql_fetch_assoc($add_result)) {
			
			$pitem_id = $add_row['pitem_item'];
			
			$add_item_result = mysql_query("SELECT * FROM Items WHERE item_id='$pitem_id' && item_del!=1 LIMIT 1");
			if($add_item_result) {
				
				while ($add_item_row = mysql_fetch_assoc($add_item_result)) {
					
					$item_total_price = $add_row['pitem_qty'] * $add_item_row['item_amount'];
					$item_total_price = number_format($item_total_price, 2, '.', ',');
			
					echo '<input type="checkbox" value="' . $item_total_price . '|' . $add_item_row['item_name'] . '|' . $pitem_id . '" name="add_check[]" checked="checked" /> <label for="add_' . $i . '_check">' . $add_item_row['item_name'] . '</label> $<input type="text" value="' . $item_total_price . '" name="add_total[]" /><br />';
					
				}
			} $i++;
		}
		
		
	}}
	
	
	
	echo '</div>';	
	echo '<div class="clear"><input class="submit-button" type="submit" name="update_total" style="position:relative;bottom:.4em;" value="Update" /></div>';


?>



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
	
		echo '<div class="msg_head"></div>';

		echo '<div class="msg_body">';
	
		$new_total_price = 0;
	
	// Determine which checkboxes are still checked and then build the array with those still checked
	
		echo '<div class="third">';
		echo '<h4>Labor</h4>';
		
		echo '<label for="new_labor_item">Add:</label>';
		echo '<select name="new_labor_item">';
		echo '<option value="#">----------</option>';

		$newlabor_result = mysql_query("SELECT * FROM Items WHERE item_type=1 && item_del!=1 ORDER BY item_name ASC");
		if($newlabor_result) {

			while ($newlabor_item_row = mysql_fetch_assoc($newlabor_result)) {

				echo '<option value="'. $newlabor_item_row['item_id'] . '">'. $newlabor_item_row['item_name'] . ' : 1 @ $' . $newlabor_item_row['item_amount'] . '</option>';

			}
		}

		echo '</select>';
		echo '<br />';
	
	if(isset($_POST['labor_check'])) {
				
		$t = 0;
		
		foreach ($_POST['labor_check'] as $labor_total) {
				
			$labor_data = explode("|", $labor_total);
			
			$labor_price = number_format($labor_data[0], 2, '.', ',');
			$labor_name = $labor_data[1];
			$labor_id = $labor_data[2];
			
			// Check the text box to see if the value has changed
				
				$labor_updated_price = $_POST['labor_total'][$t];
				
				$labor_updated_price = number_format($labor_updated_price, 2, '.', ',');
				
				if($labor_update_price == $labor_price) {
					$new_labor_total = $labor_price;
				} else {
					$new_labor_total = $labor_updated_price;
				}
			
			echo '<input type="checkbox" value="' . $new_labor_total . '|' . $labor_name . '|' . $labor_id . '" name="labor_check[]" checked="checked" /> <label for="labor_check[]">' . $labor_name . '</label> $<input type="text" value="' . $new_labor_total . '" name="labor_total[]" /><br />';
			
			$new_total_price = $new_total_price + $new_labor_total;
			
			$t++;
			
		}
	}
	
		// Check to see if a new item from the labor select box has been selected
		
		if(isset($_POST['new_labor_item'])) {
			
			$new_labor_id = $_POST['new_labor_item'];
			
			$new_labor_item_result = mysql_query("SELECT * FROM Items WHERE item_id='$new_labor_id' && item_del!=1 LIMIT 1");
			if($new_labor_item_result) {
				
				while($new_labor_item_row = mysql_fetch_assoc($new_labor_item_result)) {
					
					$new_labor_total = number_format($new_labor_item_row['item_amount'], 2, '.', ',');
				
					echo '<input type="checkbox" value="' . $new_labor_item_row['item_amount'] . '|' . $new_labor_item_row['item_name'] . '|' . $new_labor_item_row['item_id'] . '" name="labor_check[]" checked="checked" /> <label for="labor_check[]">' . $new_labor_item_row['item_name'] . '</label> $<input type="text" value="' . $new_labor_total . '" name="labor_total[]" /><br />';
					
					$new_total_price = $new_total_price + $new_labor_item_row['item_amount'];
					
				
				}
			}		
		}
		
			
		
		echo '</div>';
	
		echo '<div class="third">';
		echo '<h4>Materials</h4>';
		
			echo '<label for="new_mat_item">Add:</label>';
			echo '<select name="new_mat_item">';
			echo '<option value="#">----------</option>';

			$newmat_result = mysql_query("SELECT * FROM Items WHERE item_type=0 && item_del!=1 ORDER BY item_name ASC");
			if($newmat_result) {

				while ($newmat_item_row = mysql_fetch_assoc($newmat_result)) {

					echo '<option value="'. $newmat_item_row['item_id'] . '">'. $newmat_item_row['item_name'] . ' : 1 @ $' . $newmat_item_row['item_amount'] . '</option>';

				}
			}

			echo '</select>';
			echo '<br />';
		
		$u=0;
	
	if(isset($_POST['mat_check'])) {
		foreach ($_POST['mat_check'] as $mat_total) {
	
			$mat_data = explode("|", $mat_total);
			
			$mat_price = number_format($mat_data[0], 2, '.', ',');
			$mat_name = $mat_data[1];
			$mat_id = $mat_data[2];
			
			// Check the text box to see if the value has changed
			
				$mat_updated_price = $_POST['mat_total'][$u];
				
				$mat_updated_price = number_format($mat_updated_price, 2, '.', ',');

				if($mat_updated_price == $mat_price) {
					$new_mat_total = $mat_price;
				} else {
					$new_mat_total = $mat_updated_price;
				}
						
			echo '<input type="checkbox" value="' . $new_mat_total . '|' . $mat_name . '|' . $mat_id . '" name="mat_check[]" checked="checked" /> <label for="mat_check[]">' . $mat_name . '</label> $<input type="text" value="' . $new_mat_total . '" name="mat_total[]" /><br />';
			
			$new_total_price = $new_total_price + $new_mat_total;
	
			$u++;
		}
	}
	
	// Check to see if a new item from the material select box has been selected
	
	if(isset($_POST['new_mat_item'])) {
		
		$new_mat_id = $_POST['new_mat_item'];
		
		$new_mat_item_result = mysql_query("SELECT * FROM Items WHERE item_id='$new_mat_id' && item_del!=1 LIMIT 1");
		if($new_mat_item_result) {
			
			while($new_mat_item_row = mysql_fetch_assoc($new_mat_item_result)) {
				
				$mat_updated_price = number_format($new_mat_item_row['item_amount'], 2, '.', ',');
			
				echo '<input type="checkbox" value="' . $new_mat_item_row['item_amount'] . '|' . $new_mat_item_row['item_name'] . '|' . $new_mat_item_row['item_id']. '" name="mat_check[]" checked="checked" /> <label for="mat_check[]">' . $new_mat_item_row['item_name'] . '</label> $<input type="text" value="' . $mat_updated_price . '" name="mat_total[]" /><br />';
				
				$new_total_price = $new_total_price + $new_mat_item_row['item_amount'];
			
			}
		}		
	}
	
	
	
		echo '</div>';
	
		echo '<div class="third">';
		echo '<h4>Add-Ons</h4>';
		
		echo '<label for="new_add_item">Add:</label>';
		echo '<select name="new_add_item">';
		echo '<option value="#">----------</option>';

		$newadd_result = mysql_query("SELECT * FROM Items WHERE item_type=2 && item_del!=1 ORDER BY item_name ASC");
		if($newadd_result) {

			while ($newadd_item_row = mysql_fetch_assoc($newadd_result)) {

				echo '<option value="'. $newadd_item_row['item_id'] . '">'. $newadd_item_row['item_name'] . ' : 1 @ $' . $newadd_item_row['item_amount'] . '</option>';

			}
		}

		echo '</select>';
		echo '<br />';
		
		$y=0;
			
	if(isset($_POST['add_check'])) {
		foreach ($_POST['add_check'] as $add_total) {
	
			$add_data = explode("|", $add_total);
		
			$add_price = number_format($add_data[0], 2, '.', ',');
			$add_name = $add_data[1];
			$add_id = $add_data[2];
			
			// Check the text box to see if the value has changed
			
			$add_updated_price = $_POST['add_total'][$y];
			
			$add_updated_price = number_format($add_updated_price, 2, '.', ',');
		
			if($add_update_price == $add_price) {
				$new_add_total = $add_price;
			} else {
				$new_add_total = $add_updated_price;
			}
						
			echo '<input type="checkbox" value="' . $new_add_total . '|' . $add_name . '|' . $add_id . '" name="add_check[]" checked="checked" /> <label for="add_check[]">' . $add_name . '</label> $<input type="text" value="' . $new_add_total . '" name="add_total[]" /><br />';
			
			$new_total_price = $new_total_price + $new_add_total;
			
			$y++;
	
		}
	}
	
	// Check to see if a new item from the labor select box has been selected
	
	if(isset($_POST['new_add_item'])) {
		
		$new_add_id = $_POST['new_add_item'];
		
		$new_add_item_result = mysql_query("SELECT * FROM Items WHERE item_id='$new_add_id' && item_del!=1 LIMIT 1");
		if($new_add_item_result) {
			
			while($new_add_item_row = mysql_fetch_assoc($new_add_item_result)) {
				
				$new_add_price = number_format($new_add_item_row['item_amount'], 2, '.', ',');
			
				echo '<input type="checkbox" value="' . $new_add_item_row['item_amount'] . '|' . $new_add_item_row['item_name'] . '|' . $new_add_item_row['item_id']. '" name="add_check[]" checked="checked" /> <label for="add_check[]">' . $new_add_item_row['item_name'] . '</label> $<input type="text" value="' . $new_add_price . '" name="add_total[]" /><br />';
				
				$new_total_price = $new_total_price + $new_add_item_row['item_amount'];
			
			}
		}		
	}
	
		
		echo '<br />';
		echo '</div>';
		echo '<div class="clear"><input class="submit-button" type="submit" name="update_total" style="position:relative;bottom:.4em;" value="Update" /></div>';
		
		echo '</div>';
		
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
	
} } ?>

</div><!-- End .content -->

<?php include('includes/navigation.php'); ?>

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