<?php $menu = $item->getValue();?>
<?php $link = $item->getContent();?>
<?php if($item->isFirst() && ($menu->getLevel()>1)):?><ul class="sub-menu"><?php endif;?>
<li class="<?php if($item->isFirst()):?>start<?php endif;?>  <?php echo $item->drawWrapperClasses();?> <?php echo $item->drawClasses();?>" <?php echo $item->drawAttributes();?> style="<?php echo $item->drawVisible();?>">
	<a href="<?php echo $link->getHref();?>" title="<?php echo $link->getTitle();?>" class="<?php echo $link->drawClasses();?>" <?php echo $link->drawAttributes();?>>
		<?php if($link->hasIcon()):?><?php echo $link->getIcon()->generate();?><?php endif;?>
		<span class="title"><?php echo $link->getLabel();?></span>
		<span class="selected"></span>
	</a>	
