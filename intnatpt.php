<?php

@session_start();
if(empty($_SESSION['user_email']))
{ 
	header('location:Login.php');
}
include_once('maindish/savinginputs.php');
include_once('maindish/eqadash.php');
include_once('lang.php');


 $lst = getSimpleSetup('eqa_data_entry');
 $flst = getSimpleSetup('facility');
 $Flst = getSimpleSetup('facility_types');
 $tlst = getSimpleSetup('testing_method');

 $rlst = getSimpleSetup('reasons');
 $Rlst = getSimpleSetup('rounds');
 $Relst = getSimpleSetup('regions');
 $dlst = getSimpleSetup('districts');
 $clst = getSimpleSetup('country');


 if(!empty($_SESSION['countryid']))
 {
	$clst = getData('country',array('id=?',$_SESSION['countryid']));
	$_REQUEST['countryid'] = $_SESSION['countryid'];
 }

 $plst = getData('providers',array('countryid=? or countryid is null',$_SESSION['countryid']));

 $recent= getRecent($_REQUEST['year'],$_REQUEST['providerid'],$_REQUEST['roundid'],$_REQUEST['countryid'],'pt_international');

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

	$passediff= PassedDiff($pasmark,$recent['providers_id'],$recent['round_id'],$_REQUEST['countryid'],'pt_international');
	$labs= labResult($recent['providers_id'],$recent['round_id'],$_REQUEST['countryid'],'pt_international');
	$eqalabs= eqa($recent['providers_id'],$recent['round_id'],$_REQUEST['countryid'],'pt_international');
	$sup = supervised($recent['providers_id'],$recent['round_id'],$_REQUEST['countryid'],'pt_international');
	$total = labs($recent['providers_id'],$recent['round_id'],$_REQUEST['countryid'],'pt_international');
	$mcompare = methodCompare($pasmark,$recent['providers_id'],$recent['round_id'],$_REQUEST['countryid'],'pt_international');
	$rcompare = regionCompare($recent['providers_id'],$recent['round_id'],$_REQUEST['countryid'],'pt_international');
	$regtotal = regiontotalCompare($recent['providers_id'],$recent['round_id'],$_REQUEST['countryid'],'pt_international');
	$roundcomp = roundCompare($recent['providers_id'],$_REQUEST['countryid'],'pt_international');
	$failreasons = failureReasons($recent['providers_id'],$recent['round_id'],$_REQUEST['countryid'],'pt_international');


}
if($total > 0)
{
	$passed_100 = number_format(($passediff['passed']/$total) * 100);
	$failed_100 = number_format(($passediff['failed']/$total) * 100);
	$submitted_100 = number_format(($labs['submitted']/$total) * 100);
	$notsubmitted_100 = number_format(($labs['notsubmitted']/$total) * 100);
	$sup_100 = number_format(($sup/$total) * 100);
}

//var_dump($labs);exit;

$regres = empty($_REQUEST['countryid'])? lang('general.CTY_RESULT_AVG'):lang('general.REG_RESULT_AVG');
$regpart = empty($_REQUEST['countryid'])? lang('general.CTY.PART'):lang('general.REG.PART');
$countrymaplink = empty($_REQUEST['countryid'])? 'custom/africa' : 'countries/'.getSimpleSetup('country',$_REQUEST['countryid'])->iso.'/'.getSimpleSetup('country',$_REQUEST['countryid'])->iso.'-all';
$charttitle = empty($_REQUEST['countryid'])? lang('general.AFRICAMAP') : lang('general.MAPOF')." ".strtoupper(getSimpleSetup('country',$_REQUEST['countryid'])->name);

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>COVID19| <?php echo lang('general.EQA_DASH')?></title> 
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
	<meta content="" name="description" />
	<meta content="" name="author" />
	<style>
    .bs-example{
    	margin: 20px;
    }

	#map-chart {
		height: 680px;
    min-width: 310px;
    max-width: 800px;
    margin: 0 auto;
		}

		.loading {
    margin-top: 10em;
    text-align: center;
    color: gray;
}

