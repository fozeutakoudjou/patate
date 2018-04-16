<?php if(!$item->isContentOnly()):?>
<form class="<?php echo $item->drawClasses();?>" <?php echo $item->drawAttributes();?> action="<?php echo $item->getFormAction();?>" 
	method="<?php echo $item->getMethod();?>" style="<?php echo $item->drawVisible();?>">
<?php endif;?>
<input type="hidden" name="<?php $item->getSubmitAction();?>" value="1"/>
<?php if($item->isDecorated()):?>
	<div class="panel">
	<?php if($item->hasHeader()):?>
		<div class="panel-heading">
			<?php if($item->hasIcon() || $item->hasLabel()):?>
				<span>
				<?php if($item->hasIcon()):?> <?php echo $item->getIcon()->generate();?> <?php endif;?>
				<?php echo $item->getLabel();?>
				</span>
			<?php endif;?>
			<?php $headers = $item->getHeaders();?>
			<?php foreach($headers as $header):?>
				<?php echo $header->generate();?>
			<?php endforeach;?>
			
		</div>
	<?php endif;?>
		<div class="form-wrapper">
<?php else:?>
	<div>
<?php endif;?>

<?php echo $item->generateContent();?>

<?php if($item->isDecorated()):?>
		</div>
	<?php if($item->hasFooter()):?>
		<div class="panel-footer">
			<?php if($item->hasCancel()):?> <?php echo $item->getCancel()->generate();?> <?php endif;?>
			<?php if($item->hasSubmit()):?> <?php echo $item->getSubmit()->generate();?> <?php endif;?>
			<?php $footers = $item->getFooters();?>
			<?php foreach($footers as $footer):?>
				<?php echo $footer->generate();?>
			<?php endforeach;?>
		</div>
	<?php endif;?>
	</div>
<?php else:?>
	</div>
<?php endif;?>

<?php if(!$item->isContentOnly()):?>
</form>
<?php endif;?>