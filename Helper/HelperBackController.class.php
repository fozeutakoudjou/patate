<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Helper;
/**
 * Description of HelperBackController
 *
 * @author ffozeu
 */
if( !defined('IN') ) die('Hacking Attempt');
Use Library\Tools;

class HelperBackController extends HelperController{
    //put your code here
    protected $name ='';
    
    protected function init(){
        $this->page->addVar('tools', new Tools());
        parent::init();
        $bread = array('icone' => 'home', 'link' => 'index.html', 'title' => $this->l('Home'));
        $this->app->breadcrumb()->addBreadcrumb(array('home' => $bread));
    }

   
    /**
     * Génération de l'arbre des catégories et sous catégories
     * @param type $idParent
     * @return type 
     */
    public function getArbreCategories($search=''){
        $manager = $this->managers->getManagerOf('Categories');        
        $dataList = $manager->getCategories(1, $search);        
        foreach ($dataList as $value) {
            $all_parents_with_direct_sons[$value->getIdParent()][] = $value->getIdFils();
			$item_name_array[$value->getIdFils()] = $value;
        }        
        return !empty($dataList)?$this->getRecursiveItem($all_parents_with_direct_sons,$item_name_array,0):array();
    }
    
    public function getArbreUserCategories($idUser){
        $manager = $this->managers->getManagerOf('Categories');        
        $dataList = $manager->getUserCategories($idUser);        
        foreach ($dataList as $value) {
            $all_parents_with_direct_sons[$value->getIdParent()][] = $value->getIdFils();
			$item_name_array[$value->getIdFils()] = $value;
        }        
        return !empty($dataList)?$this->getRecursiveItem($all_parents_with_direct_sons,$item_name_array,0):array();
    }
    
    public function getArbreSubCategories($idParent){
       $manager = $this->managers->getManagerOf('Categories');        
       $dataList = $manager->getListeFilsByIdParent($idParent);
       $data = array();
        foreach ($dataList as $value) {
            $all_parents_with_direct_sons[$value->getIdParent()][] = $value->getIdFils();
			$item_name_array[$value->getIdFils()] = $value;
        }
         return !empty($dataList)?$this->getRecursiveItem($all_parents_with_direct_sons,$item_name_array, $idParent):array();
    }
    
     public function getArbreSubCategories2($idParent){
       $manager = $this->managers->getManagerOf('Categories');        
       $dataList = $manager->getListeFilsByIdParent2($idParent);
       $data = array();
        foreach ($dataList as $value) {
            $all_parents_with_direct_sons[$value->getIdParent()][] = $value->getIdFils();
			$item_name_array[$value->getIdFils()] = $value;
        }
         return !empty($dataList)?$this->getRecursiveItem($all_parents_with_direct_sons,$item_name_array, $idParent):array();
    }
    
    /**
     * traite de façon recursive un tableau
     * @param type $parent_item
     * @param type $item_name_array
     * @param type $this_parent
     * @param type $output
     * @return type 
     */
    public function getRecursiveItem(&$parent_item,&$item_name_array, $this_parent, &$output=array()){        
		if (!empty($parent_item[$this_parent])) {
			foreach($parent_item[$this_parent] as $this_item) {
                $output[$this_item]=$item_name_array[$this_item];				
				if (!empty($parent_item[$this_item])) {
					$this->getRecursiveItem($parent_item, $item_name_array, $this_item, $output);
				}				
			}
		}        
		return $output;
    }
    /**
     * gestion de la pagination en fonction du module
     * @param type $module
     * @param type $number
     * @param type $current_page
     * @param type $nber_per_page 
     */
    public function pagination($module,$number=16,$current_page=1,$nber_per_page=16){
        if($number > $nber_per_page){
            $nberPage = ceil($number/$nber_per_page);
            $pagination['current_page'] = $current_page;
            $pagination['nberPage'] = $nberPage;
            $this->page->addVar('pagination', $pagination);
        }
    }
    
    
    /**
     * Génération de l'arbre des catégories et sous catégories
     * @param type $idParent
     * @return type 
     */
    public function getTree(){
        $manager = $this->managers->getManagerOf('Categories');        
        $dataList = $manager->findAll2();        
        foreach ($dataList as $value) {
            $all_parents_with_direct_sons[$value->getIdParent()][] = $value->getIdFils();
			$item_name_array[$value->getIdFils()] = $value;
        }        
        //var_dump($item_name_array);
        //var_dump($all_parents_with_direct_sons);
         return !empty($dataList)?$this->getRecursiveItem($all_parents_with_direct_sons,$item_name_array,0):array();
    }
	
