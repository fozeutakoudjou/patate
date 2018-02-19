<div class="top-bar">
    
    <a href="Createmodule.html" title="Ajouter une bannière" class="button">ADD NEW</a>
    <h1>Gestion des modules</h1>
    <div class="breadcrumbs"><a href="adversiting.html">Bannières Publicitaires</a> / Liste</div>
    
</div><br />
<div class="select-bar"></div>
<?php if(!empty($infos)): ?>
       <div class="infos"><img alt="ok" src="/backend_images/ok2.png" /> <?php echo $infos; ?></div>
   <?php endif; ?>
<form name="" action="actiongroupedadv.html" method="post" id="groupaction">
    <input type="hidden" name="statusAdv" value="all" />
    <div id="toolsbar">
        <ul>
            <li><a href="#" id="checkall">tout cocher</a></li>
            <li><a href="#" id="uncheckall">tout décocher</a></li>
            <li>
                <select name="actionselect" id="actionselect">
                    <option value="">Pour la selection</option>
                    <option value="delete">Supprimer</option>
                    <option value="active">activer</option>
                    <option value="unactive">desactiver</option>
                </select>
            </li>
        </ul>
    </div>
    <div class="table">

        <img src="../Themes/backend/backend_images/bg-th-left.gif" width="8" height="7" alt="" class="left" />
        <img src="../Themes/backend/backend_images/bg-th-right.gif" width="7" height="7" alt="" class="right" />

        <table class="listing" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th></th>
                    <th class="first" width="27">N¤</th>
                    <th width="120">module</th>
                    <th width="150">auteur</th>
                    <th class="last">Actions</th>
                </tr>
            </thead>
            <tbody>
        <?php foreach($dataList as $advers):  ?>
            <tr>
                <td ><input type="checkbox" name="eltcheck[]" class="elttocheck" value="<?php echo $advers->getId(); ?>"></td>
                <td class="first style1"><?php echo $advers->getId() ?></td>
                <td class="first style1"><a href="adversiting-detail-<?php echo $advers->getId() ?>.html" title="plus de détail"><?php echo $advers->getAltText()  ?></a></td>
                <td><a href="adversiting-detail-<?php echo $advers->getId() ?>.html" title="plus de détail"><img alt="<?php echo $advers->getAltText()  ?>" src="<?php echo _UPLOAD_DIR_.'Adversiting/'.array_shift(explode(';',$advers->getImage()))  ?>" width="100"/></a></td>
                <td><?php echo $advers->getActive()?'enabled':'disabled' ?></td>
                <td><?php echo date_format(date_create($advers->getDateBegin()), 'd/m/Y') ?></td>
                <td><?php echo $dateend[$advers->getId()]; ?></td>
                <td><?php echo $advers->getFinalPrice() ?></td>
                <td class="last"> <a href="adversiting-edit-<?php echo $advers->getId() ?>.html" title="modifiier"><img src="../Themes/backend/backend_images/edit-icon.gif" style="width:16px; height:16px;" alt="&Eacute;diter" /></a> <a class="delete_elt" href="adversiting-delete-<?php echo $advers->getId() ?>.html" title="supprimer" onclick="return(confirm('Êtes-vous sûr?'));"><img src="../Themes/backend/backend_images/hr.gif" style="width:16px; height:16px;" alt="Supprimer" /></a></td>
            </tr>
        <?php endforeach;?>
            </tbody>
        </table>

        <div class="select">
            <?php if( isset($pagination) ) echo $pagination; ?>
        </div>

    </div>
    <input type="hidden" name="actiontodo" value="./actiongroupedadv.html" id="actiontodo" />
</form>


