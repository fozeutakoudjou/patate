<?php if(!$item->isFieldOnly()):?>
<div class="form-group clearfix">
	<?php if(!$item->isLabelDisabled()):?>
		<?php if($item->hasLabelObject()):?>
			<?php echo $item->getLabelObject()->generate();?>
		<?php else:?>
			<label class="control-label <?php echo $item->getLabelWidth();?>">
				<?php echo $item->getLabel();?>
			</label>
		<?php endif;?>
	<?php endif;?>
	<div class="<?php echo $item->getWidth();?>">
<?php endif;?>
		<?php $options = $item->getOptions();?>
		<select class="form-control <?php echo $item->drawClasses();?>"  <?php echo $item->drawAttributes();?> name="<?php echo $item->getName();?>">
			<?php foreach($options as $value => $label):?>
				<option value="<?php echo $value;?>" <?php if($item->isOptionSelected($value)):?>selected<?php endif;?>><?php echo $label;?></option>
			<?php endforeach;?>
		</select>
		<?php if($item->hasHelpText()):?>
		<div class="clearfix">
			<?php echo $item->getHelpText();?>
		</div>
		<?php endif;?>
<?php if(!$item->isFieldOnly()):?>
	</div>
</div>
<?php endif;?>