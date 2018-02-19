<img src="../Themes/backend/backend_images/bg-th-left.gif" width="8" height="7" alt="" class="left" />
<img src="../Themes/backend/backend_images/bg-th-right.gif" width="7" height="7" alt="" class="right" />
<table class="listing" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th></th>
            <th width="120">drapeau</th>
            <th width="150">Libellé</th>
            <th width="20">Iso Code</th>
            <th>Code </th>
            <th>Activé</th>
            <th class="last">Actions</th>
        </tr>
    </thead>
    <tbody>
<?php foreach($datalist as $lang):  ?>
    <tr>
        <td class="first style1"><input type="checkbox" name="eltcheck[]" class="elttocheck" value="<?php echo $lang->getId_lang(); ?>"></td>
        <td ><img alt="<?php echo $lang->getName()  ?>" src="<?php echo _UPLOAD_DIR_.'Lang/'.$lang->getLanguage_code().'.jpg';  ?>" /></a></td>
        <td><?php echo $lang->getName()  ?></td>
        <td><?php echo $lang->getIso_code(); ?></td>
        <td><?php echo $lang->getLanguage_code() ?></td>
        <td><?php echo $lang->getActive()?'Oui':'Non'; ?></td>
        <td class="last"> 
            <?php if($this->app->employee()->haveRightTo('edit')){ ?>
                <a href="lang-edit-<?php echo $lang->getId_lang() ?>.html" title="modifiier"><img src="<?php echo _THEME_BO_IMG_DIR_.'edit-icon.gif'; ?>" style="width:16px; height:16px;" alt="&Eacute;diter" /></a> 
            <?php } ?>
            <?php if($this->app->employee()->haveRightTo('delete')){ ?>
                <a class="delete_elt" href="lang-delete-<?php echo $lang->getId_lang() ?>.html" title="supprimer" onclick="return(confirm('Êtes-vous sûr?'));"><img src="<?php echo _THEME_BO_IMG_DIR_.'hr.gif'; ?>" style="width:16px; height:16px;" alt="Supprimer" /></a>
            <?php } ?>
        </td>

    </tr>
<?php endforeach;?>
    </tbody>
</table>

<div class="select">
    <?php if( isset($pagination) ) echo $pagination; ?>
</div>