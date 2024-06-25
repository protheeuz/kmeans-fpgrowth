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
require_once 'function.php';
$error = '';

if(isset($_POST['login'])){
	if(empty($_POST['username']) or empty($_POST['password'])){
		$error = 'Lengkapi username dan password';
	}
	$username = $_POST['username'];
	$password = md5($_POST['password']);
	$q = $con->query("SELECT * FROM user WHERE username='".escape($username)."' AND password='".escape($password)."'");
	if ($q->num_rows > 0) {
		$h = $q->fetch_assoc();
		$_SESSION['LOGIN_ID'] = $h['id_user'];
		die;
	}else{
		$error = 'Username dan password salah';
	}
}

if(!empty($error)){
	header('HTTP/1.1 500 Internal Server Error');
	echo $error;
}

$con->close();

?>