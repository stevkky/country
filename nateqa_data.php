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


 $lst = getData('eqa_data_entry',array('type=?','nat_retest'));
 $flst = getSimpleSetup('facility');
 $plst = getSimpleSetup('providers');
 $tlst = getSimpleSetup('testing_method');

 $rlst = getSimpleSetup('reasons');
 $Rlst = getSimpleSetup('rounds');
 $clst = getSimpleSetup('country');

 include_once('header.php');
 include_once('lang.php');
?>
		<!-- begin #content -->
		<div id="content" class="content">
			<!-- begin breadcrumb -->
			
			<!-- end breadcrumb -->
			<!-- begin page-header -->
			<h1 class="page-header"><small><?php echo lang('dataEntry.NAT_RETEST_EQA_DATA')?></small></h1>
			<!-- end page-header -->
			
		<!-- begin panel -->
        <div class="row">
        <div class="col-xl-8 ui-sortable">
                <div class="panel panel-inverse">
				    <div class="panel-heading">
					    <h4 class="panel-title"><?php echo lang('dataEntry.NAT_RETEST_LIST')?></h4>
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
											<td> <?php echo getMonthName($lst->month); ?></td>
											<td> <?php echo $lst->result; ?></td>
											<td> <?php echo $lst->is_supervised == 1 ? 'Yes':'No'; ?></td>
											<td> <?php echo $lst->result_submitted == 1 ? 'Yes':'No' ?></td>
											<td> <?php echo !empty($lst->reason_id)? getSimpleSetup('reasons',$lst->reason_id)->name : ''; ?></td>
											<td> <?php echo getSimpleSetup('testing_method',$lst->method_id)->name; ?></td>
											<td><a href="seqa_data.php?id=<?php echo $lst->id; ?>"> <?php echo lang('dataEntry.EDIT')?></a></td>
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
								<input type="hidden" value="pt_national" name="type" />
								<div class="form-group row m-b-15">
						        <label class="col-form-label col-md-4"><?php echo lang('general.COUNTRY')?> :</label>
						            <div class="col-md-8">
									<select class="form-control" name="countryid">
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
                            </div>
						<div class="form-group row m-b-15">
						        <label class="col-form-label col-md-4"><?php echo lang('dataEntry.FACILITY')?> :</label>
						            <div class="col-md-8">
									<select class="form-control" name="facilityid">
											<?php
												while(!$flst->dry())
												{
														?>
														<option value="<?php echo $flst->id; ?>"><?php echo $flst->name; ?></option>
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
													if($plst->type == 'national')
													{
														?>
													<option value="<?php echo $plst->id; ?>"><?php echo $plst->name; ?></option>
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
													<option value="<?php echo $Rlst->id; ?>"><?php echo $Rlst->name; ?></option>
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
								        <option value="January">January</option>
								        <option value="February">February</option>
								        <option value="March">March</option>
								        <option value="April">April</option>
								        <option value="May">May</option>
								        <option value="June">June</option>
								        <option value="July">July</option>
								        <option value="August">August</option>
								        <option value="September">September</option>
								        <option value="October">October</option>
								        <option value="November">November</option>
								        <option value="December">December</option>
							        </select>
						        </div>
                        </div>
                        <div class="form-group row m-b-15">
						        <label class="col-form-label col-md-4"><?php echo lang('general.YEAR')?> :</label>
						            <div class="col-md-8">
                                        <select class="form-control" name="yearid">
								        <option value="2020">2020</option>
								        <option value="2021">2021</option>
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
							            </select><select class="form-control" name="issupervised">
								        <option value="1" <?php if($editstatus== '1') echo ' selected' ?>><?php echo lang('general.YES')?></option>
								        <option value="0" <?php if($editstatus == '0') echo ' selected' ?>><?php echo lang('general.NO')?></option>
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
													<option value="<?php echo $tlst->id; ?>"><?php echo $tlst->name; ?></option>
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
								        <option value="1" <?php if($editstatus2== '1') echo ' selected' ?>><?php echo lang('general.YES')?></option>
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
													<option value="<?php echo $rlst->id; ?>"><?php echo $rlst->name; ?></option>
												<?php
												 $rlst->next();
												}
											?>
							            </select>
						            </div>
					        </div>
					        <div class="form-group row m-b-0">
						        <label class="col-md-4 col-sm-4 col-form-label">&nbsp;</label>
								<div class="col-md-8 col-sm-8">
							        <button type="submit" name="submit" value="submit" class="btn btn-primary"><?php echo lang('dataEntry.SUBMIT')?></button>
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
