<!DOCTYPE html>
<!-- 
Template Name: Crystals framework 
Version: 2.0.1
Author: Crystals-services
Website: http://www.crystals-services.com/
License: GPL.
-->
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="fr" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="description" content=""/>
    <meta name="keywords" content=""/>
    <meta name="author" content="<?php echo __AUTHOR__?>"/>
    <meta http-equiv="Content-Language" content="fr"/>
    <meta name="robots" content="index,follow"/>
    <!-- Partage sur les reseaux sociaux -->
    <meta property="og:site_name" content="-CUSTOMER VALUE-">
    <meta property="og:title" content="-CUSTOMER VALUE-">
    <meta property="og:description" content="-CUSTOMER VALUE-">
    <meta property="og:type" content="website">
    <meta property="og:image" content="-CUSTOMER VALUE-"><!-- link to image for socio -->
    <meta property="og:url" content="-CUSTOMER VALUE-">

    <link rel="shortcut icon" href="<?php echo _IMG_DIR_?>favicon.ico">

    <meta name="theme-color" content="#ffffff">
    <!-- BEGIN GLOBAL MANDATORY STYLES --> 
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|PT+Sans+Narrow|Source+Sans+Pro:200,300,400,600,700,900&amp;subset=all" rel="stylesheet" type="text/css">
    <link href="<?php echo _PLUGINS_DIR_;?>font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo _PLUGINS_DIR_;?>bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL PLUGIN STYLES -->
    <link href="<?php echo _PLUGINS_DIR_;?>fancybox/source/jquery.fancybox.css" rel="stylesheet">
	<?php if(isset($pluginsCSS)) :?>
        <?php foreach ($pluginsCSS as $key => $media):?>
        <link href="<?php echo $key;?>" rel="stylesheet" type="text/css" media="<?php echo $media;?>"/>
        <?php endforeach;?> 
    <?php endif; ?>
    <!-- END PAGE LEVEL PLUGIN STYLES -->
    <!-- BEGIN THEME STYLES -->
    <link href="<?php echo _CSS_DIR_;?>components.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo _THEME_FO_CSS_DIR_;?>style.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo _THEME_FO_CSS_DIR_;?>style-responsive.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo _THEME_FO_CSS_DIR_;?>themes/red.css" rel="stylesheet" type="text/css" id="style-color"/>
    <link href="<?php echo _THEME_FO_CSS_DIR_;?>custom.css" rel="stylesheet" type="text/css"/>
    <!-- BEGIN PAGE STYLES -->
	<?php if(isset($tabCSS)) :?>
		<?php foreach ($tabCSS as $key => $media):?>
		<link href="<?php echo $key;?>" rel="stylesheet" type="text/css" media="<?php echo $media;?>"/>
		<?php endforeach;?> 
	<?php endif; ?>
    <!-- END PAGE STYLES -->
    <title><?php echo __TITLE__;?></title>
