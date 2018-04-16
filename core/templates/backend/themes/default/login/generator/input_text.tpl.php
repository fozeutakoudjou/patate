<div class="form-group clearfix">
	<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
	<label class="control-label visible-ie8 visible-ie9"><?php echo $item->getLabel();?></label>
	<div class="<?php if($item->hasLeftIcon()):?>input-icon<?php endif;?>">
		<?php if($item->hasLeftIcon()):?><?php echo $item->getLeftIcon()->generate();?><?php endif;?>
		<input type="<?php echo $item->getType();?>" name="<?php $item->getName();?>" id="<?php echo $item->getName();?>" value="<?php echo $item->getValue();?>"
			class="form-control form-control-solid placeholder-no-fix <?php echo $item->drawClasses();?>"  <?php echo $item->drawAttributes();?>
			placeholder="<?php echo $item->getLabel();?>" />
	</div>
</div>