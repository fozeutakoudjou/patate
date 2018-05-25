<?php
namespace core\dao;

use core\FileTools;
use core\constant\dao\Operator;
use core\constant\dao\LogicalOperator;
use core\constant\dao\OrderWay;

class DAO{
	
    /** @var Factory factory */
    protected $factory;
	
    protected $module;
    protected $className;
    
    protected $definition;
	
    protected $defaultModel;
    
    protected $requireValidation = true;
	
	protected $isImplementation;
	
	/** @var DAOImplementation implementation */
    protected $implementation;
	
	protected $defaultLang;
    protected $defaultLanguages;
    
    public function __construct($param){
		$this->isImplementation = false;
        $this->factory= $param['factory'];
        $this->module= $param['module'];
        $this->className= $param['className'];
        $this->defaultLang= $param['lang'];
        $this->defaultLanguages= $param['languages'];
		if(isset($param['implementation'])){
			$this->implementation = $param['implementation'];
		}
    }
    
    protected function validation($model){
        if ($this->requireValidation && !$model->isFieldsValidated()) {
            throw new \Exception('Some fields are invalid.');
        }
        $this->requireValidation = true;
    }
    
    protected function setDefinition($model = null){
        if (!isset($this->definition)) {
            if($model===null){
                $model = $this->createModel();
            }
            $this->definition = $model->getDefinition();
			$this->defaultModel = $model;
			
			if(!$this->isImplementation){
				$this->implementation->setDefinition($model);
			}
        }
    }
	
	public function save($model, $saveOfLangField = true, $languages = null) {
		$result = $model->isLoaded() ? $this->update($model, array(), array(), array(), $saveOfLangField, $languages) : $this->add($model, $saveOfLangField, $languages);
		return $result;
	}
    /**
     * Add object
     *
     * @param \models\Model $model
     * @return bool
     */
    public function add($model, $saveOfLangField = true, $languages = null) {
		$languages = ($languages==null)?$this->defaultLanguages : $languages;
		$this->setDefinition($model);
		if (isset($this->definition['fields']['dateAdd'])) {
            $model->setDateAdd(date('Y-m-d H:i:s'));
        }
        if (isset($this->definition['fields']['dateUpdate'])) {
            $model->setDateUpdate(date('Y-m-d H:i:s'));
        }
        $this->validation($model);
        $model->formatFields($languages, $this->defaultLang);
        $result = $this->getImplementation()->_add($model);
        if($result && !is_array($this->definition['primary']) && $model->isAutoIncrement()){
            $model->setPropertyValue($this->definition['primary'], $this->getLastId());
        }
		if($result){
			$result = $this->getImplementation()->saveMultilangFields($model, $saveOfLangField, $languages, false);
		}
        return $result;
    }
	
    /**
     * Update object
     *
     * @param \models\Model $model
     * @param array $identifiers
     * @param array $fieldsToExclude
     * @param array $fieldsToUpdate
     * @return bool
     */
    public function update($model, $fieldsToExclude = array(), $fieldsToUpdate = array(), $identifiers = array(), $saveOfLangField = true, $languages = null) {
		$languages = ($languages==null)?$this->defaultLanguages : $languages;
		$this->setDefinition($model);
		if (isset($this->definition['fields']['dateUpdate'])) {
            $model->setDateUpdate(date('Y-m-d H:i:s'));
        }
        $this->validation($model);
		$model->formatFields($languages, $this->defaultLang);
		$newFieldsToUpdate = array();
		$newLangFields = array();
		foreach ($this->definition['fields'] as $field => $value) {
            if(!in_array($field, $fieldsToExclude) && (empty($fieldsToUpdate) || in_array($field, $fieldsToUpdate))){
                if($model->isLangField($field)){
					$newLangFields[] = $field;
				}else{
					$newFieldsToUpdate[] = $field;
				}
            }
        }
        $result = $this->getImplementation()->_update($model, $newFieldsToUpdate, $identifiers);
		if($result){
			$result = $this->getImplementation()->saveMultilangFields($model, $saveOfLangField, $languages, true, $newLangFields);
		}
        return $result;
    }
    
    /**
     * Delete object
     *
     * @param \models\Model $model
     * @param array $identifiers
     * @return bool
     */
    public function delete($model, $setDeleted = false, $identifiers = array()) {
        $this->setDefinition($model);
		if($setDeleted){
			$this->requireValidation = false;
			$result = $this->changeValue($model, 'deleted', 1);
			$this->requireValidation = true;
			return $result;
		}else{
			return $this->getImplementation()->_delete($model, $identifiers);
		}
    }
    
