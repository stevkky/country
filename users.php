<?php
@session_start();
if(empty($_SESSION['user_email']))
{
	header('location:Login.php');
}
include_once('maindish/savinginputs.php');
$editname = null;
$editstatus= null;
$editemail= null;
$editpassword= null;
$editphone= null;
$counttryid =null;
$tab1active = "active";
$tab2active = "";
$clst = getSimpleSetup('country');
$id= empty($_REQUEST['id']) ? null : $_REQUEST['id'];

	if(isset($_POST['submit'])) 
	{
		saveSimpleSetup('user_login',$id);
		unset($_REQUEST['id']);
	}
		 
	if(isset($_REQUEST['id']))
	{
		$item = getSimpleSetup('user_login',$_REQUEST['id']);
		$editname = $item->name;
		$editstatus =$item->user_type;
		$editemail = $item->user_email;
		$editpassword = $item->user_password;
		$editphone = $item->user_phone;
		$counttryid = $item->countryid;

		$tab2active  = "active";
		$tab1active = "";

	}

$lst  = array();

if(!empty($_SESSION['countryid']))
{
	$lst = getData('user_login',array('countryid=?',$_SESSION['countryid']));
}
else
{
	$lst = getSimpleSetup('user_login');
}



include_once('header.php');
include_once('lang.php');
?>

<div id="content" class="content">
<h1 class="page-header"><small><?php echo lang('dataEntry.USR_NAME')?></small></h1> 
<!-- begin tabs -->
<ul class="nav nav-tabs nav-tabs-inverse nav-justified nav-justified-mobile" data-sortable-id="index-2">
			<li class="nav-item"><a href="#user-account-list" data-toggle="tab" class="nav-link <?php echo $tab1active ?>"><i class="fas fa-user fa-lg m-r-5"></i> <span class="d-none d-md-inline">User Account List</span></a></li>
			<li class="nav-item"><a href="#add-user-account" data-toggle="tab" class="nav-link <?php echo $tab2active ?>"><i class="fas fa-user-cog fa-lg m-r-5"></i> <span class="d-none d-md-inline">Add User Account</span></a></li>
		</ul>
		<div class="tab-content" data-sortable-id="index-3">
			<div class="tab-pane fade <?php if(!empty($tab1active)) echo 'active show'  ?>" id="user-account-list">
            <table id="data-table-default" class="table table-striped table-bordered table-td-valign-middle" style="width:100%">
				<thead>
					<tr>
						<th class="text-nowrap"><?php echo lang('general.NAME')?></th>
						<th class="text-nowrap"><?php echo lang('dataEntry.USR_EMAIL')?></th>
						<th class="text-nowrap"><?php echo lang('dataEntry.USR_PHN')?></th>
						<th class="text-nowrap"><?php echo lang('general.COUNTRY') ?></th>
						<th class="text-nowrap"><?php echo lang('dataEntry.USR_TYPE') ?></th>
						<th class="text-nowrap"></th> 
					</tr>
				</thead>
				<tbody>
					<?php
							while(!$lst->dry())
								{?>
									<tr>
										<td> <?php echo $lst->name; ?></td>
										<td> <?php echo $lst->user_email; ?></td>
										<td> <?php echo $lst->user_phone; ?></td>
										<td> <?php echo  $lst->countryid ? getSimpleSetup('country',$lst->countryid)->name: ''; ?></td>
										<td> <?php echo $lst->user_type ; ?></td>
										<td><a href="users.php?id=<?php echo $lst->id; ?>"> <?php echo lang('dataEntry.EDIT')?></a></td>
									</tr>
									<?php
										 $lst->next();
								}
					?>
				</tbody>
			</table>
			</div>
			<div class="tab-pane fade <?php if(!empty($tab2active)) echo 'active show' ?>" id="add-user-account">
            <form action="" method="POST" class="form-control-with-bg">
                <fieldset>
					<!-- begin row -->
					<div class="row">
						<!-- begin col-8 -->
						<div class="col-xl-8 offset-xl-2">
							<legend class="no-border f-w-700 p-b-0 m-t-0 m-b-20 f-s-16 text-inverse"><?php echo lang('dataEntry.USR_DET')?></legend>
							<!-- begin form-group row -->
							<div class="form-group row m-b-10">
						        	<label class="col-lg-3 text-lg-right col-form-label"><?php echo lang('general.COUNTRY')?> :</label>
						            <div class="col-lg-9 col-xl-6">
									<select class="form-control" name="countryid">
									<?php
										if(empty($_SESSION['countryid']))
										{
											echo '<option>ALL</option>';
										}
								
											while(!$clst->dry())
											{
												if(!empty($_SESSION['countryid']))
												{
													if($_SESSION['countryid'] == $clst->id)
													{
														?>
														<option <?php if($counttryid == $clst->id) echo 'selected'; ?> value="<?php echo $clst->id; ?>"><?php echo $clst->name; ?></option>
														<?php
													}
												}
												else
												{
													?>
														<option <?php if($counttryid == $clst->id) echo 'selected'; ?> value="<?php echo $clst->id; ?>"><?php echo $clst->name; ?></option>
													<?php
												}
											$clst->next();
											}
											?>
							            </select>
						            </div>
                           	 	</div>
							<div class="form-group row m-b-10">
								<label class="col-lg-3 text-lg-right col-form-label"><?php echo lang('general.NAME')?></label>
								<div class="col-lg-9 col-xl-6">
									<input type="text" required value="<?php echo $editname; ?>" name="name" placeholder="John Tep" class="form-control" />
								</div>
							</div>
							<!-- end form-group row -->
							<!-- begin form-group row -->
							<div class="form-group row m-b-10">
								<label class="col-lg-3 text-lg-right col-form-label"><?php echo lang('dataEntry.USR_EMAIL')?></label>
								<div class="col-lg-9 col-xl-6">
									<input type="text" required value="<?php echo $editemail; ?>" name="email" placeholder="ab@c.com" class="form-control" />
								</div>
							</div>
							<!-- end form-group row -->
							<!-- begin form-group row -->
							<div class="form-group row m-b-10">
								<label class="col-lg-3 text-lg-right col-form-label"><?php echo lang('general.PASSWORD') ?></label>
								<div class="col-lg-9 col-xl-6">
									<input type="password" required value="<?php echo $editpassword; ?>" name="password" placeholder="" class="form-control" />
								</div>
							</div>
							<!-- end form-group row -->
							<!-- begin form-group row -->
							<div class="form-group row m-b-10">
								<label class="col-lg-3 text-lg-right col-form-label"><?php echo lang('dataEntry.USR_PHNE')?></label>
								<div class="col-lg-9 col-xl-6">
									<input type="text" value="<?php echo $editphone; ?>" name="phone" placeholder="" class="form-control" />
								</div>
							</div>
							<!-- end form-group row -->
							<!-- begin form-group row -->
							<div class="form-group row m-b-10">
								<label class="col-lg-3 text-lg-right col-form-label"><?php echo lang('dataEntry.USR_TYPE') ?></label>
								<div class="col-lg-9 col-xl-6">
									<select class="form-control" name="usertype">
								        	<option value="Admin" <?php if($editstatus == 'Admin') echo ' selected' ?>><?php echo lang('general.ADMIN') ?></option>
								        	<option value="User" <?php if($editstatus == 'User') echo ' selected' ?>><?php echo lang('dataEntry.USER') ?></option>
							        </select>
								</div>
							</div>
							<!-- end form-group row -->
						</div>
						<!-- end col-8 -->
					</div>
					<!-- end row -->
                </fieldset>
                    <div class="btn-group mr-2 sw-btn-group" role="group">
                        <!--<button type="reset" class="btn btn-secondary sw-btn-next" >Reset</button>--> 
						<button type="submit" class="btn btn-secondary sw-btn-next" name="submit" value="submit"><?php echo lang('dataEntry.SUBMIT')?></button>
						<button type="reset" class="btn btn-info" onclick="window.location='<?php echo $_SERVER['PHP_SELF'] ?>'"><?php echo lang('general.NEW')?></button>
				    </div>  
            </form>
			</div> 	
		</div>
        <!-- end tabs -->
