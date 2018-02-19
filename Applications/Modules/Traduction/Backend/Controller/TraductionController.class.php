<?php

/**
 * Description of CategoriesController
 *
 * @author zokankais
 * 
 */

namespace Applications\Modules\Traduction\Backend\Controller;

if( !defined('IN') ) die('Hacking Attempt');

//use Library\BackController;
use Helper\HelperBackController;
use Library\HttpRequest;
use Library\Tools;
use Applications\Modules\Traduction\Form\TraductionForm;

class TraductionController extends HelperBackController{

    
    private function leftcolumn(){
        $out = array();
        

        return $this->page->addVar('left_content', $out);

    }
    
    private function rightcolumn(){
        $out ='Gérer Les traductions';
        return $this->page->addVar('right_content', $out);
    }

    
    public function executeTraducion(HttpRequest $request){
        $this->leftcolumn();
        $this->rightcolumn();
        
        $this->page->addVar('title', 'Gestion des traductions');        
           
        
        
        
    }

    
    public function executeCreate(HttpRequest $request){
        // On ajoute une définition pour le titre     
        $module = $request->getValue('mod');
        $module = ucfirst($module);
        $this->page->addVar('title', 'Traduction du module '.$module);
        
        $manager = $this->managers->getManagerOf('Lang');
        $data = $manager->findAll2();
        $langs = array();
        foreach ($data as $value) {
            $langs[$value->getLanguage_code()] = '<img alt="'.$value->getName().'" title="'.$value->getName().'" src="'. _UPLOAD_DIR_.'Lang/'.$value->getLanguage_code().'.jpg" />';
        }
         
        $this->leftcolumn();
        $this->rightcolumn();
        $dataArray = array();
        
        $config = $this->getConfig();
        if(is_array($config)&& array_key_exists('lang', $config)&& !empty($config['lang']))
            $defaultlang = $config['lang'];
        else
            $defaultlang = 'fr';
        
        $dataArray['lang'] = $defaultlang;
        $dataForm = TraductionForm::getForm($dataArray,$this, $module, $langs);  
        if($request->getMethod('post')){  
            if($request->getExists('lang'))
                $defaultlang = $request->getValue ('lang');
            $strings = $this->app()->Translate()->GetStringTotranslate($module);
            $i = 0;
            $chaines = array();
            foreach ($strings as $mots){
                foreach ($mots as $mot) {
                    $tr = $request->getValue('mot_'.$i);	
                    $chaines[$mot] = $tr;
                    $i++;
                }

            }
            $this->app()->Translate()->SetStringtranslation($chaines,$module, $defaultlang);
        }      
        //$this->app()->Translate()->SetStringtranslation($dataArray,$this->module, $defaultlang);	
        $this->page->addVar('dataForm', $dataForm);
        
    }  
    
    public function executeApplication(HttpRequest $request){
        // On ajoute une définition pour le titre     
        $app = $request->getValue('app');
        $app = ucfirst($app);
        $this->page->addVar('title', 'Traduction du '.$app);
        
        $manager = $this->managers->getManagerOf('Lang');
        $langs = $manager->findAll2();
        
        $this->leftcolumn();
        $this->rightcolumn();
        $dataArray = array();
        
        $config = $this->getConfig();
        if(is_array($config)&& array_key_exists('lang', $config)&& !empty($config['lang']))
            $defaultlang = $config['lang'];
        else
            $defaultlang = 'fr';
        
        $dataArray['lang'] = $defaultlang;
        
         
        if($request->getMethod('post')){  
            if($request->getExists('lang'))
                $defaultlang = $request->getValue ('lang');
            $strings = $this->app()->Translate()->GetStringTotranslateApp($app);
            $i = 0;
            $chaines = array();
            foreach ($strings as $mots){
                foreach ($mots as $mot) {
                    $tr = $request->getValue('mot_'.$i);	
                    $chaines[$mot] = $tr;
                    $i++;
                }

            }
            $this->app()->Translate()->SetStringtranslationApp($chaines,$app, $defaultlang);
        }      
       
        $strings = $this->app()->Translate()->GetStringTotranslateApp($app);
       
        $this->page->addVar('strings', $strings);
        $this->page->addVar('langs', $langs);
        
    } 
    
}

?>
