<?php
namespace core\controllers\backend;

use core\generator\html\HtmlGenerator;

class UserAdminController extends AdminController
{	
	protected function processList(){
		$generator = new HtmlGenerator($this->l('Submit'), $this->l('Back'), $this->formLanguages, $this->lang);
		
		$table = $generator->createTable($this->l('Users'), 'user');
		$this->processResult['content'] = $table->generate();
	}
}