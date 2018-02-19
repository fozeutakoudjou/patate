	<!DOCTYPE html>
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="robots" content="nofollow" />  
		
		<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
		<!-- BEGIN GLOBAL MANDATORY STYLES -->
		<link href="<?php echo _ASSETS_GLOBAL_PLUGINS_DIR_;?>font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
		<link href="<?php echo _ASSETS_GLOBAL_PLUGINS_DIR_;?>simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
		<link href="<?php echo _ASSETS_GLOBAL_PLUGINS_DIR_;?>bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
		<link href="<?php echo _ASSETS_GLOBAL_PLUGINS_DIR_;?>uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
		<!-- END GLOBAL MANDATORY STYLES -->
		<!-- BEGIN PAGE LEVEL STYLES -->
		<link href="<?php echo _ASSETS_GLOBAL_PLUGINS_DIR_;?>select2/select2.css" rel="stylesheet" type="text/css"/>
		<link href="<?php echo _ASSETS_GLOBAL_PLUGINS_DIR_;?>select2/select2-metronic.css" rel="stylesheet" type="text/css"/>
		<link href="<?php echo _ASSETS_ADMIN_PAGES_DIR_;?>css/login.css" rel="stylesheet" type="text/css"/>
		<!-- END PAGE LEVEL SCRIPTS -->
		<!-- BEGIN THEME STYLES -->
		<link href="<?php echo _ASSETS_GLOBAL_CSS_DIR_;?>components.css" rel="stylesheet" type="text/css"/>
		<link href="<?php echo _ASSETS_GLOBAL_CSS_DIR_;?>plugins.css" rel="stylesheet" type="text/css"/>
		<link href="<?php echo _ASSETS_ADMIN_LAYOUT_DIR_;?>css/layout.css" rel="stylesheet" type="text/css"/>
		<link id="style_color" href="<?php echo _ASSETS_ADMIN_LAYOUT_DIR_;?>css/themes/default.css" rel="stylesheet" type="text/css"/>
		<link href="<?php echo _ASSETS_ADMIN_LAYOUT_DIR_;?>css/custom.css" rel="stylesheet" type="text/css"/>
		<!-- END THEME STYLES -->
		<link rel="shortcut icon" href="favicon.ico"/> 
	</head>    
	   
	<body class="login" >
        <?php echo $content;?> 
        <div class="copyright">
            <?php echo __SITE__;?> est une application développée par 
             <a href="http://crystals-sevice.com/"
            title="Crystals-services ">Crystals-Services.</a>
            <span>2014 &copy;.&nbsp;Tout droit reservé</span>
        </div>
		<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->

		<script src="<?php echo _ASSETS_GLOBAL_PLUGINS_DIR_;?>jquery-1.11.0.min.js" type="text/javascript"></script>
		<script src="<?php echo _ASSETS_GLOBAL_PLUGINS_DIR_;?>jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
		<script src="<?php echo _ASSETS_GLOBAL_PLUGINS_DIR_;?>bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
		<script src="<?php echo _ASSETS_GLOBAL_PLUGINS_DIR_;?>bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
		<script src="<?php echo _ASSETS_GLOBAL_PLUGINS_DIR_;?>jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
		<script src="<?php echo _ASSETS_GLOBAL_PLUGINS_DIR_;?>jquery.blockui.min.js" type="text/javascript"></script>
		<script src="<?php echo _ASSETS_GLOBAL_PLUGINS_DIR_;?>jquery.cokie.min.js" type="text/javascript"></script>
		<script src="<?php echo _ASSETS_GLOBAL_PLUGINS_DIR_;?>uniform/jquery.uniform.min.js" type="text/javascript"></script>
		<!-- END CORE PLUGINS -->
		<!-- BEGIN PAGE LEVEL PLUGINS -->
		<script src="<?php echo _ASSETS_GLOBAL_PLUGINS_DIR_;?>jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
		<script type="text/javascript" src="<?php echo _ASSETS_GLOBAL_PLUGINS_DIR_;?>select2/select2.min.js"></script>
		<!-- END PAGE LEVEL PLUGINS -->
		<!-- BEGIN PAGE LEVEL SCRIPTS -->
		<script src="<?php echo _ASSETS_GLOBAL_SCRIPTS_DIR_;?>metronic.js" type="text/javascript"></script>
		<script src="<?php echo _ASSETS_ADMIN_LAYOUT_DIR_;?>scripts/layout.js" type="text/javascript"></script>
		<script src="<?php echo _ASSETS_ADMIN_PAGES_DIR_;?>scripts/login.js" type="text/javascript"></script>
		<script src="<?php echo _THEME_BO_JS_DIR_;?>index.js" type="text/javascript"></script>
		<!-- END PAGE LEVEL SCRIPTS -->
		<script>
			jQuery(document).ready(function() {     
				Metronic.init(); // init metronic core components
				Layout.init(); // init current layout
				Login.init();
			});
		</script>
		<!-- END JAVASCRIPTS -->
	</body>
</html>