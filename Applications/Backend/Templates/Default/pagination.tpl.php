<?php ob_start(); ?>
<?php if(sizeof($pagination)):?>
<div id="paginate" class="pager">
    <form method="POST" id="paginate-form" class="form-paginate">
		<img src="<?php echo _THEME_BO_IMG_DIR_;?>first.png" class="navigate first"/>
		<img src="<?php echo _THEME_BO_IMG_DIR_;?>prev.png" class="navigate prev"/>
        <span class="current_page"><?php echo $pagination['current_page'] ?></span>/<span class="number_page"><?php echo $pagination['nberPage'] ?></span>
		<img src="<?php echo _THEME_BO_IMG_DIR_;?>next.png" class="navigate next"/>
		<img src="<?php echo _THEME_BO_IMG_DIR_;?>last.png" class="navigate last"/>
        <input type="hidden" name="nberpage" value="<?php echo $pagination['nber_per_page'] ?>" id="nberpage" />
        <input type="hidden" name="currentpage" value="<?php echo $pagination['current_page'] ?>" id="currentpage" />
        <?php foreach($pagination['sup_params'] as $key => $data):  ?>
        <input type="hidden" name="<?php echo $key ?>" value="<?php echo $data ?>" id="<?php echo $key ?>" />
        <?php endforeach;?>
	</form>
</div>
<?php endif; ?>
<?php $contentpage = ob_get_clean();
$cache->setCache($cacheName,$contentpage);
echo $contentpage;
?>
