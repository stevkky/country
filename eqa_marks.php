<?php
@session_start();
if(empty($_SESSION['user_email']))
{
	header('location:Login.php');
}
include_once('maindish/savinginputs.php');
 
$editname = null;
$editstatus= null;
$roundid = null;
$countryid =null;
$id= empty($_REQUEST['id']) ? null : $_REQUEST['id'];

 
 if(isset($_POST['submit']))
 {
	 //var_dump($_POST);exit;
	saveSimpleSetup('eqa_passmark',$id);
 }

 if(isset($_REQUEST['id']))
 {
	$item = getSimpleSetup('eqa_passmark',$_REQUEST['id']);
	$editname = $item->mark;
	$roundid = $item->round_id;
	$countryid = $item->providers_id;
	//$editstatus =$item->;
 }


 $lst = getSimpleSetup('eqa_passmark');
 $plst = getSimpleSetup('providers');
 $rlst = getSimpleSetup('rounds');

 $provids = array();
 if(!empty($_SESSION['countryid']))
 {
	$plst = getData('providers',array('countryid=?',$_SESSION['countryid']));
	while(!$plst->dry())
	{
		$provids[] = $plst->id;
		$plst->next();
	}

	$plst->first();
 }

 include_once('header.php');
 include_once('lang.php');
?>
		
		<!-- begin #content -->
		<div id="content" class="content">
			<!-- begin breadcrumb -->
			
			<!-- end breadcrumb -->
			<!-- begin page-header -->
			<h1 class="page-header"><small><?php echo lang('dataEntry.EQA_PASSMARK_LIST')?></small></h1>
			<!-- end page-header -->
			
		<!-- begin panel -->
        <div class="row">
            <div class="col-xl-6 ui-sortable">
                <div class="panel panel-inverse">
				    <div class="panel-heading">
					    <h4 class="panel-title"><?php echo lang('dataEntry.EQA_PASSMARK')?></h4>
					    <div class="panel-heading-btn">
						    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
						    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-redo"></i></a>
						    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
						    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
					    </div>
				    </div>
				    <div class="panel-body">
                        <table id="data-table-fixed-columns" class="table table-striped table-bordered table-td-valign-middle" style="width:100%;">
					        <thead>
						        <tr>
							    <th class="text-nowrap"><?php echo lang('general.PROVIDER')?></th>
							    <th class="text-nowrap"><?php echo lang('dataEntry.MARK')?>(%)</th>
							    <th class="text-nowrap"><?php echo lang('general.ROUND')?></th>
							    <th class="text-nowrap"></th>
						        </tr>
					        </thead>
					        <tbody>
							<?php
							
									 while(!$lst->dry())
									 {
										 if( in_array($lst->providers_id,$provids) || empty($provids))
										 {
										?>
											<tr>
											  <td> <?php echo getSimpleSetup('providers',$lst->providers_id)->name; ?></td>
											  <td> <?php echo $lst->mark; ?></td>
											  <td> <?php echo getSimpleSetup('rounds',$lst->round_id)->name; ?></td>
											  <td><a href="eqa_marks.php?id=<?php echo $lst->id; ?>"> <?php echo lang('dataEntry.EDIT')?></a></td>
										  </tr>
									   <?php
										 }
										
										 $lst->next();
									 }
								?>
						
					        </tbody>
				        </table>
				    </div>
			    </div>
            </div>

            <div class="col-xl-6 ui-sortable">
                <div class="panel panel-inverse">
				    <div class="panel-heading">
					    <h4 class="panel-title"><?php echo lang('dataEntry.ADD_EQA_PASS')?></h4>
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
							<label class="col-form-label col-md-4"><?php echo lang('general.PROVIDER')?> :</label>
						            <div class="col-md-8">
									<select class="form-control" name="providerid">
											<?php
												while(!$plst->dry())
												{?>
													<option <?php if($countryid && $countryid == $plst->id) echo 'selected'; ?> value="<?php echo $plst->id; ?>"><?php echo $plst->name; ?></option>
												<?php
												 $plst->next();  
												}
											?>
							            </select>
						            </div>
					    </div>
					   <div class="form-group row m-b-15">
						    <label class="col-md-4 col-sm-4 col-form-label" for="passmark"><?php echo lang('dataEntry.PASSMARK')?> :</label>
						        <div class="col-md-8 col-sm-8">
							        <input class="form-control" type="number"  required value="<?php echo $editname; ?>" id="passmark" name="passmark" data-parsley-required="true" />
						        </div>
                        </div>
					    <div class="form-group row m-b-15">
						        <label class="col-form-label col-md-4"><?php echo lang('general.ROUND')?> :</label>
						            <div class="col-md-4">
									<select class="form-control" name="roundid">
											<?php
												while(!$rlst->dry())
												{?>
													<option <?php if($roundid && $roundid == $rlst->id) echo 'selected'; ?> value="<?php echo $rlst->id; ?>"><?php echo $rlst->name; ?></option>
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
			$.Deferred(function( deferred ){
				$(deferred.resolve);
			})
		});
	});
</script>
	<!-- ================== END BASE JS ================== -->
</body>
</html>
