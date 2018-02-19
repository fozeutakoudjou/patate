<?php
    /**
     * Description of ConfigSMTPController
     *
     * @author Luc Alfred MBIDA
     *
     */

namespace Applications\Modules\ConfigSMTP\Backend\Controller;

if( !defined('IN') ) die('Hacking Attempt');

use Helper\HelperBackController;
use Library\HttpRequest;
use Applications\Modules\ConfigSMTP\Form\ConfigSMTPForm;
use Library\Tools;

class ConfigSMTPController extends HelperBackController{
        // Inserer votre code ici!
        protected $name = "ConfigSMTP";
        
    private function leftcolumn(){
        $out = array();
        $out['configurations.html']   = 'Configuration generale';
        $out['emailconfig.html']      = 'e-mail confuguration';
        
     
        return $this->page->addVar('left_content', $out);

    }
    
    public function executeConfigSMTP(HttpRequest $request) {
        
        $this->leftcolumn();
        $this->page->addVar('title', 'Configuration de serveur de Mail');
        
        //On lit les configurations du mail
        $dataArray  = $this->getMailConfig();
        
        if($request->getMethod('post')){ 
            if($this->saveXML($_POST)){
                $this->infos[] = $this->l('Update succefull');
            }else{
                $this->errors[] = $this->l('Update fieled');
            }
            $dataArray = $_POST;
   
        }
        
        $this->page->addVar('errors', $this->errors);
        $this->page->addVar('infos', $this->infos);
        $this->page->addVar('dataForm', ConfigSMTPForm::getForm($dataArray));
    
    }
    
    /**
     * sauvegarde les paramètres de configuration du serveur de mail dans un xml
     * @param array $array
     */
    private function saveXML(Array $array){
        $xml = new \XMLWriter();
        $exception = array('uniqid','id','submit');
        
        if($xml->openURI(_SITE_CONFIG_DIR_.'mailconfig.xml')){
            // Indiquons que nous souhaitons que le fichier soit indenté
            $xml->setIndent(True);
            // On indique le type du document XML
            $xml->startDocument('1.0', 'ISO-8859-1');
            // On ajoute le noeud : <param>
            $xml->startElement ('params');
            // les sous noeud en fonction des différentes variables
            $xml->startElement ('items');
            foreach ($array as $key => $value) {
                if(!in_array($key, $exception))
                    $xml->writeElement($key , $value);
            }
            // On ferme le noeud
            $xml->endElement();
            $xml->endElement();
            return true;
        }else{
            return false;
        }
    }
}
?>