	/*
     * @param $dir for directory
     * @return $file array of all name inside directory exect Menu 
     * 
     */
    public function getDir($dir){
    	$dh  = opendir($dir);
    	while (($filename = readdir($dh)) ) {
    		if(is_dir($dir."/".$filename) && $filename!=="." && $filename!="..")
			$files[] = $filename;
    	}
    	sort($files);
    	return $files;
    	
    }
    
    /**
     * This function look recusively for  Menu which  have child
     * @param unknown $id
     * @param unknown $name_array
     * @param unknown $output
     * @return Menu information which Child
     */
    
	  public  function getFilsMenuRecusive($link,&$id,&$name_array, &$output=array()){
	    	foreach ($name_array as $value){
	    		if((strcasecmp($value->getType_link(),$link)==0) && !(strcasecmp($value->getId(),$id)==0) && strcasecmp($value->getParent(),$id)==0 ){
	    			$output[$value->getTitre()]=$value->getTitre();
	    			$output[$value->getLien()]=$value->getLien();
	    			$output[$value->getLogo()]=$value->getLogo();
	 				$this->getFilsMenuRecusive($link,$value->getId(),$name_array,$output);
	 					
	    		}
	    		
	    	}
	    	return $output;
	    }
       /**
        * This function take record after record and determin if  the curent record have child
        *  
        */
	    public function getMenu($typeLink){
	    	$manager = $this->managers->getManagerOf('Menu');
	    	$dataList = $manager->findAll2();
	    	$module=array();
	    	if(strcasecmp($typeLink,"B")==0 ) $link=0;
	    	else $link=1;
	    	foreach ($dataList as $value){
	    		if(strcasecmp($value->getType_link(),$link)==0){
	    				$temp=$this->getFilsMenuRecusive($link,$value->getId(),$dataList,$ouput=array());
	    				if(!empty($temp)){
	    					$module[]["titre"]=$value->getTitre();
	    					$module[]["lien"]=$value->getLien();
	    					$module[]["logo"]=$value->getLogo();
	    					$module[]["fils"]=$temp;
	    				}else{
	    					if(strcasecmp($value->getParent(),"NULL")==0){
	    						$module[]["titre"]=$value->getTitre();
	    						$module[]["lien"]=$value->getLien();
	    						$module[]["logo"]=$value->getLogo();
	    						$module[]["fils"]="";
	    						
	    					}
	    				}			
	    			}
	    		}
	    	
	    	//var_dump($module);die();
	    }
	    
	    /**
	     * This function First addTable For Non charge Table
	     * Afer We look for Route.xml for all other file
	     * we determin if it's  possible to built information.  
	     */
	    
	    public function  LoadAutomaticMenu($link){
	    	$fields=array();
	    	$fields2=array();
	    	$data=array();
	    	$str="enable";
	    	if(strcasecmp($link,"B")==0 ){
	    		$temp=0;
	    		$list_Module_default=array("Errors","Index","Lang","Traduction");
	    	}
	    	else{
	    		$temp=1;
	    		$list_Module_default=array("ConfigSMTP","Configurations","MailsFormat","Errors","Index","Lang","Menu","Traduction");
	    	 }
	    	$manager = $this->managers->getManagerOf('Menu');
	    	$datalist=$this->getDir(_MODULES_DIR_);
	    	foreach ($datalist as $module){
	    		if(!(in_array($module,$list_Module_default))){
	    			//var_dump($module);
			    	$path = _MODULES_DIR_.$module.'\\Config\\';
			        $inc=0;
			        $path = _MODULES_DIR_.$module.'\\Config\\'.$link;
			    	$filename =$path.$module.'Route.xml';
			    	if(file_exists($filename)){
			    		//var_dump($filename);
			    		$xml = simplexml_load_file($filename);
			    		$param = $xml;
			    		//Ici on parcours le fichier pars� et on ne retien que les routes ayant le champ visible=enable
			    		foreach ($param as $key => $value) {
			    			foreach($value->attributes() as $key0 => $value0){
			    				if(strcasecmp($key0,"url")==0 || strcasecmp($key0,"action")==0 || strcasecmp($key0,"module")==0 )
			    					$fields[$key0]=$value0;
			    				if(strcasecmp($key0,"visible")==0 ){
			    						if(strcasecmp($value0,"enable")==0 ){
			    							$fields2[$inc]=$fields;
			    							$inc+=1;	
			    						}
			    				}	
			    			}
			    			unset($fields);
			    		}
			    		$data[$module]=$fields2;
			    		unset($fields2);
			    	}
			    	
	    		}		
	     }
	      //var_dump($data);die();
	     $manager->buitl_table_in_memory($data,$temp);
	    
	  }
	  
	/*  public function genererMenuFB(){
	  	
	  	
	  }*/
	    
}

?>
