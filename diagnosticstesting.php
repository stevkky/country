<?php
@session_start();
if(empty($_SESSION['user_email']))
{
	header('location:Login.php');
}
include_once('maindish/savinginputs.php');
include_once('maindish/diagdash.php');

$editname = null;
$editstatus= null;
$editstatus1= null;
$editstatus2= null;

$id= empty($_REQUEST['id']) ? null : $_REQUEST['id'];

 
 if(isset($_POST['submit']))
 {
	saveSimpleSetup('eqa_data_entry',$id);
 }

 if(isset($_REQUEST['id']))
 {
	$item = getSimpleSetup('eqa_data_entry',$_REQUEST['id']);
	$editname = $item->result;
	$editstatus =$item->is_supervised;
	$editstatus1 =$item->is_enrolled_pt;
	$editstatus2 =$item->result_submitted;


 }


 $lst = getSimpleSetup('eqa_data_entry');
 $flst = getSimpleSetup('facility');
 $plst = getSimpleSetup('providers');
 $tlst = getSimpleSetup('testing_method');

 $rlst = getSimpleSetup('reasons');

 $recent= getRecent($_REQUEST['year']);
 $sample=0;
 $reject=0;
 $analyse=0;
 $positive=0;
 $series=0;
 $rcompare =array();
 if($recent && count($recent) > 0)
 {
	$sample = sampleReceived($recent['id']);
	$reject = sampleReject($recent['id']);
	$analyse = sampleAnalyzed($recent['id']);
	$positive = samplePositive($recent['id']);
	$series = initialSeries($recent['id']);
	$rcompare = sampleRejectFacilty();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>GHLS | Dashboard</title>
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
	<meta content="" name="description" />
	<meta content="" name="author" />
	
	<!-- ================== BEGIN BASE CSS STYLE ================== -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="assets/css/default/app.min.css" rel="stylesheet" />
    <link href="assets/plugins/jvectormap-next/jquery-jvectormap.css" rel="stylesheet" />
    <link href="assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.css" rel="stylesheet" />
    <link href="assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" />
	<!-- ================== END BASE CSS STYLE ================== -->
</head>
<body>
	<!-- begin #page-loader -->
	<div id="page-loader" class="fade show"><span class="spinner"></span></div>
	<!-- end #page-loader -->
	
	<!-- begin #page-container -->
	<div id="page-container" class="fade page-without-sidebar page-header-fixed">
		<!-- begin #header -->
		<div id="header" class="header navbar-default">
			<!-- begin navbar-header -->
			<div class="navbar-header">
				<a href="index.html" class="navbar-brand"><span class="navbar-logo"></span> <b>Ghana Health</b> Laboratory Services</a>
			</div>
			<!-- end navbar-header -->
			<!-- begin header-nav -->
			<ul class="navbar-nav navbar-right">
				<li class="dropdown navbar-user">
					<a href="Login.php" >
					<i class="fas fa-user-lock"></i> 
						<span class="d-none d-md-inline">Admin Login</span>
					</a>	
				</li>
			</ul>
			<!-- end header-nav -->
		</div>
		<!-- end #header -->
		
		<!-- begin #content -->
		<div id="content" class="content">
		<div id="content" class="content">
			<!--begin row-->
			<div class="row">	
				<div class="col-md-7">
					<div class="row"></div>
					<div class="row">
					<h1 class="page-header">Dashboard <small><a href="index.php" style="text-decoration: none;">Diagnostics & Testing Data</a></small> </h1>
					</div>
				</div>
				<div class="col-md-1">
					<div class="row">Year</div>
					<div class="row">
						<select class="form-control" name="year" onchange="reloadRound(this.value);">
							<option <?php if($recent && $recent['year'] == 2020)  echo 'selected'; elseif(!empty($_REQUEST['year']) && $_REQUEST['year'] == 2020) echo 'selected' ?> value="2020">2020</option>
							<option <?php if($recent && $recent['year'] == 2021)  echo 'selected'; elseif(!empty($_REQUEST['year']) && $_REQUEST['year'] == 2021) echo 'selected' ?> value="2021">2021</option>
							<option <?php if($recent && $recent['year'] == 2022 )  echo 'selected'; elseif(!empty($_REQUEST['year']) && $_REQUEST['year'] == 2022) echo 'selected' ?> value="2022">2022</option>
						</select>
					</div>
				</div>
				<div class="col-md-1">
					<div class="row">Month</div>
					<div class="row">
						<select class="form-control" name="month">
							<option value="1">January</option>
							<option value="2">February</option>
							<option value="3">March</option>
							<option value="4">April</option>
							<option value="5">May</option>
							<option value="6">June</option>
							<option value="7">July</option>
							<option value="8">August</option>
							<option value="9">September</option>
							<option value="10">October</option>
							<option value="11">November</option>
							<option value="12">December</option>
						</select>
					</div>
				</div>
				<div class="col-md-1">
					<div class="row">Facility</div>
					<div class="row">
						<select class="form-control" name="facilityid">
							<option value="" selected="selected">-Facility-</option>
							<?php
								while(!$flst->dry())
								{?>
									<option value="<?php echo $flst->id; ?>"><?php echo $flst->name; ?></option>
								<?php
									$flst->next();
								}
							?>
						</select>
					</div>
				</div>
				<div class="col-md-1">
					<div class="row">Reason</div>
					<div class="row">
					<select class="form-control" name="reasonid">
						<option value="" selected="selected">-Reason-</option>
							<?php
								while(!$rlst->dry())
								{?>
									<option value="<?php echo $rlst->id; ?>"><?php echo $rlst->name; ?></option>
								<?php
									$rlst->next();
								}
							?>
						</select>
					</div>
				</div>
				<div class="col-md-1">
					<div class="row col-md-12">
						<button type="submit" class="btn btn-sm btn-primary waves-effect waves-light hastooltip hovertip blockui" style="padding:2px 10px !important;">
						<i class="fa fa-angle-double-right"></i> Generate</button>
					</div>
				</div>
			</div>
			<!--end row-->

			<div class="row"></div>
			</div>

		<div class="row">
	    <!-- begin col-3 -->
	    <div class="col-xl-3 col-md-6">
		    <div class="widget widget-stats bg-blue">
			    <div class="stats-icon"><i class="fa fa-desktop"></i></div>
			    <div class="stats-info">
				    <h4>SAMPLES RECEIVED</h4>
				    <p><?php echo $sample ?></p>	
			    </div> 
			    <div class="stats-link">
				    <a href="javascript:;">View Detail <i class="fa fa-arrow-alt-circle-right"></i></a>
			    </div>
		    </div>
	    </div>
	    <!-- end col-3 -->
	    <!-- begin col-3 -->
	    <div class="col-xl-3 col-md-6">
		    <div class="widget widget-stats bg-info">
			    <div class="stats-icon"><i class="fa fa-link"></i></div>
			    <div class="stats-info">
				    <h4>SAMPLES REJECTED</h4>
				    <p><?php echo $reject ?></p>	
			    </div>
			    <div class="stats-link">
				    <a href="javascript:;">View Detail <i class="fa fa-arrow-alt-circle-right"></i></a>
			    </div>
		    </div>
	    </div>
	    <!-- end col-3 -->
	    <!-- begin col-3 -->
	    <div class="col-xl-2 col-md-6">
		    <div class="widget widget-stats bg-orange">
			    <div class="stats-icon"><i class="fa fa-users"></i></div>
			    <div class="stats-info">
				    <h4>SAMPLES ANALYSED</h4>
				    <p><?php echo $analyse ?></p>	
			    </div>
			    <div class="stats-link">
				    <a href="javascript:;">View Detail <i class="fa fa-arrow-alt-circle-right"></i></a>
			    </div>
		    </div>
	    </div>
	    <!-- end col-3 -->
	    <!-- begin col-3 -->
	    <div class="col-xl-2 col-md-3">
		    <div class="widget widget-stats bg-red">
			    <div class="stats-icon"><i class="fa fa-clock"></i></div>
			    <div class="stats-info">
				    <h4>POSITIVE SAMPLES</h4>
				    <p><?php echo $positive ?></p>	
			    </div>
			    <div class="stats-link">
				    <a href="javascript:;">View Detail <i class="fa fa-arrow-alt-circle-right"></i></a>
			    </div>
		    </div>
	    </div>
	    <!-- end col-3 -->
	    <!-- begin col-3 -->
	    <div class="col-xl-2 col-md-3">
		    <div class="widget widget-stats bg-yellow">
			    <div class="stats-icon"><i class="fa fa-clock"></i></div>
			    <div class="stats-info">
				    <h4>INVALID SERIES</h4>
				    <p><?php echo $series ?></p>	
			    </div>
			    <div class="stats-link">
				    <a href="javascript:;">View Detail <i class="fa fa-arrow-alt-circle-right"></i></a>
			    </div>
		    </div>
	    </div>
	    <!-- end col-3 -->
    </div> 
    <!--end row-->
    
    <div class="row">
	<!-- begin col-8 -->
	<div class="col-xl-8">
		<!-- begin panel -->
		<div class="panel panel-inverse" data-sortable-id="index-1">
			<div class="panel-heading">
				<h4 class="panel-title">Overall Samples Received(perFacilities)</h4>
				<div class="panel-heading-btn">
					<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
					<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-redo"></i></a>
					<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
					<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
				</div>
			</div>
			<div class="panel-body pr-1">
				<div id="bar-chart" class="height-sm"></div>
			</div>
		</div>
		<!-- end panel -->
		
		<!-- begin panel -->
		<div class="panel panel-inverse" data-sortable-id="chart-js-1">
			<div class="panel-heading">
				<h4 class="panel-title">Comparison between Data Entries and EQA</h4>
				<div class="panel-heading-btn">
					<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
					<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-redo"></i></a>
					<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
					<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
				</div>
			</div>
			<div class="panel-body">
				<!--<p>
					A line chart is a way of plotting data points on a line.
					Often, it is used to show trend data, and the comparison of two data sets.
				</p>-->
				<div>
					<canvas id="line-chart" data-render="chart-js"></canvas>
				</div>
			</div>
		</div>
		<!-- end panel -->
		
	</div>
	<!-- end col-8 -->
	<!-- begin col-4 -->
	<div class="col-xl-4">
		<!-- begin panel -->
		<div class="panel panel-inverse" data-sortable-id="index-6">
			<div class="panel-heading">
				<h4 class="panel-title">Overall Samples Rejected(per Facility)</h4>
				<div class="panel-heading-btn">
					<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
					<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-redo"></i></a>
					<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
					<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
				</div>
			</div>
			<div class="table-responsive">
				<table class="table table-valign-middle table-panel mb-0">
					<thead>
						<tr>
							<th>Facility</th>
							<th>Rejected Sample</th>
						</tr>
					</thead>
					<tbody>
					<?php
						foreach($rcompare as $row)
						{
							?>
							<tr>
								<td><?php echo $row['facilityname'] ?></td>
								<td><?php echo $row['total'] ?></label></td>
								
							</tr>
						<?php
						}
						?>
						
					</tbody>
				</table>
			</div>
		</div>
		<!-- end panel -->
		
		<!-- begin panel -->
		<div class="panel panel-inverse" data-sortable-id="index-7">
			<div class="panel-heading">
				<h4 class="panel-title">Visitors User Agent</h4>
				<div class="panel-heading-btn">
					<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
					<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-redo"></i></a>
					<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
					<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
				</div>
			</div>
			<div class="panel-body">
				<div id="donut-chart" class="height-sm"></div>
			</div>
		</div>
		<!-- end panel -->
	</div>
	<!-- end col-4 -->
</div>
<!-- end row -->

		</div>
		<!-- end #content -->
		
		<!-- begin scroll to top btn -->
		<a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
		<!-- end scroll to top btn -->
	</div>
	<!-- end page container -->
	
	<!-- ================== BEGIN BASE JS ================== -->
	<script src="assets/js/app.min.js"></script>
	<script src="assets/js/theme/default.min.js"></script>
	<script src="assets/plugins/gritter/js/jquery.gritter.js"></script>
	<script src="assets/plugins/flot/jquery.flot.js"></script>
	<script src="assets/plugins/flot/jquery.flot.pie.js"></script>
	<script src="assets/plugins/chart.js/dist/Chart.min.js"></script>
	<!-- ================== END BASE JS ================== -->
	<script>
	App.setPageTitle('GHLS | Dashboard');
	App.restartGlobalFunction();

	/*$.when(
		$.getScript('assets/plugins/gritter/js/jquery.gritter.js'),
		$.getScript('assets/plugins/flot/jquery.flot.js'),
		$.getScript('assets/plugins/flot/jquery.flot.time.js'),
		$.getScript('assets/plugins/flot/jquery.flot.resize.js'),
		$.getScript('assets/plugins/flot/jquery.flot.pie.js'),
		$.getScript('assets/plugins/jquery-sparkline/jquery.sparkline.min.js'),
		$.getScript('assets/plugins/jvectormap-next/jquery-jvectormap.min.js'),
		$.getScript('assets/plugins/jvectormap-next/jquery-jvectormap-world-mill.js'),
		$.getScript('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.js'),
        $.getScript('assets/plugins/chart.js/dist/Chart.min.js'),
		$.Deferred(function( deferred ){
			$(deferred.resolve);
		})
	).done(function() {
		$.getScript('assets/js/demo/dashboard.js'),
        $.getScript('assets/js/demo/chart-js.demo.js'),
		$.Deferred(function( deferred ){
			$(deferred.resolve);
		})
	});*/

	function reloadRound(year)
	{
		console.log(year);
		window.location="diagnosticstesting.php?year="+year;

	}

	$(document).ready(function() {
		handleBarChart();
	});

	var handleBarChart = function () {
	'use strict';
	if ($('#bar-chart').length !== 0) {

		<?php
		if($recent && count($recent) > 0)
		{
			$results = facilityResult();
			$cont = count($results);
			$data= array();
			$ticks = array();
			for($i=0;$i<$cont;$i++)
			{
				array_push($data,[$i,(float)$results[$i]['total']]);
				array_push($ticks,[$i,$results[$i]['facilityname']]);
			}
			echo "var data = ".json_encode($data).";";
			echo "var ticks = ".json_encode($ticks).";";

	  	}
		?>
				$.plot('#bar-chart', [{ label: 'Results', data: data, color: COLOR_RED }], {
			series: {
				bars: {
					show: true,
					barWidth: 0.6,
					align: 'center',
					fill: true,
					fillColor: COLOR_RED,
					zero: true
				}
			},
			xaxis: {
				tickColor: COLOR_SILVER_TRANSPARENT_3,
				autoscaleMargin: 0.05,
				ticks: ticks
			},
			yaxis: {
				tickColor: COLOR_SILVER_TRANSPARENT_3
			},
			grid: {
				borderColor: COLOR_SILVER_TRANSPARENT_5,
				borderWidth: 1,
				backgroundColor: COLOR_SILVER_TRANSPARENT_1
			},
			legend: {
				noColumns: 0
			},
		});

		
	}
};
	
</script>
</body>
</html>
