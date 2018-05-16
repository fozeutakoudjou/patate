<?php
namespace core\controllers\backend;

class PageNotFoundAdminController extends AdminController
{	
	public function __construct()
    {
		parent::__construct();
		$this->defaultAction = '404';
		$this->action = $this->defaultAction;
		$this->availableActions[$this->defaultAction] = null;
	}
	
	public function checkUserAccess($action, $idWrapper = null)
    {
        return true;
    }
	
	protected function process404()
    {
		$this->processResult['content'] = $this->renderTpl('page_not_found', false);
    }
}