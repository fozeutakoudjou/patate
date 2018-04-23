<div class="btn-group dropup">
	<button class="btn dropdown-toggle" data-toggle="dropdown" type="button">
		<?php echo $item->getLabel();?>
		<i class="fa fa-angle-up"></i>
	</button>
	<?php $contents=$item->getContents();?>
	<ul class="dropdown-menu">
		<?php foreach($contents as $content):?>
			<li><?php echo $content->generate();?></li>
			<?php if($content->getAdditional('separator')):?><li class="divider"></li><?php endif;?>
		<?php endforeach;?>
	</ul>
</div>