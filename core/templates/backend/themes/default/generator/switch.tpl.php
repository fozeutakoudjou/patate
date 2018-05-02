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
	<div class="btn-group" data-toggle="buttons">
		<?php $options = $item->getOptions();?>
		<?php foreach($options as $value => $label):?>
		<?php $checked = $item->isOptionSelected($value);?>
		<label class="btn btn-default <?php if($value==1):?> btn-on <?php else:?> btn-off<?php endif;?> <?php if($checked):?> active <?php endif;?>">
			<input type="radio" value="<?php echo $value;?>" name="<?php echo $item->getName();?>" class="<?php echo $item->drawClasses();?>" 
				<?php echo $item->drawAttributes();?> <?php if($checked):?> checked <?php endif;?> />
			<?php echo $label;?>
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