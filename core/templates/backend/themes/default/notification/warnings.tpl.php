<?php if(isset($warnings) && count($warnings)):?>
	<div class="bootstrap">
		<div class="alert alert-warning">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php if(count($warnings)>1):?>
				<h4><?php echo sprintf($tools->l('There are %d warnings:'), count($warnings));?></h4>
			<?php endif;?>
			<ul class="list-unstyled">
				<?php foreach($warnings as $warning):?>
					<li><?php echo $warning;?></li>
				<?php endforeach;?>
			</ul>
		</div>
	</div>
<?php endif;?>