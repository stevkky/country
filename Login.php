<?php 
@session_start();
include_once('maindish/savinginputs.php');
include_once('lang.php');
$error ="";
if(isset($_POST['login']))
{
	 $email = $_POST['email'];
	 $password = $_POST['password'];

	 if(login($email,$password))
	 {
		header('location:intnatpt.php');
		exit;
	 }

	 $error ="Invalid login credentials";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Covid-19 <?php echo lang('general.DASHBOARD') ?> </title>
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
	<meta content="" name="description" />
	<meta content="" name="author" />
	
	<!-- ================== BEGIN BASE CSS STYLE ================== -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
	<link href="assets/css/default/app.min.css" rel="stylesheet" />
	<!-- ================== END BASE CSS STYLE ================== -->
</head>
<body class="pace-top">
	<!-- begin #page-loader -->
	<div id="page-loader" class="fade show">
		<span class="spinner"></span>
	</div>
	<!-- end #page-loader -->
	
	<!-- begin login-cover -->
	<div class="login-cover">
		<div class="login-cover-image" style="background-image: url(assets/img/login-bg/login-bg-21.jpg)" data-id="login-cover-image"></div>
		<div class="login-cover-bg"></div>
	</div>
	<!-- end login-cover -->
	
	<!-- begin #page-container -->
	<div id="page-container" class="fade">
		<!-- begin login -->
		<div class="login login-v2" data-pageload-addclass="animated fadeIn">
			<!-- begin brand -->
			<div class="login-header">
				<div class="brand">
					<b>COVID 19 </b> <?php echo lang('general.DASHBOARD') ?>
				</div>
				<div class="icon">
					<img src ="assets/img/virus.png" width="100px" height:="70px" style="margin-bottom:10px" />
				</div>
			</div>
			<!-- end brand -->
			<!-- begin login-content -->
			<div class="login-content">
				<?php if(!empty($error)) { ?>
					<div class="alert alert-danger fade show">
						<strong>Error! <?php echo $error; ?></strong>
					</div>
				<?php  } ?>
				<form action="Login.php" method="POST" class="margin-bottom-0">
					<div class="form-group m-b-20">
						<input type="text" class="form-control form-control-lg" placeholder="<?php echo lang('general.EMAIL') ?>" name="email" required />
					</div>
					<div class="form-group m-b-20">
						<input type="password" class="form-control form-control-lg" placeholder="<?php echo lang('general.PASSWORD') ?>" name="password" required />
					</div>
					<div class="login-buttons">
						<button type="submit" class="btn btn-success btn-block btn-lg" name="login"><?php echo lang('general.LOGIN') ?> </a></button>
					</div>
					
				</form>
			</div>
			<!-- end login-content -->
		</div>
		<!-- end login -->
		
		
		
		<!-- begin scroll to top btn -->
		<a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
		<!-- end scroll to top btn -->
	</div>
	<!-- end page container -->
	
	<!-- ================== BEGIN BASE JS ================== -->
	<script src="assets/js/app.min.js"></script>
	<script src="assets/js/theme/default.min.js"></script>
	<!-- ================== END BASE JS ================== -->
	
	<!-- ================== BEGIN PAGE LEVEL JS ================== -->
	<script src="assets/js/demo/login-v2.demo.js"></script>
	<!-- ================== END PAGE LEVEL JS ================== -->
</body>
</html>