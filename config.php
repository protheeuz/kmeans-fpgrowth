<?php
/*
---------------------------------------------
Developed by Mathtech.id
Website 				: https://www.Mathtech.id
Email					: Mathtech@gmail.com
Telp/ SMS/ WhatsApp		: 0878 7160 4309
---------------------------------------------
*/
$db_host 		= 'localhost';
$db_user 		= 'root';
$db_password 	= '';
$db_name 		= 'db_kmeans';

$www 			= 'http://localhost/kmeans/';

$con = @new mysqli($db_host, $db_user, $db_password, $db_name);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
} 
?>