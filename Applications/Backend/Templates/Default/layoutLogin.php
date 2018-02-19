<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
		<meta content="width=device-width, initial-scale=1" name="viewport"/>
		<meta name="description" content=""/>
        <meta name="keywords" content=""/>
        <meta name="author" content="<?php echo __AUTHOR__?>"/>
		<meta name="robots" content="nofollow" />
        
		<link rel="shortcut icon" href="<?php echo _IMG_DIR_?>favicon.ico">
		
		<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
		<!-- BEGIN GLOBAL MANDATORY STYLES -->
		<link href="<?php echo _PLUGINS_DIR_;?>font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
		<link href="<?php echo _PLUGINS_DIR_;?>simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
		<link href="<?php echo _PLUGINS_DIR_;?>bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
		<link href="<?php echo _PLUGINS_DIR_;?>uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo _PLUGINS_DIR_;?>bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
		<!-- END GLOBAL MANDATORY STYLES -->
		<!-- BEGIN PAGE LEVEL STYLES -->
		<link href="<?php echo _THEME_BO_CSS_DIR_;?>login.css" rel="stylesheet" type="text/css"/>
		<!-- END PAGE LEVEL SCRIPTS -->
		<!-- BEGIN THEME STYLES -->
		<link href="<?php echo _CSS_DIR_;?>components.css" rel="stylesheet" type="text/css"/>
		<link href="<?php echo _CSS_DIR_;?>plugins.css" rel="stylesheet" type="text/css"/>
		<link href="<?php echo _THEME_BO_CSS_DIR_;?>layout.css" rel="stylesheet" type="text/css"/>
		<link id="style_color" href="<?php echo _THEME_BO_CSS_DIR_;?>themes/default.css" rel="stylesheet" type="text/css"/>
		<link href="<?php echo _THEME_BO_CSS_DIR_;?>custom.css" rel="stylesheet" type="text/css"/>
		<!-- END THEME STYLES -->
		<title><?php echo __TITLE__;?></title>
	</head>    
	   
	<body class="login" >
        <?php echo $content;?> 
        <div class="copyright">
            <?php echo __NAME__;?> <?php echo $this->l('is application developped by'); ?>
            <a href="<?php echo __AUTHOR_URL__;?>" title="<?php echo __AUTHOR__;?>" target="_blank"><?php echo __AUTHOR__;?></a>
            <span><?php echo date('Y');?> &copy;.&nbsp;<?php echo $this->l('All right reserved'); ?></span>
        </div>
		<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
        <!-- BEGIN CORE PLUGINS -->
        <!--[if lt IE 9]>
        <script src="../../assets/global/plugins/respond.min.js"></script>
        <script src="../../assets/global/plugins/excanvas.min.js"></script> 
        <![endif]-->
        <script src="<?php echo _PLUGINS_DIR_;?>jquery.min.js" type="text/javascript"></script>
        <script src="<?php echo _PLUGINS_DIR_;?>jquery-migrate.min.js" type="text/javascript"></script>
        <script src="<?php echo _PLUGINS_DIR_;?>bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="<?php echo _PLUGINS_DIR_;?>jquery.blockui.min.js" type="text/javascript"></script>
        <script src="<?php echo _PLUGINS_DIR_;?>jquery.cokie.min.js" type="text/javascript"></script>
        <script src="<?php echo _PLUGINS_DIR_;?>uniform/jquery.uniform.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="<?php echo _PLUGINS_DIR_;?>jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <script src="<?php echo _SCRIPTS_DIR_;?>metronic.js" type="text/javascript"></script>
        <script src="<?php echo _THEME_BO_JS_DIR_;?>layout.js" type="text/javascript"></script>
        <script src="<?php echo _THEME_BO_JS_DIR_;?>demo.js" type="text/javascript"></script>
        <script src="<?php echo _THEME_BO_JS_DIR_;?>login.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL SCRIPTS -->
		<script>
			jQuery(document).ready(function() {
				Metronic.init(); // init metronic core components
				Layout.init(); // init current layout
				Login.init();
                Demo.init();
			});
		</script>
		<!-- END JAVASCRIPTS -->
	</body>
</html>