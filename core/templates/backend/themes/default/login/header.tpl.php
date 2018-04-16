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
	
	<?php $tools->includeTpl('../stylesheets', false, array('libraryKey'=>$libraryKey, 'notLibraryKey'=>$notLibraryKey, 'cssFiles'=>$cssFiles, 'cssContents'=>$cssContents), false);?>
	
	<link href="<?php echo $tools->getMedia($cssAdminThemeDir.'components.min.css');?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $tools->getMedia($cssAdminThemeDir.'plugins.min.css');?>" rel="stylesheet" type="text/css"/>
	
    <link href="<?php echo $tools->getMedia($cssAdminThemeDir.'login-2.css');?>" rel="stylesheet" type="text/css"/>
	
	<?php $tools->includeTpl('../javascript', false, array('partKey'=>$headKey, 'libraryKey'=>$libraryKey, 'notLibraryKey'=>$notLibraryKey, 'jsFiles'=>$jsFiles, 'jsContents'=>$jsContents, 'jsVariables'=>$jsVariables), false);?>
<?php if(isset($additionalHeader)):?>
	<?php echo $additionalHeader;?>
<?php endif;?>
</head>

<body class="login">