<?php if($item->isTextIcon()):?>
	<span class=" <?php echo $item->drawClasses();?>" <?php echo $item->drawAttributes();?>><?php echo $item->getValue();?></span>
<?php else:?>
	<i class="fa fa-<?php echo $item->getValue();?> <?php echo $item->drawClasses();?>" <?php echo $item->drawAttributes();?>></i>
<?php endif;?>