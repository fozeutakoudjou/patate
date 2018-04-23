<?php if(!$item->isFieldOnly()):?>
<div class="form-group clearfix <?php echo $item->drawWrapperClasses();?> <?php echo $item->getWrapperWidth();?>" style="<?php echo $item->drawVisible();?>">
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
	<div class="mt-radio-inline">
		<?php $options = $item->getOptions();?>
		<?php foreach($options as $value => $label):?>
			<label class="mt-radio mt-radio-outline"><?php echo $label;?>
				<input type="radio" value="<?php echo $value;?>" name="<?php echo $item->getName();?>" id="<?php echo $item->getOptionId($value);?>" <?php if($item->isOptionSelected($value)):?> checked <?php endif;?>
					class="<?php echo $item->drawClasses();?>"  <?php echo $item->drawAttributes();?> />
				<span></span>
			</label>
		<?php endforeach;?>
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