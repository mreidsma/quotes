<?php # mysqlconnect.php

// This file contains the database access information.

// Set the database access information as constants

define ('DB_USER', 'username');
define ('DB_PASSWORD', 'password');
define ('DB_HOST', 'localhost'); // This probably shouldn't change
define ('DB_NAME', 'database_name');

// Make the connection and select database 

$dbc = mysql_connect (DB_HOST, DB_USER, DB_PASSWORD);
mysql_select_db (DB_NAME);

?>
