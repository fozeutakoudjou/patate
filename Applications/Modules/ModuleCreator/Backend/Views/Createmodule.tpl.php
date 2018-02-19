	    <div class="row">
				<div class="col-md-12">
	        		<h3 class="page-title"><?php echo $this->l("Creer un Module", 'ModuleCreator'); ?></h3>
							<ul class="page-breadcrumb breadcrumb">
								<li>
									<i class="fa fa-home"></i>
									<a href="index.html">Acceuil</a>
									<i class="fa fa-angle-right"></i>
								</li>
								<li>
									<a href="#">Cr&eacute;e un module</a>
									<i class="fa fa-angle-right"></i>
								</li>
							</ul>
							<!-- END PAGE TITLE & BREADCRUMB-->
				</div>
			</div>
<div class="table">
    <fieldset>
            <legend>Paramètres du module</legend>
             <?php echo $dataForm  ?>
            <br/>
            <fieldset>
                <legend>Param&eacute;tres des champs</legend>
                <div id="fields-zone"></div>
            </fieldset>
            <br/><br/>
            <div class="form-actions">
                        <button class="btn green" type="submit" value="creer">Valider</button>
        </form>
    </fieldset>
</div>
