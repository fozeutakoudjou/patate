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
		<?php echo $item->getContent()->generate();?>
<?php if(!$item->isFieldOnly()):?>
	</div>
</div>
<?php endif;?>