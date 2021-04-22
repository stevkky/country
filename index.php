<?php
@session_start();

if(empty($_SESSION['user_email']))
{
	header('location:Login.php');
}
else
{
	header('location:intnatpt.php');
	
}
exit;

header('location:intnatpt.php');

include_once('maindish/savinginputs.php');
include_once('maindish/eqadash.php');

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
 $Flst = getSimpleSetup('facility_types');
 $plst = getSimpleSetup('providers');
 $tlst = getSimpleSetup('testing_method');
 $clst = getSimpleSetup('country');

 $rlst = getSimpleSetup('reasons');
 $Rlst = getSimpleSetup('rounds');
 $Relst = getSimpleSetup('regions');
 $dlst = getSimpleSetup('districts');

 $recent= getRecent($_REQUEST['year'],$_REQUEST['providerid'],$_REQUEST['roundid'],$_REQUEST['countryid']);

 $passediff = array('passed'=>0,'failed'=>0);
 $labs = array('submitted'=>0,'notsubmitted'=>0);
 $eqalabs = array('nat'=>0,'inteqa'=>0);
 $sup =0;
 $total =0;
 $mcompare = array();
 $pasmark = 0;
$rcompare = array();
$regtotal = array();
$roundcomp = array();
$failreasons = array();
 if($recent && count($recent) > 0)
 {
	 $pasmark = getPassMark($recent['providers_id'],$recent['round_id']);

	$passediff= PassedDiff($pasmark,$recent['providers_id'],$recent['round_id'],$recent['country_id']);
	$labs= labResult($recent['providers_id'],$recent['round_id'],$recent['country_id']);
	$eqalabs= eqa($recent['providers_id'],$recent['round_id'],$recent['country_id']);
	$sup = supervised($recent['providers_id'],$recent['round_id'],$recent['country_id']);
	$total = labs($recent['providers_id'],$recent['round_id'],$recent['country_id']);
	$mcompare = methodCompare($pasmark,$recent['providers_id'],$recent['round_id'],$recent['country_id']);
	$rcompare = regionCompare($recent['providers_id'],$recent['round_id'],$recent['country_id']);
	$regtotal = regiontotalCompare($recent['providers_id'],$recent['round_id'],$recent['country_id']);
	$roundcomp = roundCompare($recent['providers_id'],$recent['country_id']);
	$failreasons = failureReasons($recent['providers_id'],$recent['round_id'],$recent['country_id']);


}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>COVID19| EQA Dashboard</title>
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
	<meta content="" name="description" />
	<meta content="" name="author" />
	<style>
    .bs-example{
    	margin: 20px;
    }
	</style>

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
				<a href="index.php" class="navbar-brand"><span class="navbar-logo"></span> <b>COVID 19 </b> &nbsp; EQA DASHBOARD</a>
			</div>
			<!-- end navbar-header -->
			<!-- begin header-nav -->
			<?php 
            include('dashboardmenus.php');
            ?>
			<!-- end header-nav -->
			<!-- end header-nav -->
      	</div>
		<!-- end #header -->
		<div id="content" class="content">
				<div class="row">
					<div class="col-md-12">
						<div class="panel panel-inverse">
							<div class="panel-heading">
								<h4 class="panel-title" data-click="panel-collapse">Filtering Options</h4>
								<div class="panel-heading-btn">
									<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
								</div>
							</div>
							<div class="panel-body">
								<form class="form-inline" method="post" action="">
									<div class="form-group">
										<label>Country</label>
										<select class="form-control mx-sm-3" name="countryid">
										<?php
												while(!$clst->dry())
												{?>
													<option value="<?php echo $clst->id; ?>"><?php echo $clst->name; ?></option>
												<?php
												 $clst->next();
												}
											?>
										</select>
									</div>
									<div class="form-group">
										<label>Provider</label>
										<select class="form-control mx-sm-3" name="providerid">
										<?php
											while(!$plst->dry())
											{?>
												<option <?php if($recent && $plst->id == $recent['providers_id'])  echo 'selected'; elseif($_REQUEST['providerid'] && $_REQUEST['providerid'] == $plst->id) echo 'selected' ?> value="<?php echo $plst->id; ?>"><?php echo $plst->name; ?></option>
											<?php
											$plst->next();
											}
										?>
										</select>
									</div>
									<div class="form-group">
										<label>Year</label>
										<select class="form-control mx-sm-3" name="year" onchange="reloadRound(this.value);">
											<option <?php if($recent && $recent['year'] == 2020)  echo 'selected'; elseif(!empty($_REQUEST['year']) && $_REQUEST['year'] == 2020) echo 'selected' ?> value="2020">2020</option>
											<option <?php if($recent && $recent['year'] == 2021)  echo 'selected'; elseif(!empty($_REQUEST['year']) && $_REQUEST['year'] == 2021) echo 'selected' ?> value="2021">2021</option>
											<!--<option <?php if($recent && $recent['year'] == 2022 )  echo 'selected'; elseif(!empty($_REQUEST['year']) && $_REQUEST['year'] == 2022) echo 'selected' ?> value="2022">2022</option> -->
										</select>
									</div>
									<div class="form-group">
										<label>Round</label>
										<select class="form-control mx-sm-3" name="roundid">
										<?php
										if(!empty($Rlst))
										{
											while(!$Rlst->dry())
											{
												if(($recent && $recent['year'] == $Rlst->year) || ($_REQUEST['year'] == $Rlst->year))
												{
													?>
													<option <?php if($recent && $Rlst->id == $recent['round_id'])  echo 'selected'; elseif($_REQUEST['roundid'] && $_REQUEST['roundid'] == $Rlst->id) echo 'selected' ?> value="<?php echo $Rlst->id; ?>"><?php echo $Rlst->name; ?></option>
													<?php
												}
											$Rlst->next();
											}
										}
										?>
										</select>
									</div>

									<div class="form-group">
										<button type="submit" class="btn btn-sm btn-primary">Generate</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-xl-3 col-md-3">
						<div class="widget widget-stats bg-green">
							<div class="stats-icon"><i class="fa fa-thumbs-up"></i></div>
							<div class="stats-info">
								<h4>PASSED (<?php echo getPassMark($recent['providers_id'],$recent['round_id']);?>)</h4>
								<p><?php echo  $passediff['passed'] ?></p>
							</div>
							<div class="stats-link">
								<a href="javascript:openDetails('PASSED');">View Detail <i class="fa fa-arrow-alt-circle-right"></i></a>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-md-3">
						<div class="widget widget-stats bg-red">
							<div class="stats-icon"><i class="fa fa-thumbs-down"></i></div>
							<div class="stats-info">
							<h4>FAILED (<?php echo getPassMark($recent['providers_id'],$recent['round_id']);?>)</h4>
							<p><?php echo  $passediff['failed'] ?></p>
							</div>
							<div class="stats-link">
								<a href="javascript:openDetails('FAILED');">View Detail <i class="fa fa-arrow-alt-circle-right"></i></a>
							</div>
						</div>
					</div>
					
					<div class="col-xl-3 col-md-3">
						<div class="widget widget-stats bg-blue">
							<div class="stats-icon"><i class="fa fa-thumbs-up"></i></div>
							<div class="stats-info">
								<h4>RESULT SUBMITTED</h4>
								<p><?php echo  $labs['submitted'] ?></p>
							</div>
							<div class="stats-link">
								<a href="javascript:openDetails('SUBMITTED');">View Detail <i class="fa fa-arrow-alt-circle-right"></i></a>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-md-3">
						<div class="widget widget-stats bg-orange">
							<div class="stats-icon"><i class="fa fa-thumbs-down"></i></div>
							<div class="stats-info">
								<h4>RESULTS NOT SUBMITTED</h4>
								<p><?php echo  $labs['notsubmitted'] ?></p>
							</div>
							<div class="stats-link">
								<a href="javascript:openDetails('NOT SUBMITTED');">View Detail <i class="fa fa-arrow-alt-circle-right"></i></a>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<!-- begin col-3 -->
					<div class="col-xl-3 col-md-3">
						<div class="widget widget-stats bg-info">
							<div class="stats-icon"><i class="fa fa-map-marker"></i></div>
							<div class="stats-info">
								<h4>LOCAL EQA</h4>
								<p><?php echo  $eqalabs['nat'] ?></p>
							</div>
							<div class="stats-link">
								<a href="javascript:openDetails('LOCAL EQA LABS');">View Detail <i class="fa fa-arrow-alt-circle-right"></i></a>
							</div>
						</div>
					</div>
					<!-- end col-3 -->
					<!-- begin col-3 -->
					<div class="col-xl-3 col-md-3">
						<div class="widget widget-stats bg-info">
							<div class="stats-icon"><i class="fa fa-globe"></i></div>
							<div class="stats-info">
								<h4>INTERNATIONAL EQA</h4>
								<p><?php echo  $eqalabs['inteqa'] ?></p>
							</div>
							<div class="stats-link">
								<a href="javascript:openDetails('INTERNATIONAL EQA LABS');">View Detail <i class="fa fa-arrow-alt-circle-right"></i></a>
							</div>
						</div>
					</div>
					<!-- end col-3 -->
					<!-- begin col-3 -->
					<div class="col-xl-3 col-md-3">
						<div class="widget widget-stats bg-pink">
							<div class="stats-icon"><i class="fa fa-users"></i></div>
							<div class="stats-info">
								<h4>SUPERVISED LABS</h4>
								<p><?php echo  $sup ?></p>
							</div>
							<div class="stats-link">
								<a href="javascript:openDetails('SUPERVISED LABS');">View Detail <i class="fa fa-arrow-alt-circle-right"></i></a>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-md-3">
						<div class="widget widget-stats bg-cyan">
							<div class="stats-icon"><i class="fa fa-flask"></i></div>
							<div class="stats-info">
								<h4>TOTAL LABS</h4>
								<p><?php echo  $total ?></p>
							</div>
							<div class="stats-link">
								<a href="javascript:openDetails('TOTAL LABS');">View Detail <i class="fa fa-arrow-alt-circle-right"></i></a>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-xl-8">
						
						<div class="panel panel-inverse" data-sortable-id="index-1">
							<div class="panel-heading">
								<h4 class="panel-title">Lab Comparative Results</h4>
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
						
						<div class="panel panel-inverse" data-sortable-id="chart-js-1">
							<div class="panel-heading">
								<h4 class="panel-title">Provider EQA Rounds Comparison</h4>
								<div class="panel-heading-btn">
									<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
									<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-redo"></i></a>
									<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
									<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
								</div>
							</div>
							<div class="panel-body">
								<div>
									<canvas id="line-chart" data-render="chart-js"></canvas>
								</div>
							</div>
						</div>
						
					</div>	


					<div class="col-xl-4">
						<!-- begin panel -->
						<div class="panel panel-inverse" data-sortable-id="index-6">
							<div class="panel-heading">
								<h4 class="panel-title">Method Analytics</h4>
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
											<th>Method</th>
											<th>No. of Labs</th>
											<th>Avg</th>
											<th>No. Passed</th>
										</tr>
									</thead>
									<tbody>
										<?php
										foreach($mcompare as $row)
										{
											$c = 'label-danger';
											if($row['avgresult'] >= $pasmark)
											{
												$c = 'label-green';
											}
											?>

											<tr>
												<!--<td><label class="label label-danger">Unique Visitor</label></td>
												<td>13,203 <span class="text-success"><i class="fa fa-arrow-up"></i></span></td>
														-->
												<td><?php echo $row['method'] ?></td>
												<td><?php echo $row['labs'] ?></td>
												<td><label class="label <?php echo $c;?>"><?php echo $row['avgresult'] ?></label></td>
												<td><?php echo $row['passed'] ?></td>
											</tr>
										<?php
										}
										?>

									</tbody>
								</table>
							</div>
						</div>
						<div class="panel panel-warning" data-sortable-id="index-6">
							<div class="panel-heading">
								<h4 class="panel-title">Non-Submission Reasons</h4>
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
											<th>Reason</th>
											<th>No. of Labs</th>
										</tr>
									</thead>
									<tbody>
										<?php
										foreach($failreasons as $row)
										{
											$c = 'label-default';
											?>

											<tr>
												<td><?php echo $row['reason'] ?></td>
												<td><label class="label <?php echo $c;?>"><?php echo $row['cnt'] ?></label></td>

											</tr>
										<?php
										}
										?>

									</tbody>
								</table>
							</div>
						</div>
						<div class="panel panel-inverse" data-sortable-id="index-6">
							<div class="panel-heading">
								<h4 class="panel-title">Regional  Results(AVG)</h4>
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
											<th>Region</th>
											<th>Avg</th>
										</tr>
									</thead>
									<tbody>
										<?php
										foreach($rcompare as $row)
										{
											$c = 'label-danger';
											if($row['regavg'] >= $pasmark)
											{
												$c = 'label-green';
											}
											?>

											<tr>
												<td><?php echo $row['region'] ?></td>
												<td><label class="label <?php echo $c;?>"><?php echo $row['regavg'] ?></label></td>

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
								<h4 class="panel-title">Regional Participation(%)</h4>
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
						
					</div>


				</div>

		
				

		</div>
		
		<a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
		<!-- end scroll to top btn -->
	</div>
	<!-- end page container -->

	<!-- #modal-dialog -->
	<div class="modal fade" id="modal-dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 id="pop-title" class="modal-title">Modal Dialog</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				</div>
				<div id="pop-content" class="modal-body">
					<p>
						Modal body content here...
					</p>
				</div>
				<div class="modal-footer">
					<a href="javascript:;" class="btn btn-white" data-dismiss="modal">Close</a>
				</div>
			</div>
		</div>
	</div>

	<!-- ================== BEGIN BASE JS ================== -->

	<script src="assets/js/app.min.js"></script>
	<script src="assets/js/theme/default.min.js"></script>
	<script src="assets/plugins/gritter/js/jquery.gritter.js"></script>
	<script src="assets/plugins/flot/jquery.flot.js"></script>
	<script src="assets/plugins/flot/jquery.flot.pie.js"></script>
	<script src="assets/plugins/chart.js/dist/Chart.min.js"></script>

	<!-- ================== END BASE JS ================== -->
	<script>
	App.setPageTitle('EQA | Dashboard');
	App.restartGlobalFunction();

	/*$.when(
		$.getScript('assets/plugins/gritter/js/jquery.gritter.js'),
		$.getScript('assets/plugins/flot/jquery.flot.js'),
		//$.getScript('assets/plugins/flot/jquery.flot.time.js'),
		//$.getScript('assets/plugins/flot/jquery.flot.resize.js'),
		$.getScript('assets/plugins/flot/jquery.flot.pie.js'),
		//$.getScript('assets/plugins/jquery-sparkline/jquery.sparkline.min.js'),
		//$.getScript('assets/plugins/jvectormap-next/jquery-jvectormap.min.js'),
		//$.getScript('assets/plugins/jvectormap-next/jquery-jvectormap-world-mill.js'),
		//$.getScript('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.js'),
        //$.getScript('assets/plugins/chart.js/dist/Chart.min.js'),
		$.Deferred(function( deferred ){
			$(deferred.resolve);
		})
	).done(function() {
		handleBarChart();
		handleDonutChart();
		//$.getScript('assets/js/dash/dashboard.js'),
        //$.getScript('assets/js/dash/chart-js.demo.js'),
		$.Deferred(function( deferred ){
			$(deferred.resolve);
		})
	});
	*/

	function reloadRound(year)
	{
		console.log(year);
		window.location="?year="+year;

	}

	$(document).ready(function() {
		handleBarChart();
		handleDonutChart();
		handleChartJs();
});

var handleBarChart = function () {
	'use strict';
	if ($('#bar-chart').length !== 0) {

		<?php
		if($recent && count($recent) > 0)
		{
			$results = resultcompare($recent['providers_id'],$recent['round_id'],$recent['country_id']);
			$cont = count($results);
			$data= array();
			$ticks = array();
			for($i=0;$i<$cont;$i++)
			{
				array_push($data,[$i,(float)$results[$i]['result']]);
				array_push($ticks,[$i,$results[$i]['facilityname']]);
			}
			echo "var data = ".json_encode($data).";";
			echo "var ticks = ".json_encode($ticks).";";


	   }

		?>

		//var data = [[0, 10], [1, 8], [2, 4], [3, 13], [4, 17], [5, 9]];
		//var ticks = [[0, 'JAN'], [1, 'FEB'], [2, 'MAR'], [3, 'APR'], [4, 'MAY'], [5, 'JUN']];
		$.plot('#bar-chart', [{ label: 'Results', data: data, color: COLOR_DARK_LIGHTER }], {
			series: {
				bars: {
					show: true,
					barWidth: 0.6,
					align: 'center',
					fill: true,
					fillColor: COLOR_DARK_LIGHTER,
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


var handleDonutChart = function () {
	"use strict";
	if ($('#donut-chart').length !== 0) {
	<?php
	$rdata = array();
	foreach($regtotal as $reg)
	{
		//$c =  $reg['regtotal'] >=$pasmark ? '#00ACAC' : '#ff5b57';
		array_push($rdata, array('label'=>$reg['region'].' - '.$reg['regtotal'],'data'=>$reg['regtotal']));
	}

	echo "var donutData =".json_encode($rdata,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES).';';
	//$rcompare
	//$pasmark

	function pickColor() {
		$gencolors = ['#348fe2','#f59c1a','#727cb6','#ff5b57','#b6c2c9','#b6c2c9','#fb5597','#00acac'];
		return $gencolors[rand(0, 7)];
	}

	?>

		/*var donutData = [
			{ label: "Chrome",  data: 35, color: COLOR_PURPLE_DARKER},
			{ label: "Firefox",  data: 30, color: COLOR_PURPLE},
			{ label: "Safari",  data: 15, color: COLOR_PURPLE_LIGHTER},
			{ label: "Opera",  data: 10, color: COLOR_BLUE},
			{ label: "IE",  data: 5, color: COLOR_BLUE_DARKER}
		];
		, COLOR_RED = "#ff5b57"
  , COLOR_RED_LIGHTER = "#ff8481"
  , COLOR_RED_DARKER = "#bf4441"
		*/

		$.plot('#donut-chart', donutData, {
			series: {
				pie: {
					innerRadius: 0.5,
					show: true,
					label: {
						show: true
					}
				}
			},
			legend: {
				show: true
			}
		});
	}
};


/*var lineChartData = {
	labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
	datasets: [{
		label: 'Dataset 1',
		borderColor: COLOR_BLUE,
		pointBackgroundColor: COLOR_BLUE,
		pointRadius: 2,
		borderWidth: 2,
		backgroundColor: COLOR_BLUE_TRANSPARENT_3,
		data: [randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor()]
	}, {
		label: 'Dataset 2',
		borderColor: COLOR_DARK_LIGHTER,
		pointBackgroundColor: COLOR_DARK,
		pointRadius: 2,
		borderWidth: 2,
		backgroundColor: COLOR_DARK_TRANSPARENT_3,
		data: [randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor(), randomScalingFactor()]
	}]
};
*/

var handleChartJs = function() {

	<?php
	$lbls = array();

	$datasets = array();
	$flst->first();
	while(!$flst->dry())
	{
		array_push($lbls,$flst->name);
		$flst->next();
	}

	$Rlst->first();
	while(!$Rlst->dry())
	{
		$dset = array('label'=>$Rlst->name,'pointRadius'=>2,'borderWidth'=>2,'backgroundColor'=>pickColor(),'data'=>array());
		$flst->first();
		$dlst = array();
		while(!$flst->dry())
		{
			$found = false;
			foreach($roundcomp as $rdata)
			{
				if($rdata['facility_id'] == $flst->id  && $rdata['round_id'] == $Rlst->id)
				{
					array_push($dlst,$rdata['result']);
					$found = true;
					break;
				}
			}
			if(!$found)
			{
				array_push($dlst,0);
			}

			$flst->next();
		}

		$dset['data'] = $dlst;
		array_push($datasets,$dset);

		$Rlst->next();
	}

	$chartdata = array('labels'=>$lbls,'datasets'=>$datasets);
	echo "var lineChartData = ".json_encode($chartdata, JSON_PRETTY_PRINT).";";



	//$flst
	//Rlst
//roundcomp
	?>

	var ctx = document.getElementById('line-chart').getContext('2d');
	var lineChart = new Chart(ctx, {
		type: 'line',
		data: lineChartData
	});
};


function openDetails(type)
{
	console.log(type);
	$('#pop-title').html(type);
	$('#pop-content').html("Fetching Data. Please wait...")
	$('#modal-dialog').modal('toggle');

	$.ajax({
    dataType: "html",
    url: 'fetcheqadetails.php?type='+type+'&providerid=<?php echo $recent['providers_id']?>'+'&roundid=<?php echo $recent['round_id']?>'+'&countryid=<?php echo $recent['country_id']?>',
    method: "GET",
    success: function (resp) {
      console.log(resp);
	  $('#pop-content').html(resp);
    }
  });


}
</script>
</body>
</html>
