<?php if(!$item->isFieldOnly()):?>
<div class="form-group clearfix">
	<?php if(!$item->isLabelDisabled()):?>
		<?php if($item->hasLabelObject()):?>
			<?php echo $item->getLabelObject()->generate();?>
		<?php else:?>
			<label class="control-label col-lg-3">
				<?php echo $item->getLabel();?>
			</label>
		<?php endif;?>
	<?php endif;?>
	<div class="col-lg-9">
<?php endif;?>
		<div class="clearfix">
			<?php if($item->isTranslatable()):?>
				<div class="col-lg-9 translatable-field">
			<?php else:?>
				<?php $languages=array(''=>'');?>
			<?php endif;?>
			
			<?php if($item->hasLeftIcon()):?><?php echo $item->getLeftIcon()->generate();?><?php endif;?>
			
			<?php foreach($languages as $key => $lang):?>
				<?php $name = $tools->getFieldName($item->getName(), $key);?>
				<input type="<?php echo $item->getType();?>" name="<?php $name;?>" id="<?php echo $name;?>" value="<?php echo $item->getValue();?>"
				class="<?php echo $item->drawClasses();?>"  <?php echo $item->drawAttributes();?>
				<?php if(!$item->hasPlaceholder()):?> placeholder="<?php echo $item->getPlaceholder();?>" <?php endif;?>/>
			<?php endforeach;?>
			<?php if($item->hasRightIcon()):?><?php echo $item->getRightIcon()->generate();?><?php endif;?>
			<?php if($item->isTranslatable()):?>
				</div>
				<div class="col-lg-2">
					<?php echo $item->drawLangList();?>
				</div>
			<?php endif;?>
		</div>
		<?php if($item->hasHelpText()):?>
		<div class="clearfix">
			<?php echo $item->getHelpText();?>
		</div>
		<?php endif;?>
<?php if(!$item->isFieldOnly()):?>
	</div>
</div>
<?php endif;?>