<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="Description" content=""/>
        <meta name="Keywords" content=""/>
        <meta http-equiv="Content-Language" content="fr"/>
        <meta name="robots" content="index,follow"/>
        <link rel="stylesheet" href="<?php echo _THEME_CSS_DIR_.'frontend.css'; ?>" type="text/css" media="screen"/>
         
    <title>Crystals FrameWork</title>
    
    </head>    
    <body id="index">
        
        <div id="global">
            <div id="header" class="clearfix">
                <div id="inner-header" class="clearfix"> </div>
				<div class="clear"></div>
            </div>
             
            <div id="container" class="clearfix">
                <div id="contenu" class="clearfix"> 
                    <?php echo $content; ?>        
                </div>
                <div id="footer" class="clearfix">
                </div>
            </div>
            <!-- chargement des différentes libraireis javascript -->
            <script src="<?php echo _WEB_JS_DIR_;?>jquery-1.7.1.min.js" type="text/javascript"></script>
            <script src="<?php echo _THEME_JS_DIR_.'frontend.js'; ?>" type="text/javascript"></script>          
            
        </div>
    </body>
</html>
