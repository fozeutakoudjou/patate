<?php echo $header;?>
<div class="page-content">
	<?php $tools->includeTpl('../notification/confirmations', false);?>
	<?php $tools->includeTpl('../notification/informations', false);?>
	<?php $tools->includeTpl('../notification/warnings', false);?>
	<?php $tools->includeTpl('../notification/errors', false);?>
	
	<?php echo $page; ?>
</div>	
<?php echo $footer;?>