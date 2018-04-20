<?php
namespace core\controllers\backend;

use core\controllers\backend\partial\FormAdminController;

abstract class AdminController extends FormAdminController
{	
	protected function processAdd(){
		$this->createForm();
		$this->createFormFields();
		$data = $this->formatFormData($this->getFormData());
		$this->form->setValue($data);
		$this->processResult['content'] = $this->form->generate();
	}
	
	protected function processList(){
		$this->createTable();
		$this->createColumns();
		$this->createTableActions();
		$this->createRowsActions();
		$data = $this->formatListData($this->getListData());
		$this->table->setTotalResult($data['total']);
		$this->table->setValue($data['list']);
		$this->processResult['content'] = $this->table->generate();
	}
}