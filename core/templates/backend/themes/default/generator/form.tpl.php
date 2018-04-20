<?php if($item->isDecorated()):?>
	<div class="portlet light bordered <?php echo $item->drawWrapperClasses();?> <?php echo $item->getWrapperWidth();?>" 
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
		<div class="portlet-body form">
<?php else:?>
	<div  class=" <?php echo $item->drawWrapperClasses();?> <?php echo $item->getWrapperWidth();?>" 
		style="<?php echo $item->drawVisible();?>">
<?php endif;?>
<?php if(!$item->isContentOnly()):?>
<form class="form-horizontal <?php echo $item->drawClasses();?>" <?php echo $item->drawAttributes();?> action="<?php echo $item->getFormAction();?>" 
	method="<?php echo $item->getMethod();?>">
<?php endif;?>
	<div class="form-body">
		<input type="hidden" name="<?php $item->getSubmitAction();?>" value="1"/>
		<?php echo $item->generateContent();?>
	</div>
	<?php if($item->hasFooter()):?>
		<div class="form-actions">
			<?php if($item->hasCancel()):?> <?php $cancel = $item->getCancel(); $cancel->addClass('default pull-left btn-lg'); echo $cancel->generate();?> <?php endif;?>
			<?php if($item->hasSubmit()):?> <?php $submit = $item->getSubmit(); $submit->addClass('green pull-right btn-lg'); echo $submit->generate();?> <?php endif;?>
			<?php $footers = $item->getFooters();?>
			<?php foreach($footers as $footer):?>
				<?php echo $footer->generate();?>
			<?php endforeach;?>
		</div>
	<?php endif;?>
<?php if(!$item->isContentOnly()):?>
</form>
<?php endif;?>
<?php if($item->isDecorated()):?>
		</div>
	</div>
<?php else:?>
	</div>
<?php endif;?>