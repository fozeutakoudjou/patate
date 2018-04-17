<?php if(!$item->isContentOnly()):?>
	<tr class="btn <?php echo $item->drawClasses();?> <?php echo $item->drawWrapperClasses();?>" 
		<?php echo $item->drawAttributes();?> style="<?php echo $item->drawVisible();?>">
<?php endif;?>

<?php if(!$item->isContentOnly()):?>
	</tr>
<?php endif;?>