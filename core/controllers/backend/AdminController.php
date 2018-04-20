<?php
namespace core\controllers\backend;
use core\Tools;
use core\controllers\backend\partial\FormAdminController;

abstract class AdminController extends FormAdminController
{	
	protected function processAdd(){
		$submitted = Tools::isSubmit($this->createFormAction());
		if($submitted){
			$this->retrieveSubmittedData(true);
			$validateErrors = $this->validateFormData(true);
			if($this->defaultModel->isValid() && empty($this->errors) && empty($validateErrors)){
				$result = $this->defaultModel->add();
				$this->redirectLink = $this->createLink(array());
			}
		}
		if(!$this->redirectAfter){
			$this->createForm(false);
			$this->createFormFields();
			$data = $this->formatFormData($this->getFormData($submitted, false), $submitted, false);
			$this->form->setValue($data);
			$this->processResult['content'] = $this->form->generate();
		}
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