<?php $item->prepareContent();?>
<?php if(!$item->isContentOnly()):?>
<form class="login-form <?php echo $item->drawClasses();?>" <?php echo $item->drawAttributes();?> action="<?php echo $item->getFormAction();?>"
	method="<?php echo $item->getMethod();?>" style="<?php echo $item->drawVisible();?>">
<?php endif;?>
<input type="hidden" name="<?php echo $item->getSubmitAction();?>" value="1"/>
<div class="form-title">
	<span class="form-title"><?php echo $item->getLabel();?> </span>
	<span class="form-subtitle"><?php echo $item->getSubLabel();?></span>
</div>
<div class="alert alert-danger" style="<?php echo $item->drawErrorVisible();?>">
	<button type="button" class="close" data-dismiss="alert">&times;</button> <?php echo $item->getErrorText();?> 
</div>
<?php echo $item->getChild('email')->generate();?>
<div class="form-actions">
	<?php if($item->hasCancel()):?> <?php $cancelBtn = $item->getCancel(); $cancelBtn->addClass('btn-default'); echo $cancelBtn->generate();?> <?php endif;?>
	<?php if($item->hasSubmit()):?> <?php $submitBtn = $item->getSubmit(); $submitBtn->addClass('btn-primary uppercase pull-right'); echo $submitBtn->generate();?> <?php endif;?>
</div>
<?php if(!$item->isContentOnly()):?>
</form>
<?php endif;?>