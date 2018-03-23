<?php
namespace core;

class TemplateTools{
	
	public function include($file){
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
