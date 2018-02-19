	<!-- BEGIN LOGO -->

	<div class="logo">
		<img src="<?php echo _THEME_BO_IMG_DIR_;?>logo-big.png" alt=""/>
	</div>
	<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
	<div class="menu-toggler sidebar-toggler">
	</div>
	<!-- END SIDEBAR TOGGLER BUTTON -->
	<div class="content">
		<form class="login-form" action="log-in.html" method="post">
		
			<h3 class="form-title">Login to your Account </h3>
			<div class="alert alert-danger display-hide">
				<button class="close" data-close="alert"></button>
				<span>
						Wrong User and Password </span>
			</div>
			
			<div class="form-group">
				<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
				<label class="control-label visible-ie8 visible-ie9">User</label>
				<div class="input-icon">
					<i class="fa fa-user"></i>
					<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Username" name="username"/>
				</div>
			</div>
			
			<div class="form-group">
				<label class="control-label visible-ie8 visible-ie9">Password</label>
				<div class="input-icon">
					<i class="fa fa-lock"></i>
					<input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="password"/>
				</div>
			</div>
			
			<div class="form-actions">
				<label class="checkbox">
					<input type="checkbox" name="remember" value="1"/> Remember me </label>
				<button type="submit" class="btn green pull-right">
					Connexion <i class="m-icon-swapright m-icon-white"></i>
				</button>
			</div>
			 <div id="clear"></div>
			 
			
			
		
		</form>
		
	</div>	