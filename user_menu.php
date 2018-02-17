<ul class='nav navbar-nav navbar-right' style="margin-right:0px;"> 
	<li class='dropdown'><a class='dropdown-toggle' data-toggle='dropdown' href='#'><span class="hidden-sm hidden-xs"><?php echo employee_name($_COOKIE['user_id']); ?></span><span class="visible-sm visible-xs">....</span></a>
		<ul class='dropdown-menu'>
            <li><a href='index.php?content=Employee&reset_password=y&id_employee=<?php echo $_COOKIE['user_id']; ?>'><i class='fa fa-key'></i> Reset Password</a></li>
            <li><a href='index.php?logout'><i class='fa fa-sign-out fa-fw'></i> Logout</a></li>
		</ul>
	</li>
</ul>
