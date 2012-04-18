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