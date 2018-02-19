<div class="top-bar">
    <!--<a href="#" class="button">ADD NEW</a>-->
    <h1>Gestionnaire des Images par défaut</h1>
</div><br />
<div class="select-bar"></div>
<?php if(!empty($infos)): ?>
    <div class="infos"><img alt="ok" src="/backend_images/ok2.png" /> <?php echo $infos; ?></div>
<?php endif; ?>
<?php if(!empty($errors)): ?>
    <div class="error"><img alt="error" src="/backend_images/error2.png" /> <?php echo $errors; ?></div>
<?php endif; ?>
<div class="table">
   <h2>Images par défault</h2>
    <?php echo $dataForm  ?>
    <div class="select">
        <strong><?php if( isset($var) ) echo $var; ?></strong>
    </div>
</div>

