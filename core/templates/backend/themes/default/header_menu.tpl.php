<?php $menu = $item->getValue();?>
<?php if($item->isFirst() && ($menu->getLevel()>1)):?><ul class="sub-menu"><?php endif;?>
<li class="<?php if($item->isFirst()):?>start<?php endif;?>  <?php echo $item->drawWrapperClasses();?>" <?php echo $item->drawAttributes();?>>
	<a href="<?php echo $item->getAdditional('href');?>" title="<?php echo $menu->getTitle();?>" class="<?php echo $item->drawClasses();?>" <?php echo $item->drawAttributes();?>>
		<?php if($item->hasIcon()):?><?php echo $item->getIcon()->generate();?><?php endif;?>
		<span class="title"><?php echo $menu->getName();?></span>
		<span class="selected"></span>
	</a>	
