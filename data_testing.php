<?php
@session_start();
if(empty($_SESSION['user_email']))
{
	header('location:Login.php');
}
include_once('maindish/savinginputs.php');

$editname = null;
$editname1 = null;
$editname2 = null;
$editname3 = null;
$editname4 = null;
$editstatus= null;
$id= empty($_REQUEST['id']) ? null : $_REQUEST['id'];
$item =null;
 
 if(isset($_POST['submit']))
 {
	saveSimpleSetup('testing_data',$id);
 }

 if(isset($_REQUEST['id']))
 {
	$item = getSimpleSetup('testing_data',$_REQUEST['id']);
	$editname = $item->no_samples_receive;
	$editname1 = $item->no_samples_reject;
	$editname2 = $item->no_invalid_series;
	$editname3 = $item->no_samples_analyzed;
	$editname4 = $item->no_positive_samples;
	$editstatus =$item->resultsready_ontime;

 }


 $lst = getSimpleSetup('testing_data');
 $flst = getSimpleSetup('facility');
 $rlst = getSimpleSetup('reasons');
 $clst = getSimpleSetup('country');
 $flst = getData('facility',array('countryid=?', $clst->id));
 

 
 if(!empty($_SESSION['countryid']))
 {
	$clst = getData('country',array('id=?',$_SESSION['countryid']));
	$lst = getData('testing_data',array('country_id=?',$_SESSION['countryid']));
	$flst = getData('facility',array('countryid=?',$_SESSION['countryid']));
 }

 if(!empty($_REQUEST['countryid']) && $_REQUEST['countryid'] > 0)
 {
	$lst = getData('testing_data',array('country_id=?',$_REQUEST['countryid']));
	$flst = getData('facility',array('countryid=?',$_REQUEST['countryid']));
	//$plst = getData('providers',array('countryid=? and type=?',$_REQUEST['countryid'],'international'));
 }

 include_once('header.php');
 include_once('lang.php');
