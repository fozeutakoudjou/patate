<?php
    // Inserer votre code ici!
?>
<h1 class="titre-errors"> Page 404</h2>
<p><?php echo $this->l("Si vous arrivez sur cette page, c'est tout simplement par ce que votre lien ne mène nulle part.") ?></p>
<ul>
    <li><?php echo $this->l('Vérifier si la route est prise en compte par le système') ?></li>
    <li><?php echo $this->l('Vérifier si la vue spécifiée est bien existante' )?></li>
    <li><?php echo $this->l('Vérifier si la méthode est bien definie') ?></li>
</ul>