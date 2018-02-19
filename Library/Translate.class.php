<?php
namespace Library;

if( !defined('IN') ) die('Hacking Attempt');
/**
 * Description of Translate
 *
 * @author zokankais
 */

// mettre la classe translate ds la classe application
class Translate extends ApplicationComponent {
    /**
    * Get translation for a string
    * @param string $filename
    * @param string $tring 
    * @return string
    */
    public  function getStringTranslation($string,$module, $iso = 'fr'){
        $filename = _MODULES_DIR_.$module.'/Lang/'.$iso.'.xml';
        if (file_exists($filename)){
            $key = md5(str_replace('\'', '\\\'', $string));
            $dom = new \DOMDocument; 
            $dom->load($filename);

            foreach ($dom->getElementsByTagName('tr') as $word){
                if (preg_match('`^'.$word->getAttribute('key').'$`', $key, $matches)){
                    $value = $word->getAttribute('value');
                    return $value;
                }
            }
            return $string;
        }
        else {
            return $string;
        }
    }
    
   
    	
    /**
    * Get all translatable strings from a module
    * @param string $module
    * @param string $tring 
    * @param string $iso
    * @return array
    */
    public  function GetStringTotranslate($module){
        $repertoire = _MODULES_DIR_.$module;

        $chaines = array();
        $tabs = array();
        $rep = new \RecursiveDirectoryIterator($repertoire, \FilesystemIterator::SKIP_DOTS);
        $extensions = array('html','php');
        foreach(new \RecursiveIteratorIterator($rep) as $file) {
            $fichier = $file->getPath().'/'.$file->getfilename();
            $extension = pathinfo($fichier,PATHINFO_EXTENSION );                           
            if (in_array($extension, $extensions) ){
                $tab = $this->ExtraireChaine($file->getPath().'/'.$file->getfilename());
                if(count($tab))
                    array_push($chaines, $tab);
            }
        }
        return $chaines;
    }
    
    

    /**
    * Set the translation  for given strings
    * @param array $translatable
    */
    public  function SetStringtranslation($translatable,$module,$iso){
        if (!file_exists(_MODULES_DIR_.$module.'/Lang')){
            mkdir(_MODULES_DIR_.$module.'/Lang');
        }
        file_put_contents(_MODULES_DIR_.$module.'/Lang/'.$iso.'.xml','');
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;
        $parent_node = $dom->createElement('traduction');
        $parent_node = $dom->appendChild($parent_node);    		
         foreach($translatable as $key => $word){
            $child_node = $dom->createElement('tr');
            $child_node = $parent_node->appendChild($child_node);	
            $child_node->setAttribute("key", (string)md5($key));
            $child_node->setAttribute("value",(string)$word);		 	
         }
         
         $dom->save(_MODULES_DIR_.$module.'/Lang/'.$iso.'.xml');
 	}
  	/**
  	*	Cette fonction recherche le paramètre de la fonction l() à l'interieur des lignes d'un fichier  	*
  	*	@param string $filename
  	*	@return array()   	*
  	*/
  	public function ExtraireChaine($filename){
  		$chaines = array();
        $matches = array();   
  		$file = fopen($filename,'r+');
  		while ($line = fgets($file)) {   
  			if (preg_match("#->l\(\"(.+)\"\)#",$line,$matches) || preg_match("#->l\(\'(.+)\'\)#",$line,$matches))
  				array_push($chaines,$matches[1]);		                   
  		}
        return $chaines;
  	} 
    
    // ######################### Application translation ###########################""
     public  function getStringTranslationApp($string,$module, $iso = 'fr'){
        $filename = _SITE_APP_DIR.$module.'/Lang/'.$iso.'.xml';
        if (file_exists($filename)){
            $key = md5(str_replace('\'', '\\\'', $string));
            $dom = new \DOMDocument; 
            $dom->load($filename);

            foreach ($dom->getElementsByTagName('tr') as $word){
                if (preg_match('`^'.$word->getAttribute('key').'$`', $key, $matches)){
                    $value = $word->getAttribute('value');
                    return $value;
                }
            }
            return $string;
        }
        else {
            return $string;
        }
    }
    
    /**
    * Get all translatable strings from a module
    * @param string $module
    * @param string $tring 
    * @param string $iso
    * @return array
    */
    public  function GetStringTotranslateApp($module){
        $repertoire = _SITE_APP_DIR.$module;

        $chaines = array();
        $tabs = array();
        $rep = new \RecursiveDirectoryIterator($repertoire, \FilesystemIterator::SKIP_DOTS);
        $extensions = array('html','php');
        foreach(new \RecursiveIteratorIterator($rep) as $file) {
            $fichier = $file->getPath().'/'.$file->getfilename();
            $extension = pathinfo($fichier,PATHINFO_EXTENSION );                           
            if (in_array($extension, $extensions) ){
                $tab = $this->ExtraireChaine($file->getPath().'/'.$file->getfilename());
                if(count($tab))
                    array_push($chaines, $tab);
            }
        }
        return $chaines;
    }
    
     /* Set the translation  for given strings
    * @param array $translatable
    */
    public  function SetStringtranslationApp($translatable,$module,$iso){
        if (!file_exists(_SITE_APP_DIR.$module.'/Lang')){
            mkdir(_SITE_APP_DIR.$module.'/Lang');
        }
        file_put_contents(_SITE_APP_DIR.$module.'/Lang/'.$iso.'.xml','');
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;
        $parent_node = $dom->createElement('traduction');
        $parent_node = $dom->appendChild($parent_node);    		
         foreach($translatable as $key => $word){
            $child_node = $dom->createElement('tr');
            $child_node = $parent_node->appendChild($child_node);	
            $child_node->setAttribute("key", (string)md5($key));
            $child_node->setAttribute("value",(string)$word);		 	
         }
         
         $dom->save(_SITE_APP_DIR.$module.'/Lang/'.$iso.'.xml');
 	}
}
?>