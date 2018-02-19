
<div class="row">
    <div class="col-md-12">
        <h3 class="page-title"><?php echo $this->l("Gestion groupe Utilisateur", 'Utilisateurs'); ?></h3>
        <ul class="page-breadcrumb breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="index.html">Acceuil</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>
                <a href="utilisateurs.html">Utilisateurs</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>
                 <a href="add-groupuser.html">Ajouter un groupe d'utilisateur</a>
            </li>
        </ul>
        <!-- END PAGE TITLE & BREADCRUMB-->
    </div>
</div>
<?php if(!empty($errors)){ ?>
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-danger">
            <button class="close" data-close="alert"></button>
            <?php echo $errors; ?>
        </div>
    </div>
</div>
<?php } ?>

<!-- END PAGE HEADER-->
<div class="form-body" >
   <?php echo $dataForm  ?>
       <div class="acces_level">
           <div id="privileges" class="acces_item">
                   <legend><h3 class="form-section">Privil&eacute;ges</h3></legend>
               <div class="toolscheck">
                   <a href="#" onclick="return false;" class="check_all" id="privcheck">tout cocher</a> /
                   <a href="#" onclick="return false;" class="uncheck_all" id="privuncheck">tout décocher</a>
               </div> <br/>
               <div id="cb"><?php foreach ($privileges as $priv):?>
                <label class="checkbox-inline"><span>
                   <input type="checkbox" <?php echo (isset($privilegegroup[$priv->id]))?'checked="checked"':' '; ?> value="<?php echo $priv->id;?>" name="priv[]" class="privcheck"/>
                           </span><?php echo $priv->libelle;?> </label>
               <?php endforeach; ?></div>
           </div>

           <div id="module" class="acces_item">
              <legend><h3 class="form-section">Module</h3></legend> 
               <div class="toolscheck">
                   <a href="#"  class="check_all" id="modcheck">tout cocher</a> / 
                <a href="#"  class="uncheck_all" id="moduncheck">tout décocher</a> 
				 <!-- <input type="checkbox" id="cocheTout"/><span id="cocheText"> Tout Cocher</span>-->
               </div>
               <div id="cases"><?php foreach ($modules as $mod):?>
				   <div class="form-group">
						<label class="control-label col-md-2"><span>
                       </span><?php echo $mod['name'];?> </label>
					   <div class="col-md-10">
						   <table class="table-responsive">
							   <tr>
								   <?php foreach ($mod['link_access'] as $linkacc):?>
								   <td><label class="col-md-3"> <input type="checkbox" <?php if(isset($modulesgroup[$mod['id']]) && in_array($linkacc, $modulesgroup[$mod['id']])):?> checked="checked" <?php endif; ?>value="<?php echo $linkacc;?>" name="modlinks[<?php echo $mod['id']; ?>][]" class="modcheck" /> <?php echo str_replace('/admin/', '', $linkacc); ?></label></td>
								   <?php endforeach; ?></div>
							   </tr>
						   </table>
					   </div>
				   </div>
               <?php endforeach; ?></div>
       </div>
   </div>
 </div><br/><br/>  
 <div class="col-md-offset-4 col-md-8">
      <button class="btn green" type="submit" value=Enregistrer>Enregistrer</button>
   </div>

</form>
		
