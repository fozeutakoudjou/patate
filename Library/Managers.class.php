<?php
namespace Library;
/**
 * Description of Managers
 *
 * @author FFOZEU
 */
class Managers {
    
    protected $api = null;
    protected $dao = null;
    protected $managers = array();
    
    public function __construct($api, $dao){
        $this->api = $api;
        $this->dao = $dao;
    }
    /**
     * permet d'instancié un le manager d'un module
     * @param type $module
     * @param type $module_reference
     * @return type
     * @throws \InvalidArgumentException 
     */
    public function getManagerOf($module, $module_reference=null){
        
        if (!is_string($module) || empty($module)){
            throw new \InvalidArgumentException('Le module spécifié est invalide');
        }
        if (!isset($this->managers[$module])){
            if(!empty($module_reference)){
                //$manager = 'Applications\\'.$module_reference.'\\Models\\'.$module.'Manager_'.$this->api;
                $module = $module_reference;
            }
            $manager = '\\Applications\\Modules\\'.$module.'\\Models\\'.$module.'Manager_'.$this->api;
            $this->managers[$module] = new $manager($this->dao);
        }
        
        return $this->managers[$module];
    }
}

?>
