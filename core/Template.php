<?php
namespace core\controllers;

class Template{
    
	protected $fileExtention = 'tpl.php';
	
	protected $data = array();
	
	/**
     * Add parameters in file 
     *
     * @param array|string $param name of parameter or data to add 
     * @param type $value value of parameter
     */
	public function assign($param, $value = null){
		$values = is_array($param) ? $param ? array($param => $value);
		foreach($values as $key=> $val){
			$this->data[$key] = $val;
		}
    }
	
	
    public function render($file){
		$file = $this->getFileFullName($file);
        if(file_exists($file))){
            extract($this->data);
            ob_start();
            include $file;
            $buffer = ob_get_contents();
            @ob_end_clean();
            return $buffer;
        } else {
            throw new \Exception('Template file "' . $file . '" does not exist');
        }
    }
	
	public function getFileExtention(){
		return $this->fileExtention;
	}
	
	public function exist($file){
		return file_exists($this->getFileFullName($file));
	}
	
	public function getFileFullName($file){
		return $file.'.'.$this->fileExtention;
	}
}
