

<div class="row">
    <div class="col-md-12">
        <h3 class="page-title"><?php echo $this->l("Ajouter un Utilisateur", 'Utilisateurs'); ?></h3>
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
                 <a href="add-user.html">Ajouter un Utilisateur</a>
            </li>
        </ul>
        <!-- END PAGE TITLE & BREADCRUMB-->
    </div>
</div>
<!-- END PAGE HEADER-->
<?php if(!empty($errors)){ ?>
<div  class="row">
    <div class="col-md-12">
        <div class="alert alert-danger">
            <button class="close" data-close="alert"></button>
            <?php echo $errors; ?>
        </div>
    </div>
</div>


<?php } ?>	   		
		
<?php // echo $dataForm  ?>



<div class="form-body" >
	<?php echo $dataForm  ?>
    <legend><h3 class="form-section">Groupes Utilisateur</h3><legend>
     <div class="toolscheck ">
          <a href="#" onclick="return false;" class="check_all " id="groupcheck">tout cocher</a>/ 
           <a href="#" onclick="return false;" class="uncheck_all" id="groupuncheck">tout décocher</a>
        </div><br/>
            <div class="checkbox-list acces_item" id="grpeUser">
                    <?php foreach ($groupeutilisateur as $ug):?>
                    <label class="checkbox-inline"><span>
                    <input type="checkbox" <?php echo (isset($usergroup[$ug->id]))?'checked="checked"':' '; ?> value="<?php echo $ug->id;?>" name="groupe[]" class="case" />
                        </span><?php echo $ug->nom_groupe;?> </label>
                    <?php endforeach; ?>

             </div>   
</div>

<br/><br/>
<div class="col-md-offset-4 col-md-8">
	<button class="btn green" type="submit" onclick="checkEmail()" value=Enregistrer>Enregistrer</button>
</div>
</form>
		
		