#apex-mixed-chart {
	width: 100%;
  height: 600px;
}
	</style>

	<!-- ================== BEGIN BASE CSS STYLE ================== -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="assets/css/default/app.min.css" rel="stylesheet" />
    <link href="assets/plugins/jvectormap-next/jquery-jvectormap.css" rel="stylesheet" />
    <link href="assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.css" rel="stylesheet" />
	<link href="assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" />
	<link href="https://github.com/chartjs/chartjs-plugin-annotation.git"/>
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
				<a href="index.php" class="navbar-brand"><img src ="assets/img/virus.png" width="100px" height:="70px" style="margin-bottom:5px" /></i> &nbsp; <b>COVID 19 - <?php echo lang('general.INT_PTT')?> </b> &nbsp;</a>
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
								<h4 class="panel-title" data-click="panel-collapse"><?php echo lang('general.FILTER_OPT') ?></h4>
								<div class="panel-heading-btn">
									<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
								</div>
							</div>
							<div class="panel-body">
								<form class="form-inline" method="post" action="">
									<div class="form-group">
										<label><?php echo lang('general.COUNTRY')?></label>
										<select class="form-control mx-sm-3" name="countryid">
										<?php 
											 if(empty($_SESSION['countryid']))
											 {
												 echo '<option value="">ALL</option>';
;
											 }
										?>
										
										<?php
												while(!$clst->dry())
												{
													if(!empty($_REQUEST['countryid']))
													{?>
														<option <?php if($recent && $clst->id == $recent['country_id'])  echo 'selected'; elseif($_REQUEST['countryid'] && $_REQUEST['countryid'] == $clst->id) echo 'selected' ?> value="<?php echo $clst->id; ?>"><?php echo $clst->name; ?></option>
													<?php
													}
													else
													{?>
														<option value="<?php echo $clst->id; ?>"><?php echo $clst->name; ?></option>
													<?php
													}
												 $clst->next();
												}
											?>
										</select>
									</div>
									<div class="form-group">
										<label><?php echo lang('general.PROVIDER')?></label>
										<select class="form-control mx-sm-3" name="providerid">
										<?php
											while(!$plst->dry())
											{
												if($plst->type == 'international')
												{
													?>
														<option <?php if($recent && $plst->id == $recent['providers_id'])  echo 'selected'; elseif($_REQUEST['providerid'] && $_REQUEST['providerid'] == $plst->id) echo 'selected' ?> value="<?php echo $plst->id; ?>"><?php echo $plst->name; ?></option>
													<?php
												}
											$plst->next();
											}
										?>
										</select>
									</div>
									<div class="form-group">
										<label><?php echo lang('general.YEAR')?></label>
										<select class="form-control mx-sm-3" name="year" onchange="reloadRound(this.value);">
											<option <?php if($recent && $recent['year'] == 2020)  echo 'selected'; elseif(!empty($_REQUEST['year']) && $_REQUEST['year'] == 2020) echo 'selected' ?> value="2020">2020</option>
											<option <?php if($recent && $recent['year'] == 2021)  echo 'selected'; elseif(!empty($_REQUEST['year']) && $_REQUEST['year'] == 2021) echo 'selected' ?> value="2021">2021</option>
											<!--<option <?php if($recent && $recent['year'] == 2022 )  echo 'selected'; elseif(!empty($_REQUEST['year']) && $_REQUEST['year'] == 2022) echo 'selected' ?> value="2022">2022</option> -->
										</select>
									</div>
									<div class="form-group">
										<label><?php echo lang('general.ROUND')?></label>
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
										<button type="submit" class="btn btn-sm btn-primary"><?php echo lang('general.GENERATE')?></button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-xl-4 col-md-4">
						<div class="widget widget-stats <?php echo getColor($passed_100) ?>">
							<div class="stats-icon"><i class="fa fa-thumbs-up"></i></div>
							<div class="stats-info">
								<h4><?php echo lang('general.PASSED')?> (<?php echo getPassMark($recent['providers_id'],$recent['round_id']);?>)</h4>
								<p><?php echo  $passediff['passed'] .' ('. $passed_100.'%)' ?></p>
							</div>
							<div class="stats-link">
								<a href="javascript:openDetails('<?php echo lang('general.PASSED')?>','PASSED');"><?php echo lang('general.VIEW_DETAILS')?> <i class="fa fa-arrow-alt-circle-right"></i></a>
							</div>
						</div>
					</div>
					<div class="col-xl-4 col-md-4">
						<div class="widget widget-stats <?php echo getColor($failed_100) ?>">
							<div class="stats-icon"><i class="fa fa-thumbs-down"></i></div>
							<div class="stats-info">
							<h4><?php echo lang('general.FAILED')?> (<?php echo getPassMark($recent['providers_id'],$recent['round_id']);?>)</h4>
							<p><?php echo  $passediff['failed'] .' ('. $failed_100.'%)' ?></p>
							</div>
							<div class="stats-link">
								<a href="javascript:openDetails('<?php echo lang('general.FAILED')?>','FAILED');"><?php echo lang('general.VIEW_DETAILS')?> <i class="fa fa-arrow-alt-circle-right"></i></a>
							</div>
						</div>
					</div>
					
					<div class="col-xl-4 col-md-4">
						<div class="widget widget-stats <?php echo getColor($submitted_100) ?>">
							<div class="stats-icon"><i class="fa fa-thumbs-up"></i></div>
							<div class="stats-info">
								<h4><?php echo lang('general.RESULT_SUBMIT')?></h4>
								<p><?php echo  $labs['submitted'].' ('. $submitted_100.'%)' ?></p>
							</div>
							<div class="stats-link">
								<a href="javascript:openDetails('<?php echo lang('general.RESULT_SUBMIT')?>','SUBMITTED');"><?php echo lang('general.VIEW_DETAILS')?> <i class="fa fa-arrow-alt-circle-right"></i></a>
							</div>
						</div>
					</div>
					
				</div>

				<div class="row">
					<!-- end col-3 -->
					<!-- begin col-3 -->
					<div class="col-xl-4 col-md-4">
						<div class="widget widget-stats <?php echo getColor($sup_100) ?>">
							<div class="stats-icon"><i class="fa fa-users"></i></div>
							<div class="stats-info">
								<h4> <?php echo lang('general.DOC_CORRECTION')?></h4>
								<p><?php echo  $sup.' ('. $sup_100.'%)' ?></p>
							</div>
							<div class="stats-link">
								<a href="javascript:openDetails('<?php echo lang('general.DOC_CORRECTION')?>','SUPERVISED LABS');"><?php echo lang('general.VIEW_DETAILS')?> <i class="fa fa-arrow-alt-circle-right"></i></a>
							</div>
						</div>
					</div>
					<div class="col-xl-4 col-md-4">
						<div class="widget widget-stats bg-cyan">
							<div class="stats-icon"><i class="fa fa-flask"></i></div>
							<div class="stats-info">
								<h4><?php echo lang('general.TOTAL_LABS')?></h4>
								<p><?php echo  $total ?></p>
							</div>
							<div class="stats-link">
								<a href="javascript:openDetails('<?php echo lang('general.TOTAL_LABS')?>','TOTAL LABS');"><?php echo lang('general.VIEW_DETAILS')?> <i class="fa fa-arrow-alt-circle-right"></i></a>
							</div>
						</div>
					</div>
					<div class="col-xl-4 col-md-4">
						<div class="widget widget-stats <?php echo getColor($notsubmitted_100) ?>">
							<div class="stats-icon"><i class="fa fa-thumbs-down"></i></div>
							<div class="stats-info">
								<h4><?php echo lang('general.RESULT_NOT_SUBMIT')?></h4>
								<p><?php echo  $labs['notsubmitted'].' ('. $notsubmitted_100.'%)' ?></p>
							</div>
							<div class="stats-link">
								<a href="javascript:openDetails('<?php echo lang('general.RESULT_NOT_SUBMIT')?>','NOT SUBMITTED');"><?php echo lang('general.VIEW_DETAILS')?> <i class="fa fa-arrow-alt-circle-right"></i></a>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-xl-8">
						
						<div class="panel panel-inverse" data-sortable-id="index-1">
							<div class="panel-heading">
								<h4 class="panel-title"><?php echo lang('general.LAB_COMP_RESULT')?></h4>
								<div class="panel-heading-btn">
									<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
									<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-redo"></i></a>
									<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
									<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
								</div>
							</div>
							<div class="panel-body pr-1">
								<!--<div id="bar-chart" class="height-sm"></div> -->
								<div id="apex-mixed-chart"> </div>
							</div>
						</div>
						
						<div class="panel panel-inverse" data-sortable-id="chart-js-1">
							<div class="panel-heading">
								<h4 class="panel-title"><?php echo lang('general.MAP')?></h4>
								<div class="panel-heading-btn">
									<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
									<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-redo"></i></a>
									<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
									<a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
								</div>
							</div>
							<div class="panel-body">
								<div id="map-chart"></div>
							</div>
						</div>
						
					</div>	


					<div class="col-xl-4">
						<!-- begin panel -->
						<div class="panel panel-inverse" data-sortable-id="index-6">
							<div class="panel-heading">
								<h4 class="panel-title"><?php echo lang('general.METHOD_ANLYT')?></h4> 
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
											<th><?php echo lang('general.METHOD')?></th>
											<th><?php echo lang('general.NO_LABS')?></th>
											<th>Avg</th>
											<th><?php echo lang('general.NO_PASSED')?></th>
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
												<td><label class="label <?php echo $c;?>"><?php echo number_format($row['avgresult']) ?></label></td>
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
								<h4 class="panel-title"><?php echo lang('general.NON_SUB_REASON')?></h4>
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
											<th><?php echo lang('general.REASON')?></th>
											<th><?php echo lang('general.NO_LABS')?></th>
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
								<h4 class="panel-title"><?php echo $regres; ?></h4>
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
											<th><?php echo lang('general.COUNTRY')?></th>
											<th><?php echo lang('general.REGION')?></th>
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
												<td><?php echo $row['country'] ?></td>
												<td><?php echo $row['region'] ?></td>
												<td><label class="label <?php echo $c;?>"><?php echo number_format($row['regavg']) ?></label></td>

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
								<h4 class="panel-title" > <?php echo $regpart; ?></h4>
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
					<h4 id="pop-title" class="modal-title"> <?php echo lang('general.MODAL_DIALOG')?></h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				</div>
				<div id="pop-content" class="modal-body">
					<p>
					<?php echo lang('general.MODAL_CON')?>
					</p>
				</div>
				<div class="modal-footer">
					<a href="javascript:;" class="btn btn-white" data-dismiss="modal"><?php echo lang('general.CLOSE')?></a>
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
	<script src="assets/plugins/apexcharts/dist/apexcharts.min.js"></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.3.6/proj4.js"></script>
	<script src="https://code.highcharts.com/maps/highmaps.js"></script>
	<script src="https://code.highcharts.com/maps/modules/exporting.js"></script>
	<script src="https://code.highcharts.com/maps/modules/offline-exporting.js"></script>
	<script src="https://code.highcharts.com/mapdata/<?php echo $countrymaplink.'.js'; ?>"></script>

	<script src="https://www.amcharts.com/lib/4/core.js"></script>
	<script src="https://www.amcharts.com/lib/4/charts.js"></script>
	<script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>
	<!--<script src="https://www.amcharts.com/lib/4/themes/kelly.js"></script> -->
	

	<!-- ================== END BASE JS ================== -->
	<script>
	App.setPageTitle('EQA | <?php echo lang('general.DASHBOARD')?>');
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
		//handleChartJs();
		//handleMixedChart();
		handleAMCharts();

		loadMapData();
});