?>

		<!-- begin #content -->
		<div id="content" class="content">
			<!-- begin breadcrumb -->
			
			<!-- end breadcrumb -->
			<!-- begin page-header -->
			<h1 class="page-header"><small><?php echo lang('dataEntry.TEST_DATA_ENT')?></small></h1>
			<!-- end page-header -->
			
		<!-- begin panel -->
        <div class="row">
            <div class="col-xl-8 ui-sortable">
                <div class="panel panel-inverse">
				    <div class="panel-heading">
					    <h4 class="panel-title"><?php echo lang('dataEntry.TEST_DATA_LIST')?></h4>
					    <div class="panel-heading-btn">
						    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
						    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-redo"></i></a>
						    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
						    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
					    </div>
				    </div>
				    <div class="panel-body">
                        <table id="data-table-fixed-columns" class="table table-striped table-bordered">
					        <thead>
						        <tr>
								<th><?php echo lang('general.COUNTRY')?></th>
							    <th><?php echo lang('general.LAB')?></th>
							    <th><?php echo lang('dataEntry.MONTH')?></th>
							    <th ><?php echo lang('general.YEAR')?></th>
							    <th ><?php echo lang('dataEntry.SAMP_REC')?></th>
							    <th ><?php echo lang('dataEntry.SAMP_REJ')?></th>
							    <th ><?php echo lang('dataEntry.SAMP_ANA')?></th>
							    <th ><?php echo lang('dataEntry.INVALID_SERIES')?></th>
							    <th ><?php echo lang('dataEntry.POS_SAMP')?></th>
							    <th ><?php echo lang('dataEntry.RES_READY_TIME')?></th>
							    <th ></th>
						        </tr>
					        </thead>
					        <tbody>
							<?php
									 while(!$lst->dry())
									 {?>
									  	<tr>
										  	<td> <?php echo getSimpleSetup('country',$lst->country_id)->name; ?></td>
											<td> <?php echo getSimpleSetup('facility',$lst->facility_id)->name; ?></td>
											<td> <?php  echo lang('year.'.strtoupper(getMonthName($lst->month))); ?></td>
											<td> <?php echo $lst->year; ?></td>
											<td> <?php echo $lst->no_samples_receive; ?></td>
											<td> <?php echo $lst->no_samples_reject; ?></td>
											<td> <?php echo $lst->no_samples_analyzed; ?></td>
											<td> <?php echo $lst->no_invalid_series; ?></td>
											<td> <?php echo $lst->no_positive_samples; ?></td>
											<td> <?php echo $lst->resultsready_ontime; ?></td>
											<td><a href="data_testing.php?id=<?php echo $lst->id.'&countryid='.$lst->country_id; ?>"> <?php echo lang('dataEntry.EDIT')?></a></td>
										</tr>
									 <?php
										 $lst->next();
									 }
								?>
					        </tbody>
				        </table>
				    </div>
			    </div>
            </div>

            <div class="col-xl-4 ui-sortable">
                <div class="panel panel-inverse">
				    <div class="panel-heading">
					    <h4 class="panel-title"><?php echo lang('dataEntry.ADD_DATA')?></h4>
					    <div class="panel-heading-btn">
						    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
						    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-redo"></i></a>
						    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
						    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
					    </div>
				    </div>
				    <div class="panel-body">
					    <form class="form-horizontal" data-parsley-validate="true" name="demo-form" method="POST">
						<div class="form-group row m-b-15">
						        	<label class="col-form-label col-md-4"><?php echo lang('general.COUNTRY')?> :</label>
						            <div class="col-md-8">
									<select class="form-control" name="countryid" onchange="self.location='<?php echo $_SERVER['PHP_SELF'];?>?countryid='+this.value">
											<?php
												while(!$clst->dry())
												{?>
													<option <?php if($item->country_id==$clst->id) echo 'selected'; else if(!empty($_REQUEST['countryid']) && $_REQUEST['countryid'] == $clst->id) echo 'selected'; ?> value="<?php echo $clst->id; ?>"><?php echo $clst->name; ?></option>
												<?php
												 $clst->next();
												}
											?>
							            </select>
						            </div>
                        </div>
						<div class="form-group row m-b-15">
						        <label class="col-form-label col-md-4"><?php echo lang('dataEntry.FACILITY')?> :</label>
						            <div class="col-md-8">
									<select class="form-control" name="facilityid">
											<?php
												while(!$flst->dry())
												{?>
													<option <?php if(!empty($item) && $item->facility_id == $flst->id) echo 'selected' ?> value="<?php echo $flst->id; ?>"><?php echo $flst->name; ?></option>
												<?php
												 $flst->next();
												}
											?>
							            </select>
						            </div>
                            </div>
						<div class="form-group row m-b-15">
						    <label class="col-form-label col-md-4"><?php echo lang('dataEntry.MONTH')?> :</label>
						        <div class="col-md-8">
							        <select class="form-control" name="monthid">
								       <option <?php if($item->month == '1') echo 'selected'; ?> value="1"><?php echo lang('year.JAN'); ?></option>
								        <option <?php if($item->month == '2') echo 'selected'; ?> value="2"><?php echo lang('year.FEB'); ?></option>
								        <option <?php if($item->month == '3') echo 'selected'; ?> value="3"><?php echo lang('year.MAR'); ?></option>
								        <option <?php if($item->month == '4') echo 'selected'; ?> value="4"><?php echo lang('year.APR'); ?></option>
								        <option <?php if($item->month == '5') echo 'selected'; ?> value="5"><?php echo lang('year.MAY'); ?></option>
								        <option <?php if($item->month == '6') echo 'selected'; ?> value="6"><?php echo lang('year.JUN'); ?></option>
								        <option <?php if($item->month == '7') echo 'selected'; ?> value="7"><?php echo lang('year.JUL'); ?></option>
								        <option <?php if($item->month == '8') echo 'selected'; ?> value="8"><?php echo lang('year.AUG'); ?></option>
								        <option <?php if($item->month == '9') echo 'selected'; ?> value="9"><?php echo lang('year.SEP'); ?></option>
								        <option <?php if($item->month == '10') echo 'selected'; ?> value="10"><?php echo lang('year.OCT'); ?></option>
								        <option <?php if($item->month == '11') echo 'selected'; ?> value="11"><?php echo lang('year.NOV'); ?></option>
								        <option <?php if($item->month == '12') echo 'selected'; ?> value="11"><?php echo lang('year.DEC'); ?></option>
							        </select>
						        </div>
                        </div>
						<div class="form-group row m-b-15">
						        <label class="col-form-label col-md-4"><?php echo lang('general.YEAR')?> :</label>
						            <div class="col-md-8">
									<select class="form-control" name="yearid">
								        <option <?php if(!empty($item) && $item->year == 2020) echo 'selected' ?> value="2020">2020</option>
								        <option <?php if(!empty($item) && $item->year == 2021) echo 'selected' ?> value="2021">2021</option>
								        
                                        </select>
						            </div>
					    </div>
					        <div class="form-group row m-b-15">
						        <label class="col-md-4 col-sm-4 col-form-label" for="samplesreceived"><?php echo lang('dataEntry.SAMP_REC')?> :</label>
						            <div class="col-md-8 col-sm-8">
							            <input class="form-control" type="number" required value="<?php echo $editname; ?>" id="samplesreceived" name="samplesreceived" data-parsley-required="true" />
						            </div>
                            </div>
					        <div class="form-group row m-b-15">
						        <label class="col-md-4 col-sm-4 col-form-label" for="rejectedsamples"><?php echo lang('dataEntry.SAMP_REJ')?> :</label>
						            <div class="col-md-8 col-sm-8">
							            <input class="form-control" type="number" required value="<?php echo empty($editname1) ? 0 : $editname1; ?>" id="rejectedsamples" name="rejectedsamples"  data-parsley-required="true" />
						            </div>
                            </div>
					        <div class="form-group row m-b-15">
						        <label class="col-md-4 col-sm-4 col-form-label" for="invalidseries"><?php echo lang('dataEntry.INVALID_SERIES')?> :</label>
						            <div class="col-md-8 col-sm-8">
							            <input class="form-control" type="number" required value="<?php echo empty($editname2)? 0:$editname2; ?>" id="invalidseries" name="invalidseries"  data-parsley-required="true" />
						            </div>
                            </div>
					       
					        <div class="form-group row m-b-15">
						        <label class="col-md-4 col-sm-4 col-form-label" for="analyzedsamples"><?php echo lang('dataEntry.SAMP_ANA')?> :</label>
						            <div class="col-md-8 col-sm-8">
							            <input class="form-control" type="number" required value="<?php echo empty($editname3)? 0:$editname3; ?>" id="analyzedsamples" name="analyzedsamples"  data-parsley-required="true" />
						            </div>
                            </div>
					        <div class="form-group row m-b-15">
						        <label class="col-md-4 col-sm-4 col-form-label" for="positivesamples"><?php echo lang('dataEntry.POS_SAMP')?> :</label>
						            <div class="col-md-8 col-sm-8">
							            <input class="form-control" type="number" required value="<?php echo empty($editname4)? 0:$editname4 ; ?>" id="positivesamples" name="positivesamples" data-parsley-required="true" />
						            </div>
                            </div>
					        <div class="form-group row m-b-15">
							<label class="col-form-label col-md-4"><?php echo lang('dataEntry.RES_READY_TIME')?> :</label>
						            <div class="col-md-8">
										<input class="form-control" type="number" required value="<?php echo empty($editstatus) ? 0 :$editstatus ; ?>" id="resultreadyontime" name="resultreadyontime" data-parsley-required="true" />
									</div>
					        </div> 
                            

                            <div class="form-group row m-b-0">
							<label class="col-md-4 col-sm-4 col-form-label">&nbsp;</label>
								<div class="col-md-8 col-sm-8">
							        <button type="submit" name="submit" value="submit" class="btn btn-primary"><?php echo lang('dataEntry.SUBMIT')?></button>
									<button type="reset" class="btn btn-info" onclick="window.location='<?php echo $_SERVER['PHP_SELF'] ?>'"><?php echo lang('general.NEW')?></button>
						        </div>
					        </div>			
				        </form>
				    </div>
			    </div>
            </div>
        </div>
        
           
        
			<!-- end panel -->			
		<!-- begin scroll to top btn -->
            <!-- begin #footer -->
		
		<!-- end #footer -->
		<a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
		<!-- end scroll to top btn -->
	</div>
	<!-- end page container -->
	
	<!-- ================== BEGIN BASE JS ================== -->
	<script src="assets/js/app.min.js"></script>
    <script src="assets/js/theme/default.min.js"></script>
    <script>
	App.setPageTitle('COVID 19- <?php echo lang('general.DASHBOARD')?>');
	App.restartGlobalFunction();
    
	$.getScript('assets/plugins/datatables.net/js/jquery.dataTables.min.js').done(function() {
		$.when(
			$.getScript('assets/plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js'),
			$.getScript('assets/plugins/datatables.net-fixedcolumns/js/dataTables.fixedcolumns.min.js'),
			$.getScript('assets/plugins/datatables.net-fixedcolumns-bs4/js/fixedcolumns.bootstrap4.min.js'),
			$.Deferred(function( deferred ){
				$(deferred.resolve);
			})
		).done(function() {
			$.getScript('assets/js/demo/table-manage-fixed-columns.demo.js'),
			$("#data-table-default").DataTable({responsive:true});
			$.Deferred(function( deferred ){
				$(deferred.resolve);
			})
		});
	});
</script>
	<!-- ================== END BASE JS ================== -->
</body>
</html>
