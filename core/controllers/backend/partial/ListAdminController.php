<?php
namespace core\controllers\backend\partial;
use core\Tools;
use core\StringTools;
use core\models\Model;
use core\constant\dao\OrderWay;
use core\constant\generator\ColumnType;
use core\constant\generator\SearchType;
use core\constant\ActionCode;
use core\constant\UrlParamType;
use core\constant\Separator;
use core\generator\html\interfaces\AccesChecker;
use core\generator\html\interfaces\UrlCreator;

abstract class ListAdminController extends BaseAdminController implements AccesChecker, UrlCreator
{
	protected $table;
	
	protected $columnsToExclude = array('dateAdd', 'dateUpdate', 'deleted', 'idProposer', 'additionalInfos', 'email', 'avatar', 'firstName', 'preferredLang', 'gender', 'type', 'balance');
	
	protected $defaultOrderWay = OrderWay::DESC;
	protected $orderWay;
	
	protected $defaultOrderColumn;
	protected $orderColumn;
	
	protected $defaultItemsPerPage =2;
	protected $itemsPerPage;
	
	protected $itemsPerPageOptions;
	protected $currentPage = 1;
	
	protected $orderWayParams = array(OrderWay::ASC=>'asc', OrderWay::DESC=>'desc');
	protected $limitParams = array(0=>'all');
	
	public function init()
    {
		parent::init();
		$boolOptions = array(''=>'--', '1'=>$this->l('Yes'), '0'=>$this->l('No'));
		$this->generator->setSearchOptions(SearchType::SELECT, $boolOptions);
		$this->generator->setSearchButtonText($this->l('Search'));
		$this->generator->setResetButtonText($this->l('Reset'));
		$this->generator->setSelectAllText($this->l('Select all'));
		$this->generator->setUnselectAllText($this->l('Unselect all'));
		$this->generator->setEmptyRowText($this->l('No records found'));
		$this->generator->setBulkActionText($this->l('Bulk actions'));
		$this->itemsPerPageOptions = array('20'=>20, '2'=>2, '50'=>50, '100'=>100, '300'=>300, '1000'=>1000, '0'=>$this->l('All'));
		if($this->defaultModel!=null){
			$this->defaultOrderColumn =$this->defaultModel->getPrimaries()[0];
		}
    }
	
	protected function createTable()
    {
		$this->table = $this->generator->createTable($this->l($this->modelClassName.'s'));
		$this->table->setIdentifier($this->modelIdentifier);
		$this->table->setUrlCreator($this);
		$this->table->setItemsPerPageOptions($this->itemsPerPageOptions);
		$this->table->setCurrentPage($this->currentPage);
		$this->table->setItemsPerPage(($this->itemsPerPage === null)?$this->defaultItemsPerPage : $this->itemsPerPage);
		$this->table->setOrderColumn(empty($this->orderColumn) ? $this->defaultOrderColumn : $this->orderColumn);
		$this->table->setOrderWay(($this->orderWay === null)? $this->defaultOrderWay : $this->orderWay);
		$this->customizeTable();
	}
	
	protected function customizeTable() {}
	
	protected function createTableActions() {
		$addLink = $this->createUrl(array('action'=>ActionCode::ADD));
		$this->generator->createTableAction($this->table, $this->l('Add new'), $addLink, 'plus', $this->l('Add new'), true, ActionCode::ADD, ActionCode::ADD);
	}
	
	protected function createBulkActions() {
		$addLink = $this->createUrl(array('action'=>ActionCode::ADD));
		$this->generator->createBulkAction($this->table, $this->l('Add new'), $addLink, 'plus', $this->l('Add new'), true, ActionCode::ADD, ActionCode::ADD);
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
	
	public function createSortUrl($column, $way){
		return $this->createListUrl(array(UrlParamType::ORDER=>$column, 'way'=>$way));
	}
	public function createLimitUrl($limit){
		return $this->createListUrl(array(UrlParamType::LIMIT=>$limit));
	}
	
	public function createPaginationUrl($page){
		return $this->createListUrl(array(UrlParamType::PAGE=>$page));
	}
	
	public function createListUrl($data){
		$params = array('action'=>ActionCode::LISTING);
		if(isset($data[UrlParamType::ORDER])){
			$params = $this->addOrderParam($params, $data[UrlParamType::ORDER], $data['way']);
		}elseif(isset($data[UrlParamType::LIMIT])){
			$params = $this->addLimitParam($params, $data[UrlParamType::LIMIT]);
		}elseif(isset($data[UrlParamType::PAGE])){
			$params = $this->addPageParam($params, $data[UrlParamType::PAGE]);
		}
		return $this->createUrl($params);
	}
	
	public function addOrderParam($params, $column, $way){
		$way = isset($this->orderWayParams[$way])?$this->orderWayParams[$way] : $way;
		$params['param1'] = UrlParamType::ORDER.Separator::URL_VALUE.$way.Separator::URL_VALUE.
			StringTools::toUnderscoreCase(Separator::URL_VALUE.$column,Separator::COLUMN_CAMEL_CASE);
		return $params;
	}
	
	public function addLimitParam($params, $limit){
		$str = UrlParamType::LIMIT.Separator::URL_VALUE.(isset($this->limitParams[$limit])?$this->limitParams[$limit] : $limit);
		if(!empty($this->orderColumn)){
			$params = $this->addOrderParam($params, $this->orderColumn, $this->orderWay);
			$params['param2'] = $str;
		}else{
			$params['param1'] = $str;
		}
		return $params;
	}
	
	public function addPageParam($params, $page){
		$str = UrlParamType::PAGE.Separator::URL_VALUE.$page;
		if($this->itemsPerPage!==null){
			$params = $this->addLimitParam($params, $this->itemsPerPage);
			$params['param3'] = $str;
		}elseif(!empty($this->orderColumn)){
			$params = $this->addOrderParam($params, $this->orderColumn, $this->orderWay);
			$params['param2'] = $str;
		}else{
			$params['param1'] = $str;
		}
		return $params;
	}
	
	public function retrieveListUrlParam(){
		$param1 = Tools::getValue('param1');
		if($param1){
			//$data
		}
		
	}
}