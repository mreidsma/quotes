<?php

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

function buildTestDropdown($ty) {
	
	echo '<label for="' . $ty . '_new_item">Add:</label>
	<select name="' . $ty . '_new_item[]">
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
			
					echo '<input type="checkbox" value="' . $item_total_price . '|' . $item_row['item_name'] . '|' . $pitem_id . '|' . $item_row['item_type'] . '" name="_check[]" checked="checked" /> <label for="_check">' . $item_row['item_name'] . '</label> $<input type="text" value="' . $item_total_price . '" name="_total[]" /><br />';
					
				}
			} $i++;
		}
		
	}}

}

function newCheckboxes($ty, $id) {

	$_result = mysql_query("SELECT * FROM project_items WHERE pitem_type='$id' && pitem_type='$ty' && pitem_del!=1");
	if($_result) {
		$_num = mysql_num_rows($_result);
	
		if($_num > 0) {
			
		$i = 0;
		
		while ($_row = mysql_fetch_assoc($_result)) {
			
			$pitem_id = $_row['pitem_item'];
			
			$_item_result = mysql_query("SELECT * FROM Items WHERE item_id='$pitem_id' && item_del!=1 LIMIT 1");
			if($_item_result) {
				
				while ($_item_row = mysql_fetch_assoc($_item_result)) {
					
					$item_total_price = $_row['pitem_qty'] * $_item_row['item_amount'];
					$item_total_price = number_format($item_total_price, 2, '.', ',');
			
					echo '<input type="checkbox" value="' . $item_total_price . '|' . $_item_row['item_name'] . '|' . $pitem_id . '|' . $_item_row['item_type'] . '" name="' . $ty . '_check[]" checked="checked" /> <label for="' . $ty . '_check">' . $_item_row['item_name'] . '</label> $<input type="text" value="' . $item_total_price . '" name="' . $ty . '_total[]" /><br />';
					
				}
			} $i++;
		}
		
	}}
}

function checkCheckBoxes($ty) {
	
	global $new_total_price;
	
	$checkNew = $ty . '_new_item';
	
	if(isset($_POST[$checkNew])) { // Did we get any new items from dropdowns?
		
		$x = 0;
		
		foreach ($_POST[$checkNew] as $_newItem) {
		
			$new_id = $_newItem;
		
			$new_item_result = mysql_query("SELECT * FROM Items WHERE item_id='$new_id' && item_del!=1 LIMIT 1");
		
			if($new_item_result) {
			
				while($new_item_row = mysql_fetch_assoc($new_item_result)) {
				
				$new_total = number_format($new_item_row['item_amount'], 2, '.', ',');
			
				// Add this checkbox
				
				if($new_item_row['item_type'] == $ty) {
				
				echo '<input type="checkbox" value="' . $new_total . '|' . $new_item_row['item_name'] . '|' . $new_item_row['item_id'] . '|' . $new_item_row['item_type'] . '" name="' . $ty . '_check[]" checked="checked" /> <label for="' . $ty . '_check[]">' . $new_item_row['item_name'] . '</label> $<input type="text" value="' . $new_total . '" name="' . $ty . '_total[' . $x . ']" /><br />';
				
				$new_total_price = $new_total_price + $new_total;
				
			}
				
				}
			}
			
			$x++;
		}		
	}
	
	$checkFunc = $ty . '_check';
	
	if(isset($_POST[$checkFunc])) {
				
		$t = 0;
						
		foreach ($_POST[$checkFunc] as $check_total) {
				
			$_data = explode("|", $check_total);
			
			$_price = number_format($_data[0], 2, '.', ',');
			$_name = $_data[1];
			$_id = $_data[2];
			$_type = $_data[3];
						
			// Check the text box to see if the value has changed
			
				$totalFunc = $ty . '_total';
				
				$_updated_price = $_POST[$totalFunc][$t];
				$new_total = number_format($_updated_price, 2, '.', ',');
					
							
			echo '<input type="checkbox" value="' . $new_total . '|' . $_name . '|' . $_id . '|' . $_type . '" name="' . $ty . '_check[]" checked="checked" /> <label for="' . $ty . '_check[]">' . $_name . '</label> $<input type="text" value="' . $new_total . '" name="' . $ty . '_total[]" /><br />';
			
			$new_total_price = $new_total_price + $new_total;
			
			$t++;	
		}
	}
}


?>