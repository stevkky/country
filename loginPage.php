<?php
	session_start();

  include_once('maindish/try.php');
  $error = ['email' => ''];
  $loginError = '';
    if(isset($_POST['signin'])){
        
    $_SESSION['email'] = $_POST['email'];
	$_SESSION['password'] = $_POST['password']; 

      $email = $_POST['email'];
      $password = $_POST['password'];

		if(!filter_var($email,FILTER_VALIDATE_EMAIL))
        {
			$error['email'] = 'invalid  email';
        }

       if(!array_filter($error)) { 
      $password = mysqli_real_escape_string($db, $_POST['password']);
	  //$hashed_password = md5($password);
      $email = mysqli_real_escape_string($db, $_POST['email']);
      $sql = "SELECT * FROM user_login WHERE user_email = '$email' and user_password ='$password' and user_type = 'Admin'";
      $sql_1 = "SELECT * FROM user_login WHERE user_email = '$email' and user_password ='$password' and user_type = 'User'";
      $query = mysqli_query($db, $sql);
      $query_1 = mysqli_query($db, $sql_1);
	   }
      //include 'Template/Create_db.php';
  
      if(mysqli_num_rows($query) > 0){
		 header('Location:index11.php');
	 }else if(mysqli_num_rows($query_1) > 0){
		header('Location:index.php'); 
	 }
	 else{
		 $loginError = 'invalid email or password'; 
	 }
	
}
              
?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Covid-19 | Login Page</title>
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
	<meta content="" name="description" />
	<meta content="" name="author" />

    <style>
        .errText {
        color: #dc143c;
        padding: 10px;
            }
    </style>
	
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
					<b>COVID 19 </b> Login 
				</div>
				<div class="icon">
					<i class="fa fa-snowflake"></i>
				</div>
			</div>
			<!-- end brand -->
			<!-- begin login-content -->
			<div class="login-content">
				<form action="loginPage.php" method="POST" class="margin-bottom-0">
					<div class="form-group m-b-20">
						<input type="text" class="form-control form-control-lg" placeholder="abc@p.com" name="email" required />
                        <div class="errText"><?php echo $error['email']; ?></div>
					</div>
					<div class="form-group m-b-20">
						<input type="password" class="form-control form-control-lg" placeholder="Password" name="password" required />
					</div>
					<div class="checkbox checkbox-css m-b-20">
						<input type="checkbox" id="remember_checkbox" /> 
						<!--<label for="remember_checkbox">
							Remember Me
						</label>-->
					</div>
					<div class="login-buttons">
						<button type="submit" class="btn btn-success btn-block btn-lg" name="signin">Login</button>
					</div><br>
					<?php echo $loginError ?>

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