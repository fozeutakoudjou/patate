<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7 lt-ie6 " lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8 ie7" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9 ie8" lang="en"> <![endif]-->
<!--[if gt IE 8]> <html lang="fr" class="no-js ie9" lang="en"> <![endif]-->
<html lang="<?php echo $iso;?>">
<head>
	<meta charset="utf-8">

	<meta name="viewport" content="width=device-width, initial-scale=0.75, maximum-scale=0.75, user-scalable=0">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<link rel="icon" type="image/x-icon" href="<?php echo $tools->getMedia($imgDir.'favicon.ico');?>" />
	<link rel="apple-touch-icon" href="<?php echo $tools->getMedia($imgDir.'app_icon.png');?>" />

	<meta name="robots" content="NOFOLLOW, NOINDEX">
	<title><?php echo $metaTitle;?></title>
	
	<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $librariesDir;?>css/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $librariesDir;?>css/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $librariesDir;?>bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $librariesDir;?>uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $librariesDir;?>bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
	
	<?php $tools->includeTpl('stylesheets', false, array('libraryKey'=>$libraryKey, 'notLibraryKey'=>$notLibraryKey, 'cssFiles'=>$cssFiles, 'cssContents'=>$cssContents), false);?>
	
	<link href="<?php echo $tools->getMedia($cssAdminThemeDir.'components.min.css');?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $tools->getMedia($cssAdminThemeDir.'plugins.min.css');?>" rel="stylesheet" type="text/css"/>
	<link href="<?php echo $tools->getMedia($cssAdminThemeDir.'layout.min.css');?>" rel="stylesheet" type="text/css"/>
    <link id="style_color" href="<?php echo $tools->getMedia($cssAdminThemeDir.'themes/darkblue.min.css');?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo $tools->getMedia($cssAdminThemeDir.'custom.css');?>" rel="stylesheet" type="text/css"/>
	
	<?php $tools->includeTpl('javascript', false, array('partKey'=>$headKey, 'libraryKey'=>$libraryKey, 'notLibraryKey'=>$notLibraryKey, 'jsFiles'=>$jsFiles, 'jsContents'=>$jsContents, 'jsVariables'=>$jsVariables), false);?>
<?php if(isset($additionalHeader)):?>
	<?php echo $additionalHeader;?>
<?php endif;?>
</head>

<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white">
<div class="page-wrapper">
<?php if($useOfHeader):?>
	<div class="page-header navbar navbar-fixed-top">
		<!-- BEGIN HEADER INNER -->
		<div class="page-header-inner">
			<!-- BEGIN LOGO -->
			<div class="page-logo">
				<a href="admin/index.html">
					<img src="<?php echo $imgAdminThemeDir;?>logo.png" alt="logo" class="logo-default"/>
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
							<img alt="" class="img-circle" src="<?php echo ''.'Utilisateurs/'.'small'  ?>"/>
							<span class="username">
							Admin </span>
							<i class="fa fa-angle-down"></i>
						</a>
						<ul class="dropdown-menu">
							<li>
								<a href="utilisateur-profil-''; ?>.html">
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
								<a href="<?php echo '';?>admin/deconnexion.html">
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
	<div class="clearfix"> </div>
<?php endif;?>