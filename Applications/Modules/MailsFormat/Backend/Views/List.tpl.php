
<br><br>

<div class="top-bar">
    <a href="mails-format-add.html" class="button">Ajouter</a>
    <h1>Gestion des formats de mails</h1>
    <div class="breadcrumbs"><a href="mails-format.html">Format</a> / Liste</div>
</div><br />
<div class="select-bar"></div>
<?php if(!empty($infos)): ?>
       <div class="infos"><img alt="ok" src="<?php echo _THEME_BO_IMG_DIR_; ?>ok2.png" /> <?php echo $infos; ?></div>
   <?php endif; ?>
<form name="" action="actiongroupedformat.html" method="post" id="groupaction">
    <div id="toolsbar">
        <ul>
            <li><a href="#" id="checkall">tout cocher</a></li>
            <li><a href="#" id="uncheckall">tout décocher</a></li>
            <li>
                <select name="actionselect" id="actionselect">
                    <option value="">Pour la selection</option>
                    <option value="delete">Supprimer</option>
                </select>
            </li>
            <li>
                <input type="text" value="recherche" name="searchzone" id="searchzone" />
            </li>
             <li>
                <input type="button" value="rechercher" id="btnsearchzone" />
            </li>
        </ul>
    </div>
    <div class="table">
       <img src="<?php echo _THEME_BO_IMG_DIR_; ?>bg-th-left.gif" width="8" height="7" alt="" class="left" />
       <img src="<?php echo _THEME_BO_IMG_DIR_; ?>bg-th-right.gif" width="7" height="7" alt="" class="right" />
       <table class="listing" cellpadding="0" cellspacing="0">
           <thead>
            <tr>
                <th>ID</th>
                <th>Template Name</th>
                <th>Titre</th>
                <th>Content</th>
                <th>Active</th>
                <th>Date ajout</th>
                <th>Date mise à jour</th>
                <th class="last">Actions</th>
            </tr>
           </thead>
           <tbody>
            <?php foreach($datalist as $data):  ?>
            <tr>
                <td ><input type="checkbox" name="eltcheck[]" class="elttocheck" value="<?php echo $data->getId(); ?>"></td>
                <td><?php echo $data->getTemplate(); ?></td>
                <td><?php echo $data->getTitle(); ?></td>
                <td><?php echo $data->getContent(); ?></td>
                <td><img src="<?php echo _THEME_BO_IMG_DIR_; ?><?php echo ($data->getActive()?'enabled':'disabled'); ?>.gif" alt="State" /></td>
                <td><?php echo $data->getDate_add(); ?></td>
                <td><?php echo $data->getDate_upd(); ?></td>
                <td class="last">
                    <?php if($this->app->employee()->haveRightTo('delete')){ ?>
                    <a href="mails-format-delete-<?php echo $data->getId() ?>.html" title="Supprimer" onclick="return(confirm('Êtes-vous sûr de vouloir supprimer cet enregistrement?'));"><img src="<?php echo _THEME_BO_IMG_DIR_; ?>hr.gif" alt="Supprimer" /></a>
                    <?php }
                    if($this->app->employee()->haveRightTo('edit')){ ?>
                    <a href="mails-format-edit-<?php echo $data->getId() ?>.html" title="&Eacute;diter"><img src="<?php echo _THEME_BO_IMG_DIR_; ?>edit-icon.gif" alt="&Eacute;diter" /></a>
                    <?php } ?>
                </td>
            </tr>
            <?php endforeach;?>
           </tbody>
        </table>

        <div class="select">
            <?php if( isset($pagination) ) echo $pagination; ?>
        </div>
    </div>
     <input type="hidden" name="actiontodo" value="./actiongroupedformat.html" id="actiontodo" />
</form>
