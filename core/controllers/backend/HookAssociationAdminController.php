<?php
namespace core\controllers\backend;
use core\controllers\backend\partial\AssociationController;
use core\constant\ActionCode;
use core\constant\generator\ColumnType;
use core\constant\generator\SearchType;
use core\constant\dao\Operator;
use core\constant\dao\OrderWay;
use core\Tools;
use core\constant\FormPosition;
class HookAssociationAdminController extends AssociationController
{	
	protected $modelClassName = 'HookAssociation';
	protected $targetRequired = true;
	public function __construct()
    {
		parent::__construct();
		$this->formFieldsToExclude = array_merge($this->formFieldsToExclude, array('idHook', 'position', 'idWrapper'));
        $this->restrictedActions[] = ActionCode::VIEW;
		$this->columnsToExclude[] = 'idWrapper';
		$this->addDefaultValues['position'] = 1;
    }
	protected function loadTargetObject(){
		$this->targetObject = $this->getDAOInstance('Hook', false)->getById($this->extraListParams['target']);
		$this->targetField = 'idHook';
		$this->fieldToSet = 'idWrapper';
		$this->associationList[$this->fieldToSet] = null;
		$this->columnsToExclude[] = $this->targetField;
		$this->ajaxFormPosition = FormPosition::LEFT;
	}
	protected function getAddableItems() {
		if($this->addableItems===null){
			$excludes = $this->getDataToExclude();
			$restrictions = array();
			if(!empty($excludes)){
				$restrictions['id'] = array('operator'=>Operator::NOT_IN_LIST, 'value'=>$excludes);
			}
			$this->addableItems = $this->getDAOInstance('Wrapper', false)->getByFields($restrictions);
		}
		return $this->addableItems;
	}
	
	protected function getAssociatedTableLabel(){
		return $this->l('Associations of hook %s');
	}
	protected function getAssociatedFormLabel(){
		return $this->l('Add new associations to hook %s');
	}
	protected function customizeFormFields($update = false) {
		$data = $this->getAddableItems();
		$columns = array('id'=>$this->l('Id'), 'name'=>array('label'=>$this->l('Name'), 'dataType'=>ColumnType::TO_STRING), 'module'=>$this->l('Module'));
		
		$table = $this->generator->createTableCheckboxMultiple($columns, $this->l('There are not any wrapper you can add'));
		$table->setValue($this->getAddableItems());
		$table->setIdentifier($this->selectableIdentifier);
		$this->form->addChild($this->generator->createInputCustomContent($table, $this->selectableIdentifier, $this->l('Wrappers'), false));
	}
	
	protected function customizeColumns() {
		//$this->generator->createColumn($this->table, $label, $name, $dataType= ColumnType::TEXT, $searchType = SearchType::TEXT, $sortable = true, $searchable = true, $searchOptions = array(), $dataOptions = array());
		//$label = $this->isExtraUserParam() ? $this->l('Profiles') : $this->l('Administrators');
		$columnName = $this->generator->createColumn($this->table, $this->l('Target name'), Tools::formatForeignField($this->fieldToSet, 'name'), ColumnType::TEXT, SearchType::TEXT, true, true);
		$columnModule = $this->generator->createColumn($this->table, $this->l('Module'), Tools::formatForeignField($this->fieldToSet, 'module'), ColumnType::TEXT, SearchType::TEXT, true, true);
	}
	protected function beforeEdit($update = false){
		if(!$update){
			$lastBrother = $this->getDAOInstance($this->modelClassName, false)->getByFields(array('idHook' => $this->defaultModel->getIdHook()), false, null, false, false, array(), 0, 1, 'position', OrderWay::DESC);
			$position = empty($lastBrother) ? 1 : (int)$lastBrother[0]->getPosition() + 1;
			$this->defaultModel->setPosition($position);
		}
		return true;
	}
}