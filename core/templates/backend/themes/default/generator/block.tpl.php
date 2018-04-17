<?php if($item->isDecorated()):?>
	<div class="portlet light bordered <?php echo $item->drawClasses();?> <?php echo $item->drawWrapperClasses();?>" <?php echo $item->drawAttributes();?>
		 style="<?php echo $item->drawVisible();?>">
	<?php if($item->hasHeader()):?>
		<div class="portlet-title">
			<?php if($item->hasIcon() || $item->hasLabel()):?>
				<div class="caption">
				<?php if($item->hasIcon()):?> <?php echo $item->getIcon()->generate();?> <?php endif;?>
				<?php echo $item->getLabel();?>
				</div>
			<?php endif;?>
			<?php $headers = $item->getHeaders();?>
			<?php if(!empty($headers)):?>
				<div class="actions">
				<?php foreach($headers as $header):?>
					<?php echo $header->generate();?>
				<?php endforeach;?>
				</div>
			<?php endif;?>
		</div>
	<?php endif;?>
		<div class="portlet-body">
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