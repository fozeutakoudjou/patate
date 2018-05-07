<?php
namespace core\controllers\backend;
class VirtualAdminController extends AdminController
{	
	public function __construct($modelClassName, $module)
    {
		parent::__construct();
		$this->modelClassName = $modelClassName;
		$this->setControllerName();
		$this->setModuleName();
    }
	
	protected function setModuleName()
    {
		$this->moduleName = $this->moduleName;
		$this->isModule = !empty($this->moduleName);
    }
	protected function setControllerName()
    {
		$this->controllerClass = $this->modelClassName;
    }
}