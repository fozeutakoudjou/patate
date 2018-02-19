<?php
namespace Library;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Record
 *
 * @author FFOZEU
 */
abstract class Record implements \ArrayAccess{
    
    protected $erreurs = array();
    protected $id;
    public $tabAttrib = array();
    protected $tabType = array('name'=>'is_numeric','name_2'=>'html');

    public function __construct(array $donnees = array(), $fromDB = false){
        if (!empty($donnees)){
            if(!$fromDB)
                $this->hydrate($donnees);
            else
                $this->hydrate2($donnees); 
        }
    }
    
    public function isNew(){
        return empty($this->id);
    }
    
    public function erreurs(){
        return $this->erreurs;
    }
    
    public function id(){
        return $this->id;
    }
    
    public function setId($id){
        $this->id = (int) $id;
    }
    
    public function hydrate(array $donnees){
        $tools = new Tools();
        foreach ($donnees as $attribut => $valeur){
            $methode = 'set'.ucfirst($attribut);
            if (is_callable(array($this, $methode))){
                $html = (array_key_exists($attribut,$this->tabType)&& $this->tabType[$attribut]=='html')?true:false;
                $this->$methode($this->escape($valeur, $tools,$html));                
            }
            $this->tabAttrib[$attribut] = $valeur;
        }
    }
    
    public function hydrate2(array $donnees){
        $tools = new Tools();
        foreach ($donnees as $attribut => $valeur){
            $methode = 'set'.ucfirst($attribut);
            if (is_callable(array($this, $methode))){
                $html = (array_key_exists($attribut,$this->tabType)&& $this->tabType[$attribut]=='html')?true:false;
                $this->$methode($this->escape2($valeur, $tools,$html));                
            }
            $this->tabAttrib[$attribut] = $this->escape2($valeur, $tools,$html);
        }
    }
    
    public function offsetGet($var){
        if (isset($this->$var) && is_callable(array($this, $var))){
            return $this->$var();
        }
    }
    
    public function offsetSet($var, $value){
        $method = 'set'.ucfirst($var);
        if (isset($this->$var) && is_callable(array($this, $method))){
            $this->$method($value);
        }
    }
    
    public function offsetExists($var){
        return isset($this->$var) && is_callable(array($this,$var));
    }
    /**
     *  suppression d'une ligne non existante
     * @param type $var
     * @throws \Exception
     */
    public function offsetUnset($var){

        throw new \Exception('Impossible de supprimer une quelconque valeur');
    }
    /**
     * set content attrib who not exist
     * @param type $key
     * @param type $value
     */
    public function __set($key,$value=null){
        if (!is_array($value))
            $this->tabAttrib[(string)$key] = (string)$value;
    }
    /**
     * get content attrib who not exit
     * @param type $key
     * @return type
     */
    public function __get($key){
        return isset($this->tabAttrib[$key]) ? stripslashes($this->tabAttrib[$key]) : false;
    }
    /**
     * isset attrib who not exit
     * @param type $key
     * @return type
     */
    public function __isset($key) {
        return isset($this->tabAttrib[$key]);
    }
    /**
     * unset attrib who not exit
     * @param type $key
     */
    public function __unset($key) {
        unset($this->tabAttrib[$key]);
    }

    public function __call($name, $arguments=null) {
        $prefix = substr($name, 0,3);
        $key = lcfirst(substr($name, 3, strlen($name)-3));
        if($prefix=='get'){
            return $this->__get($key);
        }elseif($prefix=='set') {
            $this->__set($key,$arguments[0]);
        }else{
            return FALSE;
        }
    }
    /**
     * 
     * @param type $string
     * @param type $html_ok
     * @return type
     */
    public function escape($string, $tools, $html = false)
	{
		if (_MAGIC_QUOTES_GPC_ && !is_array($string))
			$string = stripslashes($string);
		if (!is_numeric($string) && !is_array($string))
		{
			if (!$html){
                $string = $this->_escape($string);
				$string = strip_tags($tools->nl2br($string));
            }
		}

		return $string;
	}
    
    // pour les données venant de la base de données
    public function escape2($string, $tools, $html = false)
	{
		if (_MAGIC_QUOTES_GPC_ && !is_array($string))
			$string = stripslashes($string);
		if (!is_numeric($string) && !is_array($string))
		{
			if (!$html){
                $string = $this->_escape2($string);
				$string = strip_tags($tools->nl2br($string));				
                //$string = stripslashes($string);
            }
		}

		return $string;
	}
    /**
     * escape string before sql
     * @param type $str
     * @return type
     */
    private function _escape($str)
	{
		$search = array("\\", "\0", "\n", "\r", "\x1a", "'", '"');
		$replace = array("\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"');		
		return str_replace($search, $replace, $str);
	}
    
    private function _escape2($str)
	{
		$replace = array("\\", "\0", "\n", "\r", "\x1a", "'", '"');
		$search = array("\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"');		
		return str_replace($search, $replace, $str);
	}
}

?>
