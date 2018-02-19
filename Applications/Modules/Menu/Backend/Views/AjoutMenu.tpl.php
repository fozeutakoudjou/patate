<div class="row">
    <div class="col-md-12">
        <h3 class="page-title"><?php echo $this->l("Ajout Menu", 'Menu'); ?></h3>
        <ul class="page-breadcrumb breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="index.html">Accueil</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>
                <a href="#">Creer un Menu </a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>
                <a href="ajoutmenu.html">Ajout Menu</a>
            </li>
        </ul>
    </div>
</div>
<br />

<div class="table">
    <fieldset>
            <legend>Parametrage menu</legend>
             <?php echo $dataForm  ?>
            <br>
            <div id="fields-zone"></div>
        
    </fieldset>
</div>