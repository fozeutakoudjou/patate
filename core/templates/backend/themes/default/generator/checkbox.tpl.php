<label class="mt-checkbox mt-checkbox-outline"><?php echo $item->getLabel();?>
	<input type="checkbox" value="<?php echo $item->getValue();?>" name="<?php echo $item->getName();?>" <?php if($item->isChecked()):?> checked <?php endif;?>
		class="<?php echo $item->drawClasses();?>"  <?php echo $item->drawAttributes();?> />
	<span></span>
</label>