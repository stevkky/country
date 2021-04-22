<?php
@session_start();
if(empty($_SESSION['user_email']))
{
	header('location:Login.php');
}
include_once('maindish/savinginputs.php');

$editname = null;
$editstatus= null;
$editstatus1= null;
$editstatus2= null;
$item = null;

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
	$editstatus3 =$item->is_enrolled_Intpt;
 }


 $lst = getData('eqa_data_entry',array('type=?','pt_international'));
 $tlst = getSimpleSetup('testing_method');

 $rlst = getSimpleSetup('reasons');
 $Rlst = getSimpleSetup('rounds');
 $clst = getSimpleSetup('country');
 $flst = getData('facility',array('countryid=?', $clst->id));

 $plst = getData('providers',array('type=?','international'));

 if(!empty($_SESSION['countryid']))
 {
	$clst = getData('country',array('id=?',$_SESSION['countryid']));
	$lst = getData('eqa_data_entry',array('type=? and country_id=?','pt_international',$_SESSION['countryid']));
	$flst = getData('facility',array('countryid=?',$_SESSION['countryid']));
 }

 if(!empty($_REQUEST['countryid']) && $_REQUEST['countryid'] > 0)
 {
	$lst = getData('eqa_data_entry',array('type=? and country_id=?','pt_international',$_REQUEST['countryid']));
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
			<h1 class="page-header"><small><?php echo lang('dataEntry.INT_EQA_DATA')?></small></h1>
			<!-- end page-header -->
			
		<!-- begin panel -->
        <div class="row">
        <div class="col-xl-8 ui-sortable">
                <div class="panel panel-inverse">
				    <div class="panel-heading">
					    <h4 class="panel-title"><?php echo lang('dataEntry.INT_EQA_LIST')?></h4>
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
								<th class="text-nowrap"><?php echo lang('general.PROVIDER')?></th>
								<th class="text-nowrap"><?php echo lang('general.ROUND')?></th>
								<th class="text-nowrap"><?php echo lang('dataEntry.FACILITY')?></th>
								<th class="text-nowrap"><?php echo lang('general.COUNTRY')?></th>
								<th class="text-nowrap"><?php echo lang('general.YEAR')?></th>
							    <th class="text-nowrap"><?php echo lang('dataEntry.MONTH')?></th>
							    <th class="text-nowrap"><?php echo lang('dataEntry.RESULT')?></th>
							    <th class="text-nowrap"><?php echo lang('dataEntry.SUPERVISED')?></th>
								<th class="text-nowrap"><?php echo lang('dataEntry.RES_SUB')?></th>
							    <th class="text-nowrap"><?php echo lang('general.REASON')?></th>
							    <th class="text-nowrap"><?php echo lang('dataEntry.TEST_METHOD')?></th>
								<th class="text-nowrap"><?php echo lang('general.DOC_CORRECTION')?></th>
							    <th class="text-nowrap"></th> 
						        </tr>
					        </thead>
					        <tbody>
							<?php
									 while(!$lst->dry())
									 {?>
									  	<tr>
										  	<td> <?php echo getSimpleSetup('providers',$lst->providers_id)->name; ?></td>
											<td> <?php echo getSimpleSetup('rounds',$lst->round_id)->name;?></td>
											<td> <?php echo getSimpleSetup('facility',$lst->facility_id)->name; ?></td>
											<td> <?php echo getSimpleSetup('country',$lst->country_id)->name; ?></td>
											<td> <?php echo $lst->year; ?></td>
											<td> <?php echo lang('year.'.strtoupper(getMonthName($lst->month))); ?></td>
											<td> <?php echo $lst->result; ?></td>
											<td> <?php echo $lst->is_supervised == 1 ? lang('general.YES'):lang('general.NO'); ?></td>
											<td> <?php echo $lst->result_submitted == 1 ? lang('general.YES'):lang('general.NO') ?></td>
											<td> <?php echo !empty($lst->reason_id)? getSimpleSetup('reasons',$lst->reason_id)->name : ''; ?></td>
											<td> <?php echo getSimpleSetup('testing_method',$lst->method_id)->name; ?></td>
											<td> <?php echo $lst->corrective_action == 'yes' ? lang('general.YES'):lang('general.NO'); ?></td>
											<td><a href="eqa_data.php?id=<?php echo $lst->id.'&countryid='.$lst->country_id;; ?>"> <?php echo lang('dataEntry.EDIT')?></a></td>
										</tr>
									 <?php
										 $lst->next();
									 }
								?>
							</tbody>
						
						
					        </tbody>
				        </table>
				    </div>
			    </div>
            </div>

            <div class="col-xl-4 ui-sortable">
                <div class="panel panel-inverse">
				    <div class="panel-heading">
					    <h4 class="panel-title"><?php echo lang('dataEntry.ADD_EQA_DATA')?></h4>
					    <div class="panel-heading-btn">
						    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
						    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-redo"></i></a>
						    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
						    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
					    </div>
				    </div>
				    <div class="panel-body">
					    <form class="form-horizontal" data-parsley-validate="true" name="demo-form" method="POST">
								<input type="hidden" value="pt_international" name="type" />
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
												{
														?>
														<option <?php if($item->facility_id==$flst->id) echo 'selected'; ?> value="<?php echo $flst->id; ?>"><?php echo $flst->name; ?></option>
													<?php
													$flst->next();
													
												}
											?>
							            </select>
						            </div>
                            </div>
					        <div class="form-group row m-b-15">
						        <label class="col-form-label col-md-4"><?php echo lang('general.PROVIDER')?> :</label>
						            <div class="col-md-8">
									<select class="form-control" name="providerid">
											<?php
												while(!$plst->dry())
												{
													if(($plst->type == 'international' && empty($plst->countryid)) || 
													($plst->type == 'international' && $plst->countryid == $_SESSION['countryid']))
													{
														?>
													<option <?php if($item->providers_id==$plst->id) echo 'selected'; ?> value="<?php echo $plst->id; ?>"><?php echo $plst->name; ?></option>
													<?php
													}
													$plst->next();
												}
											?>
							            </select>
						            </div>
                            </div>
							<div class="form-group row m-b-15">
						        <label class="col-form-label col-md-4"><?php echo lang('general.ROUND')?> :</label>
						            <div class="col-md-8">
									<select class="form-control" name="roundid">
											<?php
												while(!$Rlst->dry())
												{?>
													<option <?php if($item->round_id==$Rlst->id) echo 'selected'; ?> value="<?php echo $Rlst->id; ?>"><?php echo $Rlst->name; ?></option>
												<?php
												 $Rlst->next();
												}
											?>
							            </select>
						            </div>
                            </div>

                        <div class="form-group row m-b-15">
						    <label class="col-form-label col-md-4"><?php echo lang('dataEntry.MONTH')?> :</label>
						        <div class="col-md-8">
							        <select class="form-control" name="month">
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
								        <option <?php if($item->year == '2020') echo 'selected'; ?> value="2020">2020</option>
								        <option <?php if($item->year == '2021') echo 'selected'; ?>  value="2021">2021</option>
                                        </select>
						            </div>
					    </div>
					        
					        <div class="form-group row m-b-15">
						        <label class="col-md-4 col-sm-4 col-form-label" for="result"><?php echo lang('dataEntry.RESULT')?> :</label>
						            <div class="col-md-8 col-sm-8">
							            <input class="form-control" type="text" value="<?php echo $editname; ?>" id="result" name="result" />
						            </div>
                            </div>
					        <div class="form-group row m-b-15">
						        <label class="col-form-label col-md-4"><?php echo lang('dataEntry.IS_SUPER')?> :</label>
						            <div class="col-md-8">
							            <select class="form-control" name="issupervised">
											<option value="1" <?php if($item->is_supervised == 1) echo ' selected' ?>><?php echo lang('general.YES')?></option>
											<option value="0" <?php if($item->is_supervised == 0) echo ' selected' ?>><?php echo lang('general.NO')?></option>
							            </select>
						            </div>
					        </div>
					        <div class="form-group row m-b-15">
						        <label class="col-form-label col-md-4"><?php echo lang('dataEntry.TEST_METHOD')?> :</label>
						            <div class="col-md-8">
									<select class="form-control" name="methodid">
											<?php
												while(!$tlst->dry())
												{?>
													<option <?php if($item->method_id==$tlst->id) echo 'selected'; ?> value="<?php echo $tlst->id; ?>"><?php echo $tlst->name; ?></option>
												<?php
												 $tlst->next();
												}
											?>
							            </select>
						            </div>
                            </div>
					        <div class="form-group row m-b-15">
						        <label class="col-form-label col-md-4"><?php echo lang('dataEntry.IS_SUBMIT')?> :</label>
						            <div class="col-md-6">
									</select><select class="form-control" name="isresultsubmitted">
								        <option value="1" <?php if($editstatus2 == '1') echo ' selected' ?>><?php echo lang('general.YES')?></option>
								        <option value="0" <?php if($editstatus2 == '0') echo ' selected' ?>><?php echo lang('general.NO')?></option>
							            </select>
						            </div>
					        </div>
							<div class="form-group row m-b-15">
						        <label class="col-form-label col-md-4"><?php echo lang('general.REAS_REJ')?> :</label>
						            <div class="col-md-8">
									<select class="form-control" name="reasonid">
									<option value=""></option>
											<?php
												while(!$rlst->dry())
												{?>
													<option <?php if($item->reason_id && $item->reason_id == $rlst->id) echo 'selected'; ?> value="<?php echo $rlst->id; ?>"><?php echo $rlst->name; ?></option>
												<?php
												 $rlst->next();
												}
											?>
							            </select>
						            </div>
					        </div>
							<div class="form-group row m-b-15">
						        <label class="col-form-label col-md-4"><?php echo lang('general.DOC_CORRECTION')?> :</label>
						            <div class="col-md-6">
									</select><select class="form-control" name="corrective_action">
										<option value=""></option>
								        <option value="yes" <?php if($item->corrective_action == 'yes') echo ' selected' ?>><?php echo lang('general.YES')?></option>
								        <option value="no" <?php if($item->corrective_action == 'no') echo ' selected' ?>><?php echo lang('general.NO')?></option>
							            </select>
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
	App.setPageTitle('COVID19 - <?php echo lang('general.DASHBOARD')?>'); 
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
