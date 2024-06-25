<?php if(!defined('myweb')){ exit(); }?>

<!DOCTYPE html>
<html class="loading" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="author" content="">
	<title>Aplikasi Algoritma K-Means Clustering Terbaru <?php echo date('Y'); ?></title>
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo $www; ?>favicon.ico">
	<link rel="shortcut icon" type="image/png" href="<?php echo $www; ?>favicon.ico">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-touch-fullscreen" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="default">
	<link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,700,900%7CMontserrat:300,400,500,600,700,800,900" rel="stylesheet">
	<!-- BEGIN VENDOR CSS-->
	<!-- font icons-->
	<link rel="stylesheet" type="text/css" href="<?php echo $www; ?>assets/fonts/feather/style.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $www; ?>assets/fonts/simple-line-icons/style.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $www; ?>assets/fonts/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $www; ?>assets/vendors/css/perfect-scrollbar.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $www; ?>assets/vendors/css/prism.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $www; ?>assets/vendors/css/switchery.min.css">
	<!-- END VENDOR CSS-->
	<!-- BEGIN APEX CSS-->
	<link rel="stylesheet" type="text/css" href="<?php echo $www; ?>assets/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $www; ?>assets/css/bootstrap-extended.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $www; ?>assets/css/colors.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $www; ?>assets/css/components.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $www; ?>assets/css/themes/layout-dark.min.css">
	<link rel="stylesheet" href="<?php echo $www; ?>assets/css/plugins/switchery.min.css">
	<!-- END APEX CSS-->
	<!-- BEGIN Page Level CSS-->
	<link rel="stylesheet" href="<?php echo $www; ?>assets/css/pages/authentication.css">
	<!-- END Page Level CSS-->
	<!-- BEGIN: Custom CSS-->
	<link rel="stylesheet" type="text/css" href="<?php echo $www; ?>assets/css/style.css">
	<!-- END: Custom CSS-->
	<!-- BEGIN VENDOR JS-->
	<script src="<?php echo $www; ?>assets/vendors/js/vendors.min.js"></script>
	<script src="<?php echo $www; ?>assets/vendors/js/switchery.min.js"></script>
	<!-- BEGIN VENDOR JS-->
	<!-- BEGIN PAGE VENDOR JS-->
	<!-- END PAGE VENDOR JS-->
	<!-- BEGIN APEX JS-->
	<!-- END APEX JS-->
	<!-- BEGIN PAGE LEVEL JS-->
	<!-- END PAGE LEVEL JS-->
	<!-- BEGIN: Custom CSS-->
	<!-- <script src="<?php echo $www; ?>assets/js/scripts.js"></script> -->
	<!-- END: Custom CSS-->
	<link rel="stylesheet" type="text/css" href="<?php echo $www; ?>assets/vendors/css/datatables/dataTables.bootstrap4.min.css">
	<script src="<?php echo $www; ?>assets/vendors/js/datatable/jquery.dataTables.min.js"></script>
    <script src="<?php echo $www; ?>assets/vendors/js/datatable/dataTables.bootstrap4.min.js"></script>
	<script src="<?php echo $www; ?>assets/js/sweetalert2.all.min.js"></script>
	<link href="<?php echo $www; ?>assets/css/sweetalert2.min.css" rel="stylesheet">
	<!-- <script src="<?php echo $www; ?>assets/js/jquery.min.js"></script> -->
</head>
<body class="vertical-layout vertical-menu 2-columns navbar-sticky" data-menu="vertical-menu" data-col="2-columns">

    <?php include 'header.php';?>

    <div class="wrapper">


      <!-- main menu-->
      <!--.main-menu(class="#{menuColor} #{menuOpenType}", class=(menuShadow == true ? 'menu-shadow' : ''))-->
      <?php include 'sidebar.php';?>
      <div class="main-panel">
        <!-- BEGIN : Main Content-->
        <div class="main-content">
          <div class="content-overlay"></div>
          <?php eval($CONTENT_["main"]);?>
        </div>
        <!-- END : End Main Content-->

        <?php include 'footer.php';?>
      </div>
    </div>

    <!-- START Notification Sidebar-->
    <!-- END Notification Sidebar-->
    <!-- <div class="buy-now">
		<a href="https://Mathtech.id/aplikasi-algoritma-kmeans-clustering" target="_blank" class="btn btn-danger"><i class="ft-shopping-cart"></i> Beli Sekarang</a>
    </div> -->
    <div class="sidenav-overlay"></div>
    <!-- <div class="drag-target"></div> -->


  <script src="<?php echo $www; ?>assets/js/core/app-menu.min.js"></script>
  <script src="<?php echo $www; ?>assets/js/core/app.min.js"></script>
  <script src="<?php echo $www; ?>assets/js/notification-sidebar.min.js"></script>
  <script src="<?php echo $www; ?>assets/js/customizer.min.js"></script>
  <script src="<?php echo $www; ?>assets/js/scroll-top.min.js"></script>
</body>

</html>