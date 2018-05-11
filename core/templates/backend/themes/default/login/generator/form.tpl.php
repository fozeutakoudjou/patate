<?php $item->prepareContent();?>
<?php if(!$item->isContentOnly()):?>
<form class="login-form <?php echo $item->drawClasses();?>" <?php echo $item->drawAttributes();?> action="<?php echo $item->getFormAction();?>"
	method="<?php echo $item->getMethod();?>" style="<?php echo $item->drawVisible();?>">
<?php endif;?>
<input type="hidden" name="<?php echo $item->getSubmitAction();?>" value="1"/>
<?php echo $item->getChild('redirectData')->generate();?>
<h3 class="form-title"><?php echo $item->getLabel();?> </h3>
<div class="alert alert-danger" style="<?php echo $item->drawErrorVisible();?>">
	<button type="button" class="close" data-dismiss="alert">&times;</button> <?php echo $item->getErrorText();?> 
</div>
<?php echo $item->getChild('email')->generate();?>
<?php echo $item->getChild('password')->generate();?>
<div class="form-actions">
	<?php if($item->hasSubmit()):?> <?php $submitBtn = $item->getSubmit(); $submitBtn->addClass('red btn-block uppercase'); echo $submitBtn->generate();?> <?php endif;?>
</div>
<div class="form-actions">
	<div class="pull-left">
		<?php echo $item->getChild('stay_logged_in')->generate();?>
	</div>
	<div class="pull-right forget-password-block">
		<?php echo $item->getChild('forget_password')->generate();?>
	</div>
</div>
<?php if(!$item->isContentOnly()):?>
</form>
<?php endif;?>