<?php
/**
 * Description of ErrorsController
 *
 * @author ffozeu
 *
 */

namespace Applications\Modules\Errors\Backend\Controller;

if( !defined('IN') ) die('Hacking Attempt');

use Helper\HelperBackController;
use Library\HttpRequest;

use Library\Tools;

class ErrorsController extends HelperBackController{
    // Inserer votre code ici!
    protected $name = "Errors";
    
    public function executeErrors(){
         
    }
}
?>