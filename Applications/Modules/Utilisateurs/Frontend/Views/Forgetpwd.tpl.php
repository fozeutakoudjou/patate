    <div class="logo">
		<img src="<?php echo _ASSETS_ADMIN_LAYOUT_DIR_;?>img/logo-big.png" alt=""/>
	</div>
<!-- BEGIN FORGOT PASSWORD FORM -->
    <form class="forget-form" action="index.html" method="post">
        <h3>Forget Password ?</h3>
        <p>
             Enter your e-mail address below to reset your password.
        </p>
        <div class="form-group">
            <div class="input-icon">
                <i class="fa fa-envelope"></i>
                <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Email" name="email"/>
            </div>
        </div>
        <div class="form-actions  clearfix">
            <a id="back-btn" class="btn btn-back-login" href="/connexion.html">
            <i class="m-icon-swapleft"></i> Back </a>
            <button type="submit" class="btn green pull-right">
            Submit <i class="m-icon-swapright m-icon-white"></i>
            </button>
        </div>
    </form>    
 <!-- END FORGOT PASSWORD FORM -->