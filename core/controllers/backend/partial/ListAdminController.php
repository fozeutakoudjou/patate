<?php
namespace core\controllers\backend\partial;
use core\models\Model;
use core\constant\generator\ColumnType;
use core\constant\generator\SearchType;
use core\constant\ActionCode;
use core\generator\html\interfaces\AccesChecker;
use core\generator\html\interfaces\UrlCreator;

abstract class ListAdminController extends BaseAdminController implements AccesChecker, UrlCreator
{
	protected $table;
	
	protected $columnsToExclude = array('dateAdd', 'dateUpdate', 'deleted');
	
	protected $defaultOrderWay;
	protected $orderWay;
	
	protected $defaultOrderColumn;
	protected $orderColumn;
	
	protected $defaultItemsPerPage =20;
	protected $itemsPerPage;
	
	protected $itemsPerPageOptions;
	protected $currentPage;
	
	 public function __construct()
    {
		parent::__construct();
		$boolOptions = array(''=>'--', '1'=>$this->l('Yes'), '0'=>$this->l('No'));
		$this->generator->setSearchOptions(SearchType::SELECT, $boolOptions);
		$this->generator->setSearchButtonText($this->l('Search'));
		$this->generator->setResetButtonText($this->l('Reset'));
    }
	
	protected function createTable()
    {
		$this->table = $this->generator->createTable($this->l($this->modelClassName.'s'));
		$this->table->setIdentifier($this->modelIdentifier);
		$this->table->setUrlCreator($this);
		$this->customizeTable();
	}
	
	protected function customizeTable() {}
	
	protected function createTableActions() {
		$addLink = $this->createUrl(array('action'=>ActionCode::ADD));
		$this->generator->createTableAction($this->table, $this->l('Add'), $addLink, 'plus', $this->l('Add'), true, ActionCode::ADD, ActionCode::ADD);
	}
	
	protected function createRowsActions() {
		foreach($this->availableActions as $action => $data){
			if(isset($data['row'])){
				$label = isset($data['row']) ? $data['label'] : '';
				$icon = isset($data['icon']) ? $data['icon'] : '';
				$title = isset($data['title']) ? $data['title'] : $label;
				$default = isset($data['default']) ? $data['default'] : false;
				$useOfButtonStyle = $default ? true : false;
				$this->generator->createRowAction($this->table, $label, $href = '', $icon, $title, $useOfButtonStyle, $action, $action, array(), $default);
			}
		}
	}
	
	
	protected function createColumns()
    {
		$primaries = is_array($this->modelDefinition['primary'])?$this->modelDefinition['primary'] : array($this->modelDefinition['primary']);
		foreach($primaries as $field){
			if(!isset($this->modelDefinition['fields'][$field]) && !in_array($field, $this->columnsToExclude)){
				$this->generator->createColumn($this->table, $this->l($field), $field, ColumnType::TEXT, SearchType::TEXT, true, true);
			}
		}
		
		foreach($this->modelDefinition['fields'] as $field => $fieldDefinition){
			if(!in_array($field, $this->columnsToExclude)){
				$this->generator->createColumn($this->table, $this->l($field), $field, self::getColumnType($fieldDefinition['type'], $field), self::getSearchType($fieldDefinition['type'], $field), true, true);
			}
		}
		$this->customizeColumns();
	}
	
	protected function customizeColumns() {}
	protected function getBaseRestrictionFields() {
		$fields = array();
		return $fields;
	}
	protected function getListData() {
		$fields = $this->getBaseRestrictionFields();
		$data = $this->getDAOInstance()->getByFields($fields, true);
		return $data;
	}
	
	protected function formatListData($data) {
		return $data;
	}
	
	protected static function getColumnType($modelType, $field)
    {
		$type = ColumnType::TEXT;
		if($field=='active'){
			$type = ColumnType::ACTIVE;
		}elseif($modelType==Model::TYPE_BOOL){
			$type = ColumnType::BOOL;
		}elseif($modelType==Model::TYPE_DATE){
			$type = ColumnType::DATE;
		}
		return $type;
	}
	
	protected static function getSearchType($modelType, $field)
    {
		$type = SearchType::TEXT;
		if($modelType==Model::TYPE_BOOL){
			$type = SearchType::SELECT;
		}elseif($modelType==Model::TYPE_DATE){
			$type = SearchType::DATE;
		}
		return $type;
	}
}