<?php
@session_start();
if(empty($_SESSION['user_email']))
{
	header('location:Login.php');
}
include_once('maindish/savinginputs.php');

$editname = null;
$editstatus= null;
$iso = null;
$id= empty($_REQUEST['id']) ? null : $_REQUEST['id'];

 if(isset($_POST['submit']))
 {
	saveSimpleSetup('country',$id);
 }

 if(isset($_REQUEST['id']))
 {
	$item = getSimpleSetup('country',$_REQUEST['id']);
	$editname = $item->name;
	$editstatus =$item->is_active;
	$iso = $item->iso;


 }


 $lst = getSimpleSetup('country');

 include_once('header.php');
?>
		<!-- begin #content -->
		<div id="content" class="content">
			<!-- begin breadcrumb -->
			
			<!-- end breadcrumb -->
			<!-- begin page-header -->
			<h1 class="page-header"><small>Country</small></h1>
			<!-- end page-header -->
			
		<!-- begin panel -->
        <div class="row">
            <div class="col-xl-6 ui-sortable">
                <div class="panel panel-inverse">
				    <div class="panel-heading">
					    <h4 class="panel-title">List of Countries</h4>
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
							    <th class="text-nowrap">Name</th>
								<th class="text-nowrap">ISO Code</th>
								<th class="text-nowrap">IsActive</th>
								<th class="text-nowrap"></th>
						        </tr>
					        </thead>
					        <tbody>
								<?php
									 while(!$lst->dry())
									 {?>
									  	<tr>
											<td> <?php echo $lst->name; ?></td>
											<td> <?php echo $lst->iso; ?></td>
											<td> <?php echo $lst->is_active == 1? 'Yes':'No'; ?></td>
											<td><a href="country.php?id=<?php echo $lst->id; ?>"> Edit</a></td>
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

            <div class="col-xl-6 ui-sortable">
                <div class="panel panel-inverse">
				    <div class="panel-heading">
					    <h4 class="panel-title">Add Country</h4>
					    <div class="panel-heading-btn">
						    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i class="fa fa-expand"></i></a>
						    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-success" data-click="panel-reload"><i class="fa fa-redo"></i></a>
						    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
						    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove"><i class="fa fa-times"></i></a>
					    </div>
				    </div>
				    <div class="panel-body">
					<form class="form-horizontal" data-parsley-validate="true" name="demo-form" method="POST" action="">
					    <div class="form-group row m-b-15">
						    <label class="col-md-4 col-sm-4 col-form-label" >Name :</label>
						        <div class="col-md-8 col-sm-8">
							        <input class="form-control" type="text" required  value="<?php echo $editname; ?>"id="name" name="name" placeholder="Required" data-parsley-required="true" />
						        </div>
                        </div>
						<div class="form-group row m-b-15">
						    <label class="col-md-4 col-sm-4 col-form-label" >ISO Code :</label>
						        <div class="col-md-8 col-sm-8">
							        <input class="form-control" type="text" required  value="<?php echo $iso; ?>"id="iso" name="iso" placeholder="ISO COde" data-parsley-required="true" />
						        </div>
                        </div>

                        <div class="form-group row m-b-15">
						        <label class="col-form-label col-md-4">IsActive :</label>
						            <div class="col-md-4">
									<select class="form-control" name="isactive">
								        	<option value="1" <?php if($editstatus== '1') echo ' selected' ?>>Yes</option>
								        	<option value="0" <?php if($editstatus == '0') echo ' selected' ?>>No</option>
							            </select>
						            </div>
					    </div>

                        <div class="form-group row m-b-0">
						    <label class="col-md-4 col-sm-4 col-form-label">&nbsp;</label>
						        <div class="col-md-8 col-sm-8">
							        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
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
	App.setPageTitle('COVID19 - Dashboard');
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
