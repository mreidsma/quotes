<?php

// Pull information on the current user

	$usr=$_SESSION['username'];
	$user_result = mysql_query("SELECT * FROM users WHERE username='$usr'");
	if($user_result) {
			while ($user_row = mysql_fetch_assoc($user_result)) {

				$current_user_id = $user_row["user_id"]; 
				$current_username = $user_row['username']; 
				$current_user_fn = $user_row['user_fn'];
				$current_user_ln = $user_row['user_ln']; 
				$current_user_email = $user_row['user_email'];
				$current_user_admin = $user_row['user_admin'];
				$current_user_del = $user_row['user_del']; 
			}
	}
?>