<div class="form-group clearfix <?php echo $item->drawWrapperClasses();?> <?php echo $item->getWrapperWidth();?> <?php echo $item->drawWrapperErrorClass();?>" 
	style="<?php echo $item->drawVisible();?>">
	<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
	<label class="control-label visible-ie8 visible-ie9"><?php echo $item->getLabel();?></label>
	<?php 
		$iconClass = '';
		if($item->hasLeftIcon()){
			$iconClass = $item->getLeftIcon()->isAddOnIcon() ? 'input-group' : 'input-icon';
		}
		if($item->hasRightIcon()){
			$class = $item->getRightIcon()->isAddOnIcon() ? 'input-group' : 'input-icon';
			$iconClass = ($class == $iconClass) ? $iconClass : $iconClass.' '.$class;
		}
	?>
	<div class="clearfix <?php echo $iconClass;?>">
		<?php if($item->hasLeftIcon()):?>
			<?php if($item->getLeftIcon()->isAddOnIcon()):?>
				<span class="input-group-addon">
			<?php endif;?>
			<?php echo $item->getLeftIcon()->generate();?>
			<?php if($item->getLeftIcon()->isAddOnIcon()):?>
				</span>
			<?php endif;?>
		<?php endif;?>
		<input type="<?php echo $item->getType();?>" name="<?php echo $item->getName();?>" value="<?php echo $item->getValue();?>"
			class="form-control form-control-solid placeholder-no-fix <?php echo $item->drawClasses();?>"  <?php echo $item->drawAttributes();?>
			placeholder="<?php echo $item->getLabel();?>" />
		
		<?php if($item->hasRightIcon()):?>
			<?php if($item->getRightIcon()->isAddOnIcon()):?>
				<span class="input-group-addon">
			<?php endif;?>
			<?php echo $item->getRightIcon()->generate();?>
			<?php if($item->getRightIcon()->isAddOnIcon()):?>
				</span>
			<?php endif;?>
		<?php endif;?>
	</div>
	<div class="help-block help-block-error" style="<?php echo $item->drawErrorTextVisible();?>">
		<?php echo $item->getErrorText();?>
	</div>
	<?php if($item->hasHelpText()):?>
	<div class="help-block">
		<?php echo $item->getHelpText();?>
	</div>
	<?php endif;?>
</div>