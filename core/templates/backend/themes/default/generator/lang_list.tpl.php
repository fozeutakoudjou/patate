<div class="btn-group">
	<button class="btn dropdown-toggle" data-toggle="dropdown" type="button">
		<span class="<?php echo $item->drawClasses();?>"><?php echo $item->getLabel();?></span>
		<i class="fa fa-caret-down"></i>
	</button>
	<?php $contents=$item->getContents();?>
	<ul class="dropdown-menu">
		<?php foreach($contents as $content):?>
			<li class="<?php echo $content->getAdditional('parentClass');?>"><?php echo $content->generate();?></li>
		<?php endforeach;?>
	</ul>
</div>