var handleBarChart = function () {
	'use strict';
	if ($('#bar-chart').length !== 0) {
		

		<?php
		if($recent && count($recent) > 0)
		{
			$results = resultcompare($recent['providers_id'],$recent['round_id'],$_REQUEST['countryid'],'pt_international');
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
		/*	var lineChart = new Chart(speedCanvas, {
    		type: 'line',
    		data: speedData,
    		options: chartOptions
		});
			var speedData = {
  			labels: ["0s", "10s", "20s", "30s", "40s", "50s", "60s"],
  			datasets: [{
    		label: "Car Speed",
    		data: [0, 59, 75, 20, 20, 55, 40],
  		}]
		};
 
			var chartOptions = {
  			legend: { 
    		display: true,
    		position: 'top',	
    		labels: {
      		boxWidth: 80,
      		fontColor: 'black'
    		}
  		}
		};*/

		//var data = [[0, 10], [1, 8], [2, 4], [3, 13], [4, 17], [5, 9]];
		//var ticks = [[0, 'JAN'], [1, 'FEB'], [2, 'MAR'], [3, 'APR'], [4, 'MAY'], [5, 'JUN']];
		$.plot('#bar-chart', [{ label: '<?php echo lang('general.RESULTS')?>', data: data, color: COLOR_DARK_LIGHTER }], {
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
                    show: true,
                    radius: 3/4,
                    formatter: function(label, series){
                        return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">'+Math.round(series.percent)+'%</div>';
                    },
                    background: {
                        opacity: 0.5,
                        color: '#000'
                    }
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


function openDetails(title,type)
{
	console.log(title);
	$('#pop-title').html(title);
	$('#pop-content').html("<?php echo lang('general.FET_DATA')?>")
	$('#modal-dialog').modal('toggle');

	$.ajax({
    dataType: "html",
    url: 'fetcheqadetails.php?type='+type+'&providerid=<?php echo $recent['providers_id']?>'+'&roundid=<?php echo $recent['round_id']?>'+'&countryid=<?php echo $_REQUEST['countryid']?>'+'&dtype=<?php echo $recent['type'] ?>',
    method: "GET",
    success: function (resp) {
      console.log(resp);
	  $('#pop-content').html(resp);
    }
  });

}


var handleAMCharts = function ()
{
	<?php
	//var_dump($recent);
	if($recent && count($recent) > 0)
	{
		$results = resultcompare($recent['providers_id'],$recent['round_id'],$_REQUEST['countryid'],'pt_international');
		//var_dump($results);
		$cont = count($results);
		$data= array();
		for($i=0;$i<$cont;$i++)
		{
			array_push($data, array('country'=>$results[$i]['country'],'lab'=>$results[$i]['facilityname'],'result'=>(float)$results[$i]['result'],'passmark'=>$pasmark));
		}
	

   }
	?>

am4core.useTheme(am4themes_animated);
//am4core.useTheme(am4themes_kelly);
var chart = am4core.create("apex-mixed-chart", am4charts.XYChart);
chart.scrollbarX = new am4core.Scrollbar();

chart.data = <?php echo json_encode($data)?>;

// Create axes
let categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
categoryAxis.dataFields.category = "lab";
categoryAxis.title.text = "<?php echo lang('general.LAB')?>";
categoryAxis.renderer.grid.template.location = 0;
categoryAxis.renderer.minGridDistance = 30;
categoryAxis.renderer.labels.template.horizontalCenter = "right";
categoryAxis.renderer.labels.template.verticalCenter = "middle";
categoryAxis.renderer.labels.template.rotation = 270;

let valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
valueAxis.title.text = "<?php echo lang('general.RESULTS')?>";
valueAxis.min = -5;
valueAxis.max = <?php echo $pasmark.';'?>;
valueAxis.strictMinMax = true; 

// Create series
var series = chart.series.push(new am4charts.ColumnSeries());
series.dataFields.valueY = "result";
series.dataFields.categoryX = "lab";
series.name = "<?php echo lang('general.RESULTS')?>";
series.columns.template.tooltipText = "<?php echo lang('dataEntry.FACILITY') ?>: {categoryX}\n<?php echo lang('general.RESULTS') ?>: {valueY}";
series.columns.template.fill = am4core.color("#104547");

var columnTemplate = series.columns.template;
//columnTemplate.fillOpacity = .8;
//columnTemplate.strokeOpacity = 0;
columnTemplate.adapter.add("fill", function(fill, target) {
  if (target.dataItem && (target.dataItem.valueY < <?php echo $pasmark ?>)) {
    return am4core.color("#ff5b57");
  }
  else {
    return fill;
  }
});



var series2 = chart.series.push(new am4charts.LineSeries());
series2.name = "<?php echo lang('dataEntry.PASSMARK')?>";
series2.stroke = am4core.color("#CDA2AB");
series2.strokeWidth = 10;
series2.dataFields.valueY = "passmark";
series2.dataFields.categoryX = "lab";


// Add legend
chart.legend = new am4charts.Legend();
chart.legend.position = "top";
// Add cursor
chart.cursor = new am4charts.XYCursor();

// Add simple vertical scrollbar
//chart.scrollbarY = new am4core.Scrollbar();

// Add horizotal scrollbar with preview
//var scrollbarX = new am4charts.XYChartScrollbar();
////scrollbarX.series.push(series);
//chart.scrollbarX = scrollbarX;
//chart.scrollbarX.parent = chart.bottomAxesContainer;



}

var handleMixedChart = function() {

	<?php
	//var_dump($recent);
	if($recent && count($recent) > 0)
	{
		$results = resultcompare($recent['providers_id'],$recent['round_id'],$_REQUEST['countryid'],'pt_international');
		//var_dump($results);
		$cont = count($results);
		$data= array();
		$ticks = array();
		$top= array();
		for($i=0;$i<$cont;$i++)
		{
			array_push($data,(float)$results[$i]['result']);
			array_push($ticks,$results[$i]['facilityname']);
			array_push($top,$pasmark);
		}
		//echo "var data = ".json_encode($data).";";
		//echo "var ticks = ".json_encode($ticks).";";


   }

		?>
	var options = {
		chart: {
			height: 350,
			type: 'line',
			stacked: false,
			zoom: {
			enabled: true
		}
		},
		selection: {
			enabled: true
		},
		dataLabels: {
			enabled: false
		},
		series: [{
			name: "<?php echo lang('general.RESULTS')?>",
			type: 'column',
			data: <?php echo json_encode($data)?>
		}, {
			name: "<?php echo lang('dataEntry.PASSMARK')?>",
			type: 'line',
			data: <?php echo json_encode($top)?>
		}],
		stroke: {
			width: [ 0, 3]
		},
		colors: [COLOR_BLUE_DARKER,COLOR_TEAL],
		xaxis: {
			categories: <?php echo json_encode($ticks).','?>
			axisBorder: {
				show: true,
				color: COLOR_SILVER_TRANSPARENT_5,
				height: 1,
				width: '100%',
				offsetX: 0,
				offsetY: -1
			},
			axisTicks: {
				show: true,
				borderType: 'solid',
				color: COLOR_SILVER,
				height: 6,
				offsetX: 0,
				offsetY: 0
			}
		},
		yaxis: [{
			axisTicks: {
				show: true,
			},
			axisBorder: {
				show: true,
				color: COLOR_BLUE_DARKER
			},
			labels: {
				style: {
					color: COLOR_BLUE_DARKER
				}
			},
			title: {
				text: "<?php echo lang('general.RESULTS')?>",
				style: {
					color: COLOR_BLUE_DARKER
				}
			},
			tooltip: {
				enabled: true
			}
		}],
		tooltip: {
			fixed: {
				enabled: true,
				position: 'topLeft', // topRight, topLeft, bottomRight, bottomLeft
				offsetY: 30,
				offsetX: 60
			},
		},
		legend: {
			horizontalAlign: 'left',
			offsetX: 40
		}
	};

	var chart = new ApexCharts(
		document.querySelector('#apex-mixed-chart'),
		options
	);

	chart.render();

	
};

var loadMapData = function() {

<?php
$data = fetchLabsForMap($recent['providers_id'],$recent['round_id'],$_REQUEST['countryid'],$recent['type']);
?>


	Highcharts.mapChart('map-chart', {

chart: {
    map: <?php  if(empty($_REQUEST['countryid'])) echo '"custom/africa"'; else echo "'".$countrymaplink."'"; ?>
},

title: {
    text: <?php echo '"'.$charttitle.'"'; ?>
},

mapNavigation: {
    enabled: true
},

tooltip: {
    headerFormat: '',
    pointFormat: '<b>{point.name}</b><br>Lat: {point.lat}, Lon: {point.lon}'
},
credits: {
            style: {
                color: "#ffffff"
            }
        },
series: [{
    // Use the gb-all map with no data as a basemap
    name: 'Basemap',
    borderColor: '#A0A0A0',
    nullColor: 'rgba(200, 200, 200, 0.3)',
    showInLegend: false
}, {
    name: 'Separators',
    type: 'mapline',
    nullColor: '#707070',
    showInLegend: false,
    enableMouseTracking: false
}, {
    // Specify points using lat/lon
    type: 'mappoint',
    name: '<?php echo lang('general.LAB_LOCATIONS')?>',
    color: Highcharts.getOptions().colors[1],
    data:  <?php echo json_encode($data,JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK); ?>
}]
});


};
</script>
</body>
</html>
