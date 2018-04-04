<?php if(isset($jsFiles[$partKey]) && isset($jsFiles[$partKey][$libraryKey])):?>
	<?php foreach($jsFiles[$partKey][$libraryKey] as $uri => $params):?>
		<script type="text/javascript" src="<?php echo $uri;?>" <?php if(isset($params['attributes'])): echo $params['attributes']; endif;?>></script>
	<?php endforeach;?>
<?php endif;?>

<?php if(isset($jsVariables[$partKey])):?>
	<script type="text/javascript">
	<?php foreach($jsVariables[$partKey] as $name => $params):?>
		var <?php echo $name;?> = <?php echo $tools->jsonEncode($params['value']);?>;
	<?php endforeach;?>
	</script>
<?php endif;?>

<?php if(isset($jsContents[$partKey])):?>
	<?php foreach($jsContents[$partKey] as $params):?>
		<?php echo $params['content'];?>
	<?php endforeach;?>
<?php endif;?>
<?php if(isset($jsFiles[$partKey]) && isset($jsFiles[$partKey][$notLibraryKey])):?>
	<?php foreach($jsFiles[$partKey][$notLibraryKey] as $uri => $params):?>
		<script type="text/javascript" src="<?php echo $uri;?>" <?php if(isset($params['attributes'])): echo $params['attributes']; endif;?>></script>
	<?php endforeach;?>
<?php endif;?>