<?php
@session_start();
include_once('lang.php');
?>
<ul class="navbar-nav navbar-right">
                
                <li class="dropdown navbar-user">
					<a href="intnatpt.php" >
					<i class="fas fa-chart-area"></i>
					<span class="d-none d-md-inline"><?php echo lang('general.INT_PT_DASH')?></span>
					</a>
				</li>
                <li class="dropdown navbar-user">
					<a href="natpt.php" >
					<i class="fas fa-chart-area"></i>
					<span class="d-none d-md-inline"><?php echo lang('general.NAT_PT_DASH')?></span>
					</a>
				</li>
				<li class="dropdown navbar-user">
					<a href="retesting.php" >
					<i class="fas fa-chart-area"></i>
					<span class="d-none d-md-inline"><?php echo lang('general.TEST_DASH')?></span>
					</a>
				</li>
				<?php 
				if(!empty($_SESSION['user_type']) && $_SESSION['user_type'] =='Admin')
				{
				?>
				<li class="dropdown navbar-user">
					<a href="data_entry.php" >
					<i class="fas fa-user-lock"></i>
					<span class="d-none d-md-inline"><?php echo lang('general.ADMIN')?></span>
					</a>
				</li>
				<?php }?>

				<li class="dropdown navbar-user">
					<a href="logout.php" >
					<i class="fas fa-user"></i>
					<span class="d-none d-md-inline"><?php echo lang('general.LOGOUT')?></span>
					</a>
				</li>
			</ul>