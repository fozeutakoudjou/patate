<?php if($item->isDecorated()):?>
	<div class="panel <?php echo $item->drawClasses();?>" <?php echo $item->drawAttributes();?>>
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
		<div>
<?php else:?>
	<div  class=" <?php echo $item->drawClasses();?>" <?php echo $item->drawAttributes();?>>
<?php endif;?>

<?php echo $item->generateContent();?>

<?php if($item->isDecorated()):?>
		</div>
	<?php if($item->hasFooter()):?>
		<div class="panel-footer">
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