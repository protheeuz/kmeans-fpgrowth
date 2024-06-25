<?php
/*
---------------------------------------------
Developed by Mathtech.id
Website 				: https://www.Mathtech.id
Email					: Mathtech@gmail.com
Telp/ SMS/ WhatsApp		: 0878 7160 4309
---------------------------------------------
*/
$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$url_tmp = str_replace($www, '', $actual_link);
$url_tmp = explode('/',$url_tmp);
switch($url_tmp[0]){
	case 'login':
	case 'alternatif':
	case 'alternatif_update':
	case 'kriteria':
	case 'kriteria_update':
	case 'cluster':
	case 'cluster_update':
	case 'center_points':
	case 'center_points_update':
	case 'hasil':
	case 'password_update':
	case 'penjualan': // Tambahkan ini
	case 'list_penjualan': // Tambahkan ini
		$_GET['hal'] = $url_tmp[0];
		break;
	default:
		$_GET['hal'] = '';
		break;
}

$page='';
if(isset($_GET['hal'])){
	$page=$_GET['hal'];
}
$current_page=$page;
$must_login = true;
switch($page){
	case 'login':
		$page="include 'includes/p_login.php';";$must_login = false;
		break;
	case 'alternatif':
		$page="include 'includes/alternatif.php';";
		break;
	case 'alternatif_update':
		$page="include 'includes/alternatif_update.php';";
		break;
	case 'kriteria':
		$page="include 'includes/kriteria.php';";
		break;
	case 'kriteria_update':
		$page="include 'includes/kriteria_update.php';";
		break;
	case 'cluster':
		$page="include 'includes/cluster.php';";
		break;
	case 'cluster_update':
		$page="include 'includes/cluster_update.php';";
		break;
	case 'center_points':
		$page="include 'includes/center_points.php';";
		break;
	case 'center_points_update':
		$page="include 'includes/center_points_update.php';";
		break;
	case 'nilai':
		$page="include 'includes/nilai.php';";
		break;
	case 'klasifikasi':
		$page="include 'includes/klasifikasi.php';";
		break;
	case 'password_update':
		$page="include 'includes/password_update.php';";
		break;
	case 'penjualan':
		$page="include 'includes/penjualan.php';";
		break;
	case 'list_penjualan':
		$page="include 'includes/list_penjualan.php';";
		break;
	case 'hasil':
		$page="include 'includes/hasil.php';";
		break;
	case 'analisa':
		$page="include 'includes/analisa.php';";
		break;
	default:
		$page="include 'includes/home.php';";$must_login = false;
		break;
}
$CONTENT_["main"] = $page;
if($must_login==true and !isset($_SESSION['LOGIN_ID'])){
	exit("<script>location.href='".$www."';</script>");
}
?>
