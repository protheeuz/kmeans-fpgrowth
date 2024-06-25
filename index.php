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
define('myweb',true);

require_once 'config.php';
require_once 'function.php';
require_once 'page.php';

if(isset($_SESSION['LOGIN_ID'])){
	if(isset($_POST['save']) or isset($_POST['delete'])){
		eval($CONTENT_["main"]);
		//header('HTTP/1.1 500 Internal Server Error');
		//echo 'Tidak diperkenankan untuk menambah/ mengubah/ menghapus data pada versi demo ini';
		//die;
	}else{
		require_once 'template.php';
	}
	
}else{
	require_once 'template_login.php';
}
$con->close();
?>