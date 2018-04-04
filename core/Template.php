<?php
namespace core;

class Template{
    
	protected $fileExtention = 'tpl.php';
	
	protected $data = array();
	
	protected $tools;
	
	protected static $instance;
	
	protected $lastRenderedTpl;
	
	protected function __construct(){
		$this->tools = new TemplateTools();
    }
	
	public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Template();
        }
        return self::$instance;
    }
	
	public function getLastRenderedTpl()
    {
        return $this->lastRenderedTpl;
    }
	
	/**
     * Add parameters in file 
     *
     * @param array|string $param name of parameter or data to add 
     * @param type $value value of parameter
     */
	public function assign($param, $value = null){
		$values = is_array($param) ? $param : array($param => $value);
		foreach($values as $key=> $val){
			$this->data[$key] = $val;
		}
    }
	
	
    public function render($file, $checkPath = true){
		$file = $this->getFileFullName($file);
		$file = $checkPath ? FileTools::getTplFile($file) : $file;
        if(file_exists($file)){
			$this->lastRenderedTpl = $file;
			$this->assign('tools', $this->tools);
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
	
	public function includeTpl($file, $isAbsolutePath = true, $data = array(), $useCurrentData = true, $checkPath = true){
		$initialLastRenderedTpl = $this->lastRenderedTpl;
		$initialData = $this->data;
		if(!$isAbsolutePath){
			$file = FileTools::resolveFilename(dirname($initialLastRenderedTpl).'/'.$file);
		}
		$file = $this->getFileFullName($file);
		$file = $checkPath ? FileTools::getTplFile($file) : $file;
		$data = $useCurrentData ? array_merge($initialData, $data) : $data;
        if(file_exists($file)){
			$data['tools'] = $this->tools;
            extract($data);
			$this->lastRenderedTpl = $file;
			$this->data = $data;
            include $file;
        } else {
            throw new \Exception('Template file "' . $file . '" does not exist');
        }
		$this->lastRenderedTpl = $initialLastRenderedTpl;
		$this->data = $initialData;
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
