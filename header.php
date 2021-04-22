<?php
include_once('lang.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>COVID19 | <?php echo lang('general.DASHBOARD')?></title>
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
	<meta content="" name="description" />
	<meta content="" name="author" />
	
	<!-- ================== BEGIN BASE CSS STYLE ================== -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="assets/css/default/app.min.css" rel="stylesheet" />
    <link href="assets/plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <link href="assets/plugins/datatables.net-fixedcolumns-bs4/css/fixedcolumns.bootstrap4.min.css" rel="stylesheet" />
	<!-- ================== END BASE CSS STYLE ================== -->
</head>
<body>
	<!-- begin #page-loader -->
	<div id="page-loader" class="fade show"><span class="spinner"></span></div>
	<!-- end #page-loader -->
	
	<!-- begin #page-container -->
	<div id="page-container" class="page-container fade page-without-sidebar page-header-fixed page-with-top-menu">
		<!-- begin #header -->
		<div id="header" class="header navbar-default">
			<!-- begin navbar-header -->
			<div class="navbar-header">
				<a href="" class="navbar-brand"><img src ="assets/img/virus.png" width="100px" height:="70px" style="margin-bottom:5px" /> <b>COVID19</b> &nbsp; <?php echo lang('general.DASHBOARD')?></a>
				<button type="button" class="navbar-toggle" data-click="top-menu-toggled">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</div>
			<!-- end navbar-header -->
		
		</div>
		<!-- end #header -->
		
		<!-- begin #top-menu -->
		<div id="top-menu" class="top-menu">
			<!-- begin top-menu nav -->
			<ul class="nav">
				<li><a href="index.php"><i class="fas fa-home"></i> <span><?php echo lang('dataEntry.HOME')?></span></a></li>
                
				<li class="has-sub">
					<a href="javascript:;">
						<i class="fa fa-align-left"></i> 
						<span><?php echo lang('dataEntry.SET_UP')?></span>
						<b class="caret"></b>
					</a>
					<ul class="sub-menu">
						<li><a href="data_entry.php"><?php echo lang('dataEntry.PROVIDERS')?></a></li>			
						<li><a href="regions.php"><?php echo lang('dataEntry.REGIONS')?></a></li>
						<li><a href="district.php"><?php echo lang('dataEntry.DISTRICTS')?></a></li>
						<li><a href="facility.php"><?php echo lang('dataEntry.FACILITY')?></a></li>
						<li><a href="facility_type.php"><?php echo lang('dataEntry.FACILITY_TYPE')?></a></li>

						<?php
						if(!empty($_SESSION['user_type']) && $_SESSION['user_type'] =='Admin' && empty($_SESSION['countryid']))
						{?>
								<li><a href="country.php"><?php echo lang('general.COUNTRY')?></a></li>
							<?php
						}
						?>

					

						<li><a href="reason.php"><?php echo lang('general.REASON')?></a></li>
						<li><a href="testing_method.php"><?php echo lang('dataEntry.TEST_METHOD')?></a></li>
						<li><a href="eqa_marks.php"><?php echo lang('dataEntry.EQA_PASS_MARK')?></a></li>
						<li><a href="round.php"><?php echo lang('general.ROUND')?></a></li>
					</ul>
				</li>
				<li class="has-sub">
					<a href="javascript:;">
						<i class="fa fa-align-left"></i> 
						<span><?php echo lang('dataEntry.DATA_ENTRY')?></span>
						<b class="caret"></b>
					</a>
					<ul class="sub-menu">
						<li><a href="eqa_data.php"><?php echo lang('dataEntry.INT_EQA_DATA_ENT')?></a></li>
						<li><a href="natpteqa_data.php"><?php echo lang('dataEntry.NAT_EQA_DATA_ENTRY')?></a></li>
						<!--<li><a href="nateqa_data.php"><?php echo lang('dataEntry.NAT_RETEST_DATA_ENTRY')?></a></li> -->
						<li><a href="data_testing.php"><?php echo lang('dataEntry.TEST')?></a></li>
					</ul>
				</li>
				<li><a href="users.php"><i class="fas fa-users"></i> <span><?php echo lang('dataEntry.USERS')?></span></a></li>
				<li><a href="logout.php"><i class="fas fa-user"></i> <span><?php echo lang('general.LOGOUT')?></span></a></li>
				<li class="menu-control menu-control-left">
					<a href="javascript:;" data-click="prev-menu"><i class="fa fa-angle-left"></i></a>
				</li>
				<li class="menu-control menu-control-right">
					<a href="javascript:;" data-click="next-menu"><i class="fa fa-angle-right"></i></a>
				</li>
			</ul>
			<!-- end top-menu nav -->
		</div>
		<!-- end #top-menu -->