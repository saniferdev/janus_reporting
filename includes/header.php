<div class="brand clearfix">
<h4 class="pull-left text-white" style="margin:20px 0px 0px 20px"><i class="fa fa-user"></i>&nbsp; <?php echo htmlentities($_SESSION['alogin']);?> </h4>
		<span class="menu-btn"><i class="fa fa-bars"></i></span>
		<ul class="ts-profile-nav">
			
			<li class="ts-account">
				<a href="#"><img src="images/avatar.jpg" class="ts-avatar hidden-side" alt="" style="font-size: 12px"> Dépot : <?php echo $_SESSION['depot'];?> <i class="fa fa-angle-down hidden-side"></i></a>
				<ul>
					<li><a href="change-password.php">Change Password</a></li>
					<li><a href="logout.php">Logout</a></li>
				</ul>
			</li>
		</ul>
	</div>
