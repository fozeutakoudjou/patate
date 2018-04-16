<div class="btn-group">
	<button class="btn dropdown-toggle" data-toggle="dropdown" type="button">
		<?php if(isset($languages[$activeLang])):?><?php echo $languages[$activeLang]->getIsoCode();?><?php endif;?>
		<i class="fa fa-caret-down"></i>
	</button>
	<ul class="dropdown-menu">
		<?php foreach($languages as $key => $lang):?>
			<li class="<?php if($activeLang == $key):?>active<?php endif;?>"><a href="#" tabindex="-1"><?php echo $lang->getName();?></a></li>
		<?php endforeach;?>
	</ul>
</div>