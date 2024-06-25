<?php
/*
---------------------------------------------
Developed by Mathtech.id
Website 				: https://www.Mathtech.id
Email					: Mathtech@gmail.com
Telp/ SMS/ WhatsApp		: 0878 7160 4309
---------------------------------------------
*/
session_name('session_k_means');
session_start();
require_once 'config.php';
$con->close();
session_destroy();
session_unset();
exit("<script>window.location='".$www."';</script>");

?>