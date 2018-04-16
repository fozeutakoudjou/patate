<?php echo $header;?>
<div class="logo">
	<a href="#">
		<img src="<?php echo $tools->getMedia($imgDir.'favicon.ico');?>" style="height: 17px;" alt="">
	</a>
</div>
<div class="content">
	<?php $tools->includeTpl('../notification/confirmations', false);?>
	<?php $tools->includeTpl('../notification/informations', false);?>
	<?php $tools->includeTpl('../notification/warnings', false);?>
	<?php $tools->includeTpl('../notification/errors', false);?>
	
	<?php echo $page; ?>
</div>	
<?php echo $footer;?>