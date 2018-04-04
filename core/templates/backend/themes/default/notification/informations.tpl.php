<?php if(isset($informations) && count($informations) && $informations):?>
	<div class="bootstrap">
		<div class="alert alert-info">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<ul class="list-unstyled">
				<?php foreach($informations as $information):?>
					<li><?php echo $information;?></li>
				<?php endforeach;?>
			</ul>
		</div>
	</div>
<?php endif;?>