<?php if(!$item->isFieldOnly()):?>
<div class="form-group clearfix <?php echo $item->drawWrapperClasses();?> <?php echo $item->getWrapperWidth();?> <?php echo $item->drawWrapperErrorClass();?>" 
	style="<?php echo $item->drawVisible();?>">
	<?php if(!$item->isLabelDisabled()):?>
		<?php if($item->hasLabelObject()):?>
			<?php echo $item->getLabelObject()->generate();?>
		<?php else:?>
			<label class="control-label <?php echo $item->getLabelWidth();?>">
				<?php echo $item->getLabel();?>
			</label>
		<?php endif;?>
	<?php endif;?>
	<div class=" <?php echo $item->getWidth();?>">
<?php endif;?>
		<?php 
			$iconClass = '';
			if($item->hasLeftIcon()){
				$iconClass = $item->getLeftIcon()->isAddOnIcon() ? 'input-group' : 'input-icon';
			}
			if($item->hasRightIcon()){
				$class = $item->getRightIcon()->isAddOnIcon() ? 'input-group' : 'input-icon';
				$iconClass = ($class == $iconClass) ? $iconClass : $iconClass.' '.$class;
			}
			/*if(!$item->hasIcon()){
				$icon = $item->hasLeftIcon() ? $item->getLeftIcon() : $item->getRightIcon();
				$iconClass = $icon->isAddOnIcon() ? 'input-group' : 'input-icon';
			}*/
		?>
		<div class="clearfix <?php if(!$item->isTranslatable()):?><?php echo $iconClass;?><?php endif;?>">
			<?php if($item->isTranslatable()):?>
				<div class="col-lg-10 translatable-div <?php echo $iconClass;?>">
			<?php else:?>
				<?php $languages=array(''=>'');?>
			<?php endif;?>
			<?php if($item->hasLeftIcon()):?>
				<?php if($item->getLeftIcon()->isAddOnIcon()):?>
					<span class="input-group-addon">
				<?php endif;?>
				<?php echo $item->getLeftIcon()->generate();?>
				<?php if($item->getRightIcon()->isAddOnIcon()):?>
					</span>
				<?php endif;?>
			<?php endif;?>
			
			<?php foreach($languages as $key => $lang):?>
				<?php $name = $tools->getFieldName($item->getName(), $key);?>
				<input type="<?php echo $item->getType();?>" name="<?php echo $name;?>" value="<?php echo $item->getFieldValue($key);?>"
				class="form-control <?php echo $item->drawClasses();?> <?php echo $item->getLangClass($key);?>"  <?php echo $item->drawAttributes();?>
				<?php if($item->hasPlaceholder()):?> placeholder="<?php echo $item->getPlaceholder();?>" <?php endif;?>  style="<?php echo $item->getLangVisible($key);?>"/>
			<?php endforeach;?>
			<?php if($item->hasRightIcon()):?>
				<?php if($item->getRightIcon()->isAddOnIcon()):?>
					<span class="input-group-addon">
				<?php endif;?>
				<?php echo $item->getRightIcon()->generate();?>
				<?php if($item->getRightIcon()->isAddOnIcon()):?>
					</span>
				<?php endif;?>
			<?php endif;?>
			<?php if($item->isTranslatable()):?>
				</div>
				<div class="col-lg-2 lang_list_div">
					<?php echo $item->drawLangList();?>
				</div>
			<?php endif;?>
		</div>
		<?php foreach($languages as $key => $lang):?>
		<div class="help-block help-block-error" style="<?php echo $item->drawErrorTextVisible($key);?>">
			<?php echo $item->getErrorText($key);?>
		</div>
		<?php endforeach;?>
		<?php if($item->hasHelpText()):?>
		<div class="help-block">
			<?php echo $item->getHelpText();?>
		</div>
		<?php endif;?>
<?php if(!$item->isFieldOnly()):?>
	</div>
</div>
<?php endif;?>