<?php

/**
 * Description of ConnexionController
 *
 * @author FFOZEU
 */
namespace Applications\Modules\Utilisateurs\Frontend\Controller;

if( !defined('IN') ) die('Hacking Attempt');

    use Helper\HelperFrontController;
    use Library\HttpRequest;
    use Applications\Modules\Utilisateurs\Form\UtilisateursForm;
    use Library\Tools;

class UtilisateursController extends HelperFrontController{

    public function executeConnect(HttpRequest $request){
        
    }
    
    public function executeCreateuser(HttpRequest $request)
    {
        
	}
    
    public function executeForgetpwd(HttpRequest $request){
            
    }
}

?>