<?php if(isset($confirmations) && count($confirmations) && $confirmations):?>
	<div class="bootstrap">
		<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<ul class="list-unstyled">
				<?php foreach($confirmations as $confirmation):?>
					<li><?php echo $confirmation;?></li>
				<?php endforeach;?>
			</ul>
		</div>
	</div>
<?php endif;?>