</head>
<!-- Head END -->
<!-- Body BEGIN -->
<body class="corporate">
    <div id="global">
        <!-- BEGIN STYLE CUSTOMIZER -->
        <div class="color-panel hidden-sm">
            <div class="color-mode-icons icon-color"></div>
            <div class="color-mode-icons icon-color-close"></div>
            <div class="color-mode">
                <p>THEME COLOR</p>
                <ul class="inline">
                    <li class="color-red current color-default" data-style="red"></li>
                    <li class="color-blue" data-style="blue"></li>
                    <li class="color-green" data-style="green"></li>
                    <li class="color-orange" data-style="orange"></li>
                    <li class="color-gray" data-style="gray"></li>
                    <li class="color-turquoise" data-style="turquoise"></li>
                </ul>
            </div>
        </div>
        <!-- END BEGIN STYLE CUSTOMIZER --> 
        <!-- BEGIN TOP BAR -->
        <div class="pre-header">
            <div class="container">
                <div class="row">
                    <!-- BEGIN TOP BAR LEFT PART -->
                    <div class="col-md-6 col-sm-6 additional-shop-info">
                        <ul class="list-unstyled list-inline">
                            <li><i class="fa fa-phone"></i><span>+1 456 6717</span></li>
                            <li><i class="fa fa-envelope-o"></i><span>info@keenthemes.com</span></li>
                        </ul>
                    </div>
                    <!-- END TOP BAR LEFT PART -->
                    <!-- BEGIN TOP BAR MENU -->
                    <div class="col-md-6 col-sm-6 additional-nav">
                        <ul class="list-unstyled list-inline pull-right">
                            <li><a href="page-login.html">Log In</a></li>
                            <li><a href="page-reg-page.html">Registration</a></li>
                        </ul>
                    </div>
                    <!-- END TOP BAR MENU -->
                </div>
            </div>        
        </div>
        <!-- END TOP BAR -->
        <!-- BEGIN HEADER -->
        <div class="header">
            <div class="container">
                <a class="site-logo" href="index.html"><img src="<?php echo _IMG_DIR_?>logo-corp-red.png" alt="Default FrontEnd"></a>

                <a href="javascript:void(0);" class="mobi-toggler"><i class="fa fa-bars"></i></a>
                <!-- BEGIN NAVIGATION -->
                <div class="header-navigation pull-right font-transform-inherit">
                    <ul>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" data-target="#" href="#">Home</a>
                            <ul class="dropdown-menu">
                                <li><a href="#">Home Default</a></li>
                                <li><a href="#">Home Default 2</a></li>
                            </ul>
                        </li>
                        <li><a href="#" target="_blank">About us</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="main">
            <div class="container">
                <ul class="breadcrumb">
                    <li><a href="index.html" class="active">Home</a></li>
                </ul>
                <!-- BEGIN SIDEBAR & CONTENT -->
                <div class="row margin-bottom-40">
                    <!-- BEGIN CONTENT -->
                    <div class="col-md-12 col-sm-12">
                        <h1>Blog Page</h1>
                        <div class="content-page">
                            <div class="row">
                                <!-- BEGIN LEFT SIDEBAR -->            
                                <div class="col-md-12 col-sm-12">
                                    <div class="row" id="contenu-page"><?php echo $content; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END CONTENT -->
                </div>
                <!-- END SIDEBAR & CONTENT -->
            </div>
        </div>
        <!-- BEGIN PRE-FOOTER -->
        <div class="pre-footer">
            <div class="container">
                <div class="row"></div>
            </div>
        </div>
        <!-- END PRE-FOOTER -->

        <!-- BEGIN FOOTER -->
        <div class="footer">
          <div class="container">
            <div class="row">
              <!-- BEGIN COPYRIGHT -->
              <div class="col-md-6 col-sm-6 padding-top-10">
                2014 © Metronic Shop UI. ALL Rights Reserved. <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a>
              </div>
              <!-- END COPYRIGHT -->
              <!-- BEGIN PAYMENTS -->
              <div class="col-md-6 col-sm-6">
                <ul class="social-footer list-unstyled list-inline pull-right">
                  <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                  <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                  <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                  <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                  <li><a href="#"><i class="fa fa-skype"></i></a></li>
                  <li><a href="#"><i class="fa fa-dropbox"></i></a></li>
                </ul>  
              </div>
              <!-- END PAYMENTS -->
            </div>
          </div>
        </div>
        <!-- END FOOTER -->
    </div>
    <!-- END GLOBAL -->
    <!-- chargement des différentes libraireis javascript -->
    <!-- Load javascripts at bottom, this will reduce page load time -->
    <!-- BEGIN CORE PLUGINS (REQUIRED FOR ALL PAGES) -->
    <!--[if lt IE 9]>
    <script src="<?php echo _PLUGINS_DIR_;?>respond.min.js"></script>
    <![endif]-->
    <script src="<?php echo _PLUGINS_DIR_;?>jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo _PLUGINS_DIR_;?>jquery-migrate.min.js" type="text/javascript"></script>
    <script src="<?php echo _PLUGINS_DIR_;?>bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="<?php echo _THEME_FO_JS_DIR_.'back-to-top.js'; ?>" type="text/javascript"></script>
    <!-- END CORE PLUGINS -->
    <!-- BEGIN PAGE LEVEL JAVASCRIPTS (REQUIRED ONLY FOR CURRENT PAGE) -->
    <script src="<?php echo _PLUGINS_DIR_;?>fancybox/source/jquery.fancybox.pack.js" type="text/javascript"></script><!-- pop up -->
	<!-- BEGIN PAGE LEVEL PLUGINS -->
    <?php if(isset($pluginsJS)) :?>
    <?php foreach ($pluginsJS as $key => $value):?>
        <?php if(!empty($key)) :?><script src="<?php echo $key;?>" type="text/javascript"></script><?php endif; ?>
    <?php endforeach;?> 
    <?php endif; ?>
    
    <script src="<?php echo _THEME_FO_JS_DIR_.'layout.js'; ?>" type="text/javascript"></script>    
    <script src="<?php echo _THEME_FO_JS_DIR_.'custom.js'; ?>" type="text/javascript"></script>
	<?php if(isset($tabJS)) :?>
		<?php foreach ($tabJS as $key => $value):?>
		<?php if(!empty($key)) :?><script src="<?php echo $key;?>" type="text/javascript"></script><?php endif; ?>
		<?php endforeach;?> 
	<?php endif; ?>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            Layout.init();
            //Layout.initTwitter();
        });
    </script>
</body>
</html>
