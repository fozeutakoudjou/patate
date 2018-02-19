<?php
    /**
     * Description of CategoriesController
     *
     * @author MBIDA Luc
     *
     */

namespace Applications\Modules\Configurations\Backend\Controller;

if( !defined('IN') ) die('Hacking Attempt');

use Helper\HelperBackController;
use Library\HttpRequest;
use Library\Tools;
use Applications\Modules\Configurations\Form\ConfigurationsForm;

class ConfigurationsController extends HelperBackController{
        // Inserer votre code ici!
    
     private function leftcolumn(){
        $out = array();
        $out['configurations.html']   = 'Configuration generale';
        $out['emailconfig.html']      = 'e-mail configuration';
        return $this->page->addVar('left_content', $out);
    }

    public function executeConfigurationscreate(HttpRequest $request){
        // On ajoute une définition pour le titre
        //var_dump($_FILES);
        $this->leftcolumn();
        $this->page->addVar('title', 'Configuration du site');
        
        $managerlang = $this->managers->getManagerOf('Lang');
        $data = $managerlang->findAll2();
        $langs = array();
        foreach ($data as $value) {
            $langs[$value->getLanguage_code()] = '<img alt="'.$value->getName().'" title="'.$value->getName().'" src="'. _UPLOAD_DIR_.'Lang/'.$value->getLanguage_code().'.jpg" />';
        }
        //chargement des configuration depuis le xml
        $dataArray = $this->getConfig();
        //var_dump((bool)$dataArray['responseby']);die();
        if($request->getMethod('post')){
             if($this->saveXML($_POST)){
                $this->infos = $this->l('Update succefull');
            }else{
                $this->errors = $this->l('Update fieled');
            }
            $dataArray = $_POST;
            
        }
        $this->page->addVar('infos', $this->infos);
        $this->page->addVar('errors', $this->errors);
        $this->page->addVar('dataForm', ConfigurationsForm::getForm($dataArray, $langs));
    }
    
    /**
     * sauvegarde les paramètres de configuration dans un xml
     * @param array $array
     */
    private function saveXML(Array $array){
        $xml = new \XMLWriter();
        $exception = array('uniqid','id','submit');
        
        if($xml->openURI(_SITE_CONFIG_DIR_.'appconfig.xml')){
            // Indiquons que nous souhaitons que le fichier soit indenté
            $xml->setIndent(True);
            // On indique le type du document XML
            $xml->startDocument('1.0', 'ISO-8859-1');
            // On ajoute le noeud : <param>
            $xml->startElement ('configurations');
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