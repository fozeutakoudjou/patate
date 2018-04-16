<?php if(!$item->isContentOnly()):?>
<form class="login-form <?php echo $item->drawClasses();?>" <?php echo $item->drawAttributes();?> action="<?php echo $item->getFormAction();?>"
	method="<?php echo $item->getMethod();?>" style="<?php echo $item->drawVisible();?>">
<?php endif;?>
<div class="form-title">
	<span class="form-title"><?php echo $item->getLabel();?> </span>
	<span class="form-subtitle"><?php echo $item->getSubLabel();?></span>
</div>
<?php echo $item->getChild('email_forgot')->generate();?>
<div class="form-actions">
	<?php if($item->hasCancel()):?> <?php $cancelBtn = $item->getCancel(); $cancelBtn->addClass('btn-default'); echo $cancelBtn->generate();?> <?php endif;?>
	<?php if($item->hasSubmit()):?> <?php $submitBtn = $item->getSubmit(); $submitBtn->addClass('btn-primary uppercase pull-right'); echo $submitBtn->generate();?> <?php endif;?>
</div>
<?php if(!$item->isContentOnly()):?>
</form>
<?php endif;?>