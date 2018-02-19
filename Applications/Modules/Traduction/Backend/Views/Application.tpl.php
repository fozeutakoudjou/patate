<div class="table">
    <form method="post">
        <p>
            <label><?php echo $this->l('Choisissez une langue de traduction'); ?></label>
            <?php 
                foreach ($langs as $value)
                    echo '<input type="radio" name="lang" value="'.$value->getLanguage_code().'"><img alt="'.$value->getName().'" title="'.$value->getName().'" src="'. _UPLOAD_DIR_.'Lang/'.$value->getLanguage_code().'.jpg" />';
                ?>
        </p>
        <?php 
            $i = 0;
            foreach ($strings as $mots){		
                foreach ($mots as $mot) {
        ?>
                    <p>
                        <label><?php echo $mot; ?></label>
                        <input type="text" value="" name="mot_<?php echo $i; ?>" />
                    </p>
                <?php
                    $i++;

                }
            }
        ?>
        </p> 
        <p>
            <input type="submit" value="traduire" />
         </p>
    </form>
</div>