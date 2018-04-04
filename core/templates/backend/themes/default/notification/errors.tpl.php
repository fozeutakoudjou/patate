<?php if(isset($errors) && count($errors) && (current($errors) != '') && (!isset($disableDefaultErrorOutPut) || ($disableDefaultErrorOutPut == false))):?>

	<div class="bootstrap">
		<div class="alert alert-danger">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		<?php if (count($errors) == 1):?>
			<?php reset($errors);?>
		<?php else:?>
			<?php echo sprintf($tools->l('%d errors'), count($errors));?>
			<br/>
			<ol>
				<?php foreach($errors as $error):?>
					<li><?php echo $error;?></li>
				<?php endforeach;?>
			</ol>
		<?php endif;?>
		</div>
	</div>
<?php endif;?>