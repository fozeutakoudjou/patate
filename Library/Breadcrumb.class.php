<?php
namespace Library;

/**
 * Description of Breadcrumb
 *
 * @author fozeu wife
 */
class Breadcrumb extends ApplicationComponent{
    //put your code here
    
    public $tree = array();
    
    public function addbreadcrumb(array $data){
        if(is_array($data))
            foreach ($data as $key => $value) {
                $this->tree[$key] = $value;
            }
    }
    
    public function getBreadcrumb(){
        return $this->tree;
    }
}

?>
