<?php
namespace core\controllers\backend;
use core\Tools;
use core\constant\generator\ColumnType;
use core\constant\generator\SearchType;
use core\constant\dao\Operator;
class AdminMenuAdminController extends AdminController
{	
	protected $modelClassName = 'AdminMenu';
	public function __construct()
    {
		parent::__construct();
        $this->columnsToExclude = array_merge($this->columnsToExclude, array('link', 'level', 'title', 'linkType', 'iconClass'));
        $this->formFieldsToExclude = array_merge($this->formFieldsToExclude, array('linkType', 'link', 'level'));
        $this->saveFieldsToExclude = array_merge($this->saveFieldsToExclude, array('linkType', 'link'));
		/*$this->addDefaultValues['type'] = UserType::FRONT_USER;
		$this->genderOptions = array(GenderOption::MALE => $this->l('Male'), GenderOption::FEMALE => $this->l('Female'));*/
    }
	protected function customizeColumns() {
		$actionOptions = $this->createOptions('Action', '', array('dependentOnId'=>0), true);
		$this->changeColumnOptions('idAction', ColumnType::OPTION, SearchType::SELECT, $actionOptions, $actionOptions);
		$parentOptions = $this->createOptions($this->modelClassName, '', array(), true);
		$this->changeColumnOptions('idParent', ColumnType::OPTION, SearchType::SELECT, $parentOptions, $parentOptions);
		/*$field = Tools::formatForeignField('idWrapper', 'name');
		$this->generator->createColumn($this->table, $field, $field, ColumnType::TEXT, SearchType::TEXT, true, true);
		$this->associationList['idWrapper'] = array();*/
	}
	
	protected function createFieldByDefinition($fieldDefinition, $field)
    {
		if($field == 'idWrapper'){
			$input = $this->generator->createSelect($field, $this->l($field), $this->createOptions('Wrapper'));
		}elseif($field == 'idAction'){
			$input = $this->generator->createSelect($field, $this->l($field), $this->createOptions('Action', '', array('dependentOnId'=>0)));
		}elseif($field == 'idParent'){
			$restrictions = $this->defaultModel->isLoaded() ? array('id'=>array('value'=>$this->defaultModel->getId(), 'operator'=>Operator::DIFFERENT)):array();
			$input = $this->generator->createSelect($field, $this->l($field), $this->createOptions($this->modelClassName, '', $restrictions, true));
		}else{
			$input = parent::createFieldByDefinition($fieldDefinition, $field);
		}
		return $input;
	}
	
	protected function beforeEdit($update = false){
		$idParent = $this->defaultModel->getIdParent();
		$return = true;
		if(empty($idParent)){
			$this->defaultModel->setLevel(1);
		}else{
			$parent = $this->getDAOInstance($this->modelClassName, false)->getById($idParent, false, null, false, false);
			if($parent==null){
				$return = false;
				$this->formErrors['idParent'] = $this->l('This menu doest not exist');
			}else{
				$this->defaultModel->setLevel((int)$parent->getLevel()+1);
			}
		}
		return $return;
	}
}