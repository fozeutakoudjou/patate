<a href="<?php echo $item->getHref();?>" class="<?php if($item->isButtonStyleUsed()):?>btn <?php endif;?> <?php echo $item->drawClasses();?>" <?php echo $item->drawAttributes();?>>
	<?php if($item->hasCustomContent()):?>
		<?php echo $item->getCustomContent();?>
	<?php else:?>
		<?php if($item->hasIcon()):?> <?php echo $item->getIcon()->generate();?> <?php endif;?> <?php if(!$item->isLabelDisabled()):?><?php echo $item->getLabel();?><?php endif;?>
	<?php endif;?>
</a>