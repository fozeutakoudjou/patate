<div class="top-bar">
    <h1>Format des mails</h1>
</div><br />
<div class="select-bar"></div>
<?php if(!empty($infos)): ?>
    <div class="infos"><img alt="ok" src="<?php echo _THEME_BO_IMG_DIR_; ?>ok2.png" /> <?php echo $infos; ?></div>
<?php endif; ?>
<?php if(!empty($errors)): ?>
    <div class="error"><img alt="error" src="<?php echo _THEME_BO_IMG_DIR_; ?>error2.png" /> <?php echo $errors; ?></div>
<?php endif; ?>
<div class="table">
    <?php echo $dataForm  ?>
</div>
