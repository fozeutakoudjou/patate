<script type="text/javascript">
var refreshId = setInterval(function(){
     $('#une').load('ajax-url.html');
}, 1000);
</script>

<?php if(!empty($enchersUne)): ?>
    <div id="une">
    <?php foreach($enchersUne as $enchers):  ?>
		<div class="iconsu">
			<div class="iconu">
				<span class="rt"><?php echo $enchers->getRetime(); ?></span>
			</div>
			<div class="iconu">
				<span class="pc<?php echo $pc  ?>"><?php  echo $enchers->getLimite(); ?>%</span>
			</div>
			<div class="iconu">
				<span class="ae<?php echo $ae  ?>"><?php  echo $enchers->getAe(); ?></span>
			</div>
		</div>
		<div class="titreu"><?php  echo $enchers->getNom(); ?></div>
		<div id="effect<?php  echo $enchers->getId(); ?>">
			<a href="detail-product-<?php  echo $enchers->getId(); ?>.html">
				<div class="imageu"><img src="<?php  echo _UPLOAD_DIR_ ?>encheres/<?php  echo $enchers->getImages()?>" alt="<?php  echo $enchers->getNom(); ?>" title="<?php  echo $enchers->getNom(); ?>" style="height:134px; width:130px;" /></div>
			</a>
		</div>
		<div class="temps">
			<?php if( $enchers->getPause() == 0 ): ?>
				<?php echo $timeEn ?>
			<?php else: ?>
				EN PAUSE
			<?php endif; ?>
		</div>
		<div class="enchere"><?php  echo $enchers->getEnchere(); ?> &euro;</div>
		<div class="prixu">
			Prix magasin: <?php  echo $enchers->getPrix(); ?> &euro;<br />
			<span class="meneuru">Meneur:</span><span class="pseudou"><?php  echo $enchers->getPseudo(); ?></span>
		</div>
        <div class="encheriru">
            <?php if( $site->getHourClose() <= date('H:i:s') ): ?>
                <img src="<?php  echo _THEME_IMG_DIR_ ?>bt-avenir.jpg" alt="Indisponible" />
            <?php elseif($user->isAuthenticated() AND $enchers->getMeneur() == $user->getId()): ?>
                <img src="<?php  echo _THEME_IMG_DIR_ ?>bt-meneur.jpg" alt="Meneur" />
            <?php else: ?>
				<a href="#" id="encherir-<?php  echo $enchers->getId(); ?>"><img src="<?php  echo _THEME_IMG_DIR_ ?>encherir.jpg" alt="Ench&eacute;rir" /></a>
			<?php endif; ?>			  
		</div>
    <?php endforeach;?>
    </div>
<?php endif;?>
<div id="index-mod">my first module</div>
