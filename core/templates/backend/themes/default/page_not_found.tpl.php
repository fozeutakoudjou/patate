<div class="row">
	<div class="col-md-12 page-404">
		<div class="number font-green"> <?php echo $tools->l('404');?></div>
		<div class="details">
			<h3><?php echo $tools->l("Oops! You're lost.");?></h3>
			<p> <?php echo $tools->l("We can not find the page you're looking for.");?>
				<br>
				<a href="<?php echo $link->getAdminLink('', 'index');?>"> <?php echo $tools->l('Return home');?> </a> <?php echo $tools->l('or try the search bar below.');?> </p>
		</div>
	</div>
</div>