</div>

<!-- ================== BEGIN BASE JS ================== -->
<script src="assets/js/app.min.js"></script>
    <script src="assets/js/theme/default.min.js"></script>
    <script>
	App.setPageTitle('COVID19 - <?php echo lang('general.DASHBOARD')?>');
	App.restartGlobalFunction(); 
     
	$.getScript('assets/plugins/datatables.net/js/jquery.dataTables.min.js').done(function() {
		$.when(
			$.getScript('assets/plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js'),
            $.getScript('assets/plugins/datatables.net-responsive/js/dataTables.responsive.min.js'),
            $.getScript('assets/plugins/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js'),
			$.getScript('assets/plugins/datatables.net-fixedcolumns/js/dataTables.fixedcolumns.min.js'),
			$.getScript('assets/plugins/datatables.net-fixedcolumns-bs4/js/fixedcolumns.bootstrap4.min.js'),
			$.Deferred(function( deferred ){
				$(deferred.resolve);
			})
		).done(function() {
			$.getScript('assets/js/demo/table-manage-fixed-columns.demo.js'),
            $.getScript('assets/js/demo/table-manage-default.demo.js'),
			$.Deferred(function( deferred ){
				$(deferred.resolve);
			})
		});
	});
</script>
	<!-- ================== END BASE JS ================== -->
</body>
</html>