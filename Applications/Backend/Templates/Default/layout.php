<!DOCTYPE html>
<!-- 
Template Name: Crystals framework 
Version: 2.1.0
Author: Crystals-services
Website: http://www.crystals-services.com/
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="fr" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
	<meta charset="utf-8"/>
	<title><?php if (!isset($title)) { echo _WELCOME_ADMIN_ ;} else { echo $title; }?></title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<meta content="width=device-width, initial-scale=1" name="viewport"/>
	<meta name="description" content=""/>
    <meta name="keywords" content=""/>
    <meta name="author" content="<?php echo __AUTHOR__?>"/>
    
	<meta name="robots" content="nofollow" />
    
    <title><?php echo __TITLE__;?></title>
    
	<link rel="shortcut icon" href="<?php echo _IMG_DIR_?>favicon.ico">
	<!-- BEGIN GLOBAL MANDATORY STYLES -->

	<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
	<link href="<?php echo _PLUGINS_DIR_;?>font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo _PLUGINS_DIR_;?>simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo _PLUGINS_DIR_;?>bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo _PLUGINS_DIR_;?>uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo _PLUGINS_DIR_;?>bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
	<!-- END GLOBAL MANDATORY STYLES -->
	<!-- BEGIN PAGE LEVEL PLUGIN STYLES -->
	<?php if(isset($pluginsCSS)) :?>
        <?php foreach ($pluginsCSS as $key => $media):?>
        <link href="<?php echo $key;?>" rel="stylesheet" type="text/css" media="<?php echo $media;?>"/>
        <?php endforeach;?> 
    <?php endif; ?>
	<!-- END PAGE LEVEL PLUGIN STYLES -->
	<!-- BEGIN THEME STYLES -->
    <link href="<?php echo _CSS_DIR_;?>components.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo _CSS_DIR_;?>plugins.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo _THEME_BO_CSS_DIR_;?>layout.css" rel="stylesheet" type="text/css"/>
    <link id="style_color" href="<?php echo _THEME_BO_CSS_DIR_;?>themes/default.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo _THEME_BO_CSS_DIR_;?>custom.css" rel="stylesheet" type="text/css"/>
    
    <!-- BEGIN PAGE STYLES -->
	<link href="<?php echo _THEME_BO_CSS_DIR_;?>tasks.css" rel="stylesheet" type="text/css"/>
	<!-- END PAGE STYLES -->
<?php if(isset($tabCSS)) :?>
    <?php foreach ($tabCSS as $key => $media):?>
	<link href="<?php echo $key;?>" rel="stylesheet" type="text/css" media="<?php echo $media;?>"/>
    <?php endforeach;?> 
<?php endif; ?>
<!-- END THEME STYLES -->
</head>
<body class="page-header-fixed" >
    
	<!-- BEGIN HEADER -->
	<div class="page-header navbar navbar-fixed-top">
		<!-- BEGIN HEADER INNER -->
		<div class="page-header-inner">
			<!-- BEGIN LOGO -->
			<div class="page-logo">
				<a href="<?php echo _BASE_URI_;?>admin/index.html">
					<img src="<?php echo _THEME_BO_IMG_DIR_;?>logo.png" alt="logo" class="logo-default"/>
				</a>
				<div class="menu-toggler sidebar-toggler hide">
					<!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
				</div>
			</div>
			<!-- END LOGO -->
			<!-- BEGIN RESPONSIVE MENU TOGGLER -->
			<div class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
			</div>
			<!-- END RESPONSIVE MENU TOGGLER -->
			<!-- BEGIN TOP NAVIGATION MENU -->
		<div class="top-menu">
			<ul class="nav navbar-nav pull-right">
				
				<!-- BEGIN USER LOGIN DROPDOWN -->
				<li class="dropdown dropdown-user">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
						<img alt="" class="img-circle" src="<?php echo _UPLOAD_DIR_.'Utilisateurs/'.'small'.$_SESSION['employee']['Avatar']  ?>"/>
						<span class="username">
						Admin </span>
						<i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu">
						<li>
							<a href="utilisateur-profil-<?php echo $_SESSION['employee']['id']; ?>.html">
							<i class="fa fa-user"></i> My Profile </a>
						</li>
						<li>
							<a href="#">
							<i class="fa fa-calendar"></i> My Calendar </a>
						</li>
						<li>
							<a href="#">
								<i class="fa fa-envelope"></i> My Inbox <span class="badge badge-danger">
								3 </span>
							</a>
						</li>
						<li>
							<a href="#">
								<i class="fa fa-tasks"></i> My Tasks <span class="badge badge-success">
								7 </span>
							</a>
						</li>
						<li class="divider">
						</li>
						<li>
							<a href="<?php echo _BASE_URI_;?>admin/deconnexion.html">
							<i class="fa fa-key"></i> Log Out </a>
						</li>
					</ul>
				</li>
				<!-- END USER LOGIN DROPDOWN -->
				<!-- END USER LOGIN DROPDOWN -->
			</ul>
		</div>
		<!-- END TOP NAVIGATION MENU -->
	</div>	
  </div>	
	<!-- END HEADER -->
	<div class="clearfix">
	</div>
		
	<!-- BEGIN CONTAINER -->
	<div class="page-container">
		<!-- BEGIN SIDEBAR -->
		<div class="page-sidebar-wrapper">
			
			<div class="page-sidebar navbar-collapse collapse">
				<!-- BEGIN SIDEBAR MENU -->
				<ul class="page-sidebar-menu" data-auto-scroll="false" data-auto-speed="200">
					<!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
					<li class="sidebar-toggler-wrapper">
						<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
						<div class="sidebar-toggler">
						</div>
						<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
					</li>
					<!-- DOC: To remove the search box from the sidebar you just need to completely remove the below "sidebar-search-wrapper" LI element -->
					<li class="sidebar-search-wrapper hidden-xs">
						<!-- BEGIN RESPONSIVE QUICK SEARCH FORM -->
						<!-- DOC: Apply "sidebar-search-bordered" class the below search form to have bordered search box -->
						<!-- DOC: Apply "sidebar-search-bordered sidebar-search-solid" class the below search form to have bordered & solid search box -->
						<form class="sidebar-search" action="extra_search.html" method="POST">
							<a href="javascript:;" class="remove">
							</a>
							<div class="input-group">
								<input type="text" class="form-control" placeholder="Search...">
								<span class="input-group-btn">
									<!-- DOC: value=" ", that is, value with space must be passed to the submit button -->
									<input class="btn submit" type="button" type="button" value=" "/>
								</span>
							</div>
						</form>
						<!-- END RESPONSIVE QUICK SEARCH FORM -->
					</li>
					<li class="start<?php if( $curr_module == 'Index') echo ' active';?>">
						<a href="index.html">
							<i class="fa fa-home"></i>
							<span class='titre'>Acceuil</span>
							<span class="selected"></span>
						</a>
					</li>
					<?php if($this->app->employee()->haveModuleAccess('Configurations') || $this->app->employee()->isSuperAdmin()){ ?>
					  <li class="<?php if(( $curr_module == 'Configurations')||( $curr_module == 'ConfigSMTP')) echo 'active';?>">
							<a href="configurations.html">
								<i class="fa fa-cogs"></i>
								<span class="title">Configuration</span>
								<span class="selected"></span>
							</a>
                            <?php if(isset($left_content) && (($curr_module == "Configurations") ||($curr_module == "ConfigSMTP"))):?>
                                <ul class="sub-menu">
                                    <?php foreach ($left_content as $key => $value):?>
                                         <li <?php if( preg_match('#'.$key.'#', $link->requestURI())) echo ' class="active"';?>>
                                             <a href="<?php echo _BASE_URI_;?>admin/<?php echo $key; ?>">
                                                 <span class="title"><?php echo $value; ?></span>
                                             </a>
                                         </li>
                                     <?php endforeach; ?>
                               </ul>
                            <?php endif;?>
					  </li> 
					<?php } ?>
					
					<?php if($this->app->employee()->haveModuleAccess('Utilisateurs') || $this->app->employee()->isSuperAdmin()): ?>
					<li class="<?php if( $curr_module == "Utilisateurs") echo 'active';?>">
						<a href="utilisateurs.html"> 
							<i class="fa fa-user"></i>
							<span class="title">Utilisateurs</span>
							<span class="selected"></span>
						</a>
                        <?php if(isset($left_content) && ($curr_module == "Utilisateurs")):?>
                            <ul class="sub-menu">
                                <?php foreach ($left_content as $key => $value):?>
                                     <li <?php if( preg_match('#'.$key.'#', $link->requestURI())) echo ' class="active"';?>>
                                         <a href="<?php echo _BASE_URI_;?>admin/<?php echo $key; ?>">
                                             <span class="title"><?php echo $value; ?></span>
                                         </a>
                                     </li>
                                 <?php endforeach; ?>
                           </ul>
                        <?php endif;?>
					</li>
					<?php endif; ?>
                    <?php if($this->app->employee()->haveModuleAccess('ModuleCreator') || $this->app->employee()->isSuperAdmin()){ ?>
                        <li <?php if( preg_match('#modulecreator.html#', $link->requestURI())) echo ' class="active"';?>>
                            <a href="modulecreator.html">
							 <i  class="fa fa-puzzle-piece"></i>
                                <span class="title">Créer un module</span>
								<span class="selected"></span>
                            </a>
                        </li>
                     <?php } ?>
					 <li <?php if( preg_match('#list-menu.html#', $link->requestURI())) echo ' class="active"';?>>
                            <a href="list-menu.html">
							 <i  class="fa fa-puzzle-piece"></i>
                                <span class="title">Menu</span>
								<span class="selected"></span>
                            </a>
                        </li>
					<?php if($this->app->employee()->haveModuleAccess('Menu') || $this->app->employee()->isSuperAdmin()){ ?>
					<li class="<?php if( $curr_module == "Menu") echo 'active';?>">
						<a href="cree-menu.html"> 
							<i class="fa fa-table"></i>
							<span class="title">Créer un menu</span>
						</a>
                        <?php if(isset($left_content) && ($curr_module == "Menu")):?>
						<ul class="sub-menu">
							<?php foreach ($left_content as $key => $value):?>
							<li <?php if( preg_match('#'.$key.'#', $link->requestURI())) echo ' class="active"';?>>
								<a href="<?php echo _BASE_URI_;?>admin/<?php echo $key; ?>">
									<span class="title"><?php echo $value; ?></span>
								</a>
							</li>
							<?php endforeach; ?>
						</ul>
                        <?php endif;?>
					</li>
					<?php } ?>
					
				</ul>
				<!-- END SIDEBAR MENU -->
			</div>
		</div>
	
	<!-- END SIDEBAR -->
	<!-- BEGIN CONTENT -->
		<div class="page-content-wrapper">
			<div class="page-content">
				<!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
				<div class="modal fade" id="portlet-config" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
								<h4 class="modal-title">Modal title</h4>
							</div>
							<div class="modal-body">
								Widget settings form goes here
							</div>
							<div class="modal-footer">
								<button type="button" class="btn blue">Save changes</button>
								<button type="button" class="btn default" data-dismiss="modal">Close</button>
							</div>
						</div>
						<!-- /.modal-content -->
					</div>
					<!-- /.modal-dialog -->
				</div>
				<!-- /.modal -->
				<!-- END SAMPLE PORTLET CONFIGURATION MODAL FORM-->
				<!-- BEGIN STYLE CUSTOMIZER -->
				<div class="theme-panel hidden-xs hidden-sm">
					<div class="toggler">
					</div>
					<div class="toggler-close">
					</div>
					<div class="theme-options">
						<div class="theme-option theme-colors clearfix">
							<span>
							THEME COLOR </span>
							<ul>
								<li class="color-default current tooltips" data-style="default" data-original-title="Default">
								</li>
								<li class="color-darkblue tooltips" data-style="darkblue" data-original-title="Dark Blue">
								</li>
								<li class="color-blue tooltips" data-style="blue" data-original-title="Blue">
								</li>
								<li class="color-grey tooltips" data-style="grey" data-original-title="Grey">
								</li>
								<li class="color-light tooltips" data-style="light" data-original-title="Light">
								</li>
								<li class="color-light2 tooltips" data-style="light2" data-html="true" data-original-title="Light 2">
								</li>
							</ul>
						</div>
						<div class="theme-option">
							<span>
							Layout </span>
							<select class="layout-option form-control input-small">
								<option value="fluid" selected="selected">Fluid</option>
								<option value="boxed">Boxed</option>
							</select>
						</div>
						<div class="theme-option">
							<span>
							Header </span>
							<select class="page-header-option form-control input-small">
								<option value="fixed" selected="selected">Fixed</option>
								<option value="default">Default</option>
							</select>
						</div>
						<div class="theme-option">
							<span>
							Sidebar </span>
							<select class="sidebar-option form-control input-small">
								<option value="fixed">Fixed</option>
								<option value="default" selected="selected">Default</option>
							</select>
						</div>
						<div class="theme-option">
							<span>
							Sidebar Position </span>
							<select class="sidebar-pos-option form-control input-small">
								<option value="left" selected="selected">Left</option>
								<option value="right">Right</option>
							</select>
						</div>
						<div class="theme-option">
							<span>
							Footer </span>
							<select class="page-footer-option form-control input-small">
								<option value="fixed">Fixed</option>
								<option value="default" selected="selected">Default</option>
							</select>
						</div>
					</div>
				</div>
				<!-- END STYLE CUSTOMIZER -->
                
                <!-- Message Alert -->
                 <?php if(!empty($_SESSION['message_show'])){ 
                    $sh = $_SESSION['message_show'];
                    $typm = $_SESSION['message_type'];
                    
                    switch ($typm) {
                        case 0:
                        ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-danger">
                                        <button class="close" data-close="alert">&times;</button>
                                        <strong>Error!</strong> <?php echo $sh; ?> 
                                    </div>
                                </div>
                            </div>
                        <?php 
                        break;
                        case 1: ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-success">
                                        <button class="close" data-close="alert">&times;</button>
                                        <strong>Error!</strong> <?php echo $sh; ?> 
                                    </div>
                                </div>
                            </div>
                        <?php 
                         break;
                        case 2: ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-info">
                                            <button class="close" data-close="alert">&times;</button>
                                            <strong>Error!</strong> <?php echo $sh; ?> 
                                        </div>
                                    </div>
                                </div>
                         break;
                        case 3: ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-block">
                                            <button class="close" data-close="alert">&times;</button>
                                            <strong>Error!</strong> <?php echo $sh; ?> 
                                        </div>
                                    </div>
                                </div>
                        <?php }
                        $_SESSION['message_show'] = '';
                    }
                    ?>
						<!-- End Message Alert -->
                
				<?php echo $content; ?>
		<!-- END CONTENT -->
				
			</div>	
		</div>	
	</div>
    <!-- BEGIN FOOTER -->
	<div class="page-footer">
		<div class="page-footer-inner">
			<div class="copyright">
                <?php echo __NAME__;?> <?php echo $this->l('is application developped by'); ?>
                <a href="<?php echo __AUTHOR_URL__;?>" title="<?php echo __AUTHOR__;?>" target="_blank"><?php echo __AUTHOR__;?></a>
                <span><?php echo date('Y');?> &copy;.&nbsp;<?php echo $this->l('All right reserved'); ?></span>
            </div>
		</div>
		<div class="page-footer-tools">
			<span class="go-top">
			<i class="fa fa-angle-up"></i>
			</span>
		</div>
	</div>
	<!-- END FOOTER -->
    <!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
    <!-- BEGIN CORE PLUGINS -->
    <!--[if lt IE 9]>
    <script src="<?php echo _PLUGINS_DIR_;?>respond.min.js"></script>
    <script src="<?php echo _PLUGINS_DIR_;?>excanvas.min.js"></script> 
    <![endif]-->
    <script src="<?php echo _PLUGINS_DIR_;?>jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo _PLUGINS_DIR_;?>jquery-migrate.min.js" type="text/javascript"></script>
    <!-- IMPORTANT! Load jquery-ui.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
    <script src="<?php echo _PLUGINS_DIR_;?>jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
    <script src="<?php echo _PLUGINS_DIR_;?>bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="<?php echo _PLUGINS_DIR_;?>bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
    <script src="<?php echo _PLUGINS_DIR_;?>jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <script src="<?php echo _PLUGINS_DIR_;?>jquery.blockui.min.js" type="text/javascript"></script>
    <script src="<?php echo _PLUGINS_DIR_;?>jquery.cokie.min.js" type="text/javascript"></script>
    <script src="<?php echo _PLUGINS_DIR_;?>uniform/jquery.uniform.min.js" type="text/javascript"></script>
    <script src="<?php echo _PLUGINS_DIR_;?>bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
    <!-- END CORE PLUGINS -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->
	<?php if(isset($pluginsJS)) :?>
    <?php foreach ($pluginsJS as $key => $value):?>
        <?php if(!empty($key)) :?><script src="<?php echo $key;?>" type="text/javascript"></script><?php endif; ?>
    <?php endforeach;?> 
    <?php endif; ?>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="<?php echo _SCRIPTS_DIR_;?>metronic.js" type="text/javascript"></script>
    <script src="<?php echo _THEME_BO_JS_DIR_;?>layout.js" type="text/javascript"></script>
    <script src="<?php echo _THEME_BO_JS_DIR_;?>quick-sidebar.js" type="text/javascript"></script>
    <script src="<?php echo _THEME_BO_JS_DIR_;?>demo.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL SCRIPTS -->
    
    <!-- END JAVASCRIPTS -->
	
    <script type="text/javascript">
        var base_url = '<?php echo _BASE_URI_; ?>';
    </script>
        
    <!-- END PAGE LEVEL SCRIPTS -->
<?php if(isset($tabJS)) :?>
    <?php foreach ($tabJS as $key => $value):?>
        <?php if(!empty($key)) :?><script src="<?php echo $key;?>" type="text/javascript"></script><?php endif; ?>
    <?php endforeach;?> 
<?php endif; ?>
    <script>
        jQuery(document).ready(function() {    
            Metronic.init(); // init metronic core components
            Layout.init(); // init current layout
            QuickSidebar.init(); // init quick sidebar
            Demo.init(); // init demo features
        });
    </script>
    <script>
        remoteIp = "<?php echo $link->resquestIp();?>";
    jQuery(document).ready(function() {
       Metronic.init(); // init metronic core componets
       Layout.init(); // init layout
    });
    </script>
		<!-- END JAVASCRIPTS -->
	</body>
	<!-- END BODY -->
</html>