    /**
     * getByField object
     *
     * @param \models\Model $model
     * @param int|array $id
     * @return Model
     */
    public function getById($id, $onlyActive = false, $lang = null, $useOfLang = true, $useOfAllLang = false, $associations = array(), $excludeDeleted = true) {
        $this->setDefinition();
        if(is_array($this->definition['primary'])){
            $fields = $id;
        }else{
            $fields =array($this->definition['primary']=>$id);
        }
		$fields = $this->addActiveParam($fields, $onlyActive);
        $result = $this->getByFields($fields, false, $lang, $useOfLang, $useOfAllLang, $associations, 0, 0, '', OrderWay::DESC, LogicalOperator::AND_, $excludeDeleted);
        return empty($result)?null:$result[0];
    }
    
    /**
     * getByField object
     *
     * @param array $fields
     * @return array
     */
    public function getAll($returnTotal = false, $lang = null, $useOfLang = true, $useOfAllLang = false, $start = 0, $limit = 0,
            $orderBy = '', $orderWay = OrderWay::DESC, $onlyActive = false, $associations = array(), $excludeDeleted = true) {
        $this->setDefinition();
        $fields = array();
        if($onlyActive && isset($this->definition['fields']['active'])){
            $fields['active'] = true;
        }
        return $this->getByFields($fields, $returnTotal, $lang, $useOfLang, $useOfAllLang, $associations, $start, $limit, $orderBy, $orderWay, LogicalOperator::AND_, $excludeDeleted);
    }
    
    /**
     * getByField object
     *
     * @param array $fields
     * @return array
     */
    public function getByFields($fields, $returnTotal = false, $lang = null, $useOfLang = true, $useOfAllLang = false, $associations = array(), 
		$start = 0, $limit = 0, $orderBy ='', $orderWay = OrderWay::DESC, $logicalOperator = LogicalOperator::AND_, $excludeDeleted = true) {
        $this->setDefinition();
		$fields = $this->addDelectedParam($fields, $excludeDeleted);
        $result = $this->getImplementation()->_getByFields($fields, $returnTotal, $lang, $useOfLang, $useOfAllLang, $associations, $start, $limit, $orderBy, $orderWay, $logicalOperator);
		return $result;
    }
    
    
    /**
     * getByField object
     *
     * @param array $fields
     * @return int
     */
    public function getByFieldsCount($fields, $logicalOperator = LogicalOperator::AND_, $lang = null, $useOfLang = true, $useOfAllLang = false, $excludeDeleted = true) {
        $this->setDefinition();
		$fields = $this->addDelectedParam($fields, $excludeDeleted);
        return $this->getImplementation()->_getByFieldsCount($fields, $logicalOperator, $lang = null, $useOfLang = true, $useOfAllLang = false);
    }
	
	 /**
     * getByField object
     *
     * @param string $field
     * @param type $value
     * @return array
     */
    public function getByField($field, $value, $onlyActive = false, $returnTotal = false, $lang = null, $useOfLang = true, $useOfAllLang = false, 
			$associations = array(), $start = 0, $limit = 0, $orderBy = '', $orderWay = OrderWay::DESC, $operator = Operator::EQUALS, $excludeDeleted = true) {
		$fields = $this->createFieldArray($field, $value, $operator);
		$fields = $this->addActiveParam($fields, $onlyActive);
        return $this->getByFields($fields, $returnTotal, $lang, $useOfLang, $useOfAllLang, $associations, $start, $limit, $orderBy, $orderWay, LogicalOperator::AND_, $excludeDeleted);
    }
    
    public function getByFieldCount($field, $value, $onlyActive = false, $operator = Operator::EQUALS, $lang = null, $useOfLang = true, $useOfAllLang = false, $excludeDeleted = true) {
		$fields = $this->createFieldArray($field, $value, $operator);
		$fields = $this->addActiveParam($fields, $onlyActive);
        return $this->getByFieldsCount($fields, $operator, $lang, $useOfLang, $useOfAllLang, $excludeDeleted);
    }
	
	public function createFieldArray($field, $value, $operator) {
        return array($field => array('value' => $value, 'operator' => $operator));
    }
	
	protected function addDelectedParam($params, $excludeDeleted = true){
		if($excludeDeleted && isset($this->definition['fields']['deleted']) && !isset($params['deleted'])){
			$params['deleted'] = 0;
		}
		return $params;
	}
	
	protected function addActiveParam($params, $onlyActive){
		if($onlyActive && isset($this->definition['fields']['active'])){
            $params['active'] = true;
        }
		return $params;
	}
    
    /**
     * Create object
     *
     * @return \models\Model
     */
    public function createModel($param = array(), $fromDB = false, $lang = '', $useOfAllLang = false, $languages = array(), $preffix = '') {
		$folder = FileTools::getCoreDir($this->module). 'models/';
		$finalClass = FileTools::getClass(FileTools::getNamespaceFromFile($folder.$this->className));
		$file = FileTools::getFileFromNamespace($finalClass).'.php';
		if(file_exists($file)){
			return new $finalClass($param, $fromDB, $lang, $useOfAllLang, $languages, $preffix);
		}else{
			throw new \Exception('Model "'.$this->className.'" does not exist');
		}
    }
    
    public function getLastId(){
        return $this->factory->getLastInsertId();
    }
    
    /**
     * Activate object
     *
     * @param \models\Model $model
     * @return bool
     */
    public function activate($model) {
        return $this->changeActive($model, 1);
    }
    
    /**
     * Desactivate object
     *
     * @param \models\Model $model
     * @return bool
     */
    public function desactivate($model) {
        return $this->changeActive($model, 0);
    }
    
    /**
     * Update object
     *
     * @param \models\Model $model
     * @param bool $active
     * @return bool
     */
    protected function changeActive($model, $active) {
        $this->requireValidation = false;
        $result = $this->changeValue($model, 'active', (int)$active);
		$this->requireValidation = true;
		return $result;
    }
	
	 public function changeValue($model, $field, $value) {
		$this->requireValidation = false;
        $this->setDefinition($model);
		if(isset($this->definition['fields'][$field])){
			$model->setPropertyValue($field, pSQL($value));
            $result = $this->update($model, array(), array($field));
			$this->requireValidation = true;
            return $result;
        }else{
            throw new \Exception('Model must contain field ' . $field);
        }
    }
	
	protected function formatIdentifiers($model, $identifiers) {
        if(empty($identifiers)){
			$identifiers = array();
			$primaries = is_array($this->definition['primary']) ? $this->definition['primary'] : array($this->definition['primary']);
            foreach ($primaries as $field) {
				$identifiers[$field] = $model->getPropertyValue($field);
			}
        }
        return $identifiers;
    }
    
    /**
     * Add object
     *
     * @param Model $model
     * @param string $field
     * @return bool
     */
    protected function canFieldBeSet($model, $field) {
        $canBeSet = true;
        if ($model->isAutoIncrement() && !is_array($this->definition['primary']) && ($this->definition['primary']==$field)) {
            $canBeSet=false;
        }
        return $canBeSet;
    }
	
	public function setDefaultLang($lang)
    {
		$this->defaultLang = $lang;
		if(!$this->isImplementation){
			$this->implementation->setDefaultLang($lang);
		}
    }
	
	public function setDefaultLanguages($languages)
    {
		$this->defaultLanguages = $languages;
		if(!$this->isImplementation){
			$this->implementation->setDefaultLanguages($languages);
		}
    }
	
	/*public function setImplementation()
    {
		if($this instanceof DAOImplementation){
			$this->implementation = $this;
		}else{
			$this->implementation = $this->implementationParam;
		}
    }*/
	
	protected function getImplementation()
    {
		return $this->isImplementation ? $this : $this->implementation;
    }
	
	public function createForeignDAO($field)
    {
		$dao = null;
		$this->setDefinition();
		if(isset($this->definition['fields'][$field]) && isset($this->definition['fields'][$field]['reference'])){
			$dao = $this->getExternaDAO($this->definition['fields'][$field]['reference']);
        }else{
            throw new \Exception('Field "' . $field.'" doest not have any reference');
        }
		return $dao;
    }
	
	protected function getExternaDAO($params)
    {
		return  Factory::getDAOInstance($params['class'], (isset($params['module']) ? $params['module'] : ''));
    }
	
	public function getMultipleAssociatedData($associated, $id, $targetField = null, $idsOnly = true, $useOfLang = false, $lang = null, $restrictions = array(), $associations = array()){
		$this->setDefinition();
		$data = array();
		if(isset($this->definition['multipleAssociateds']) && isset($this->definition['multipleAssociateds'][$associated]) && !is_array($this->definition['primary'])){
			if(!isset($restrictions[$this->definition['primary']])){
				$restrictions[$this->definition['primary']] = $id;
			}
			$data = $this->getExternaDAO($this->definition['multipleAssociateds'][$associated])->getByFields($restrictions, false, $lang, $useOfLang, false, $associations);
		}else{
			throw new \Exception('multiple association "' . $associated.'" doest not have exist or is not available for multiple primary');
		}
		return $data;
	}
	
	public function setAssociatedData($model, $field, $useOfLang = true, $useOfAllLang = false, $lang = null){
		$data = $this->createForeignDAO($field)->getById($model->getSinglePrimaryValue(), false, $lang, $useOfLang, $useOfAllLang, array(), false);
		$model->setAssociated($field, $data);
	}
}