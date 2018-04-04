<?php if(isset($cssFiles[$libraryKey])):?>
	<?php foreach($cssFiles[$libraryKey] as $uri => $params):?>
		<link rel="stylesheet" href="<?php echo $uri;?>" type="text/css" media="<?php if(isset($params['media'])): echo $params['media']; endif;?>" />
	<?php endforeach;?>
<?php endif;?>

<?php if(isset($cssContents)):?>
	<?php foreach($cssContents as $cssContent):?>
		<?php echo $cssContent;?>
	<?php endforeach;?>
<?php endif;?>

<?php if(isset($cssFiles[$notLibraryKey])):?>
	<?php foreach($cssFiles[$notLibraryKey] as $uri => $params):?>
		<link rel="stylesheet" href="<?php echo $uri;?>" type="text/css" media="<?php if(isset($params['media'])): echo $params['media']; endif;?>" />
	<?php endforeach;?>
<?php endif;?>
