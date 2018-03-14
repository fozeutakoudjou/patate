<?php
namespace Library\dao;


use Library\constant\dao\Operator;
use Library\constant\dao\LogicalOperator;
use Library\constant\dao\OrderWay;
use Library\constant\dao\OrderBy;

abstract class DAO{
    
    /** @var Factory Database connection */
    protected $factory;
	
    protected $module;
    protected $className;
    
    protected $definition;
	
    protected $defaultModel;
    
    protected $requireValidation = true;
	protected $lang;
    protected $languages;
    protected $useOfLang = true;
    protected $useOfAllLang = false;
    protected $saveOfLangField = true;
    protected $implementation;
    
    public function __construct($param){
        $this->factory= $param['factory'];
        $this->module= $param['module'];
        $this->className= $param['className'];
        $this->lang= $param['lang'];
        $this->languages= $param['languages'];
    }
    
    protected function validation($model){
        if ($this->requireValidation && !empty($model->isFieldsValidated())) {
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
        }
    }
	
	abstract protected function saveMultilangFields($model, $update = false, $fieldsToUpdate = array());
     
    /**
     * Add object
     *
     * @param \models\Model $model
     * @return bool
     */
    public function add($model) {
		$this->setDefinition($model);
		if (isset($this->definition['fields']['dateAdd'])) {
            $model->setDateAdd(date('Y-m-d H:i:s'));
        }
        if (isset($this->definition['fields']['dateUpdate'])) {
            $model->setDateUpdate(date('Y-m-d H:i:s'));
        }
        $this->validation($model);
        $model->formatFields($this->languages, $this->lang);
        $result = $this->_add($model);
        if($result && !is_array($this->definition['primary']) && $model->isAutoIncrement()){
            $model->setPropertyValue($this->definition['primary'], $this->getLastId());
        }
		if($result){
			$result = $this->saveMultilangFields($model, false);
		}
		$this->saveOfLangField = true;
        return $result;
    }
    
    /**
     * Add object
     *
     * @param \models\Model $model
     * @return bool
     */
    abstract protected function _add($model);
    
    /**
     * Update object
     *
     * @param \models\Model $model
     * @param array $identifiers
     * @param array $fieldsToExclude
     * @param array $fieldsToUpdate
     * @return bool
     */
    public function update($model, $fieldsToExclude = array(), $fieldsToUpdate = array(), $identifiers = array()) {
		$this->setDefinition($model);
		if (isset($this->definition['fields']['dateUpdate'])) {
            $model->setDateUpdate(date('Y-m-d H:i:s'));
        }
        $this->validation($model);
        $model->formatFields($this->languages, $this->lang);
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
        $result = $this->_update($model, $newFieldsToUpdate, $identifiers);
		if($result){
			$result = $this->saveMultilangFields($model, true, $newLangFields);
		}
		$this->saveOfLangField = true;
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
    abstract protected function _update($model, $fieldsToUpdate = array(), $identifiers = array());
    
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
			return $this->_delete($model, $identifiers);
		}
    }
    
    /**
     * Delete object
     *
     * @param \models\Model $model
     * @param array $identifiers
     * @return bool
     */
    abstract protected function _delete($model, $identifiers = array());
    
    /**
     * getByField object
     *
     * @param \models\Model $model
     * @param int|array $id
     * @return Model
     */
    public function getById($id) {
        $this->setDefinition();
        if(is_array($this->definition['primary'])){
            $fields = $id;
        }else{
            $fields =array($this->definition['primary']=>$id);
        }
        $result = $this->getByFields($fields);
        return empty($result)?null:$result[0];
    }
    
    /**
     * getByField object
     *
     * @param array $fields
     * @return array
     */
    public function getAll($returnTotal = false, $start = 0, $limit = 0,
            $orderBy = OrderBy::PRIMARY, $orderWay = OrderWay::DESC, $onlyActive = false) {
        $this->setDefinition();
        $fields = array();
        if($onlyActive && isset($this->definition['fields']['active'])){
            $fields['active'] = true;
        }
        return $this->getByFields($fields, $returnTotal, $start, $limit, $orderBy, $orderWay);
    }
    
    /**
     * getByField object
     *
     * @param array $fields
     * @return array
     */
    public function getByFields($fields, $returnTotal = false, $start = 0, $limit = 0,
            $orderBy = OrderBy::PRIMARY, $orderWay = OrderWay::DESC, $logicalOperator = LogicalOperator::AND_) {
        $this->setDefinition();
		$fields = $this->addDelectedParam($fields);
        $result = $this->_getByFields($fields, $returnTotal, $start, $limit, $orderBy, $orderWay, $logicalOperator);
		$this->useOfLang = true;
		$this->useOfAllLang = true;
		return $result;
    }
    
    
    /**
     * getByField object
     *
     * @param array $fields
     * @return int
     */
    public function getByFieldsCount($fields, $logicalOperator = LogicalOperator::AND_) {
        $this->setDefinition();
		$fields = $this->addDelectedParam($fields);
        return $this->_getByFieldsCount($fields, $logicalOperator);
    }
	
	protected function addDelectedParam($params){
		if(isset($this->definition['fields']['deleted'])){
			$params['deleted'] = 0;
		}
		return $params;
	}
    
    /**
     * getByField object
     *
     * @param \models\Model $model
     * @param array $fields
     * @return array
     */
    abstract protected function _getByFields($fields, $returnTotal = false, $start = 0, $limit = 0,
            $orderBy = OrderBy::PRIMARY, $orderWay = OrderWay::DESC, $logicalOperator = LogicalOperator::AND_);
    
    
    abstract protected function _getByFieldsCount($fields, $logicalOperator = LogicalOperator::AND_);
    
    /**
     * getByField object
     *
     * @param string $field
     * @param type $value
     * @return array
     */
    public function getByField($field, $value, $returnTotal = false, $start = 0, $limit = 0,
            $orderBy = OrderBy::PRIMARY, $orderWay = OrderWay::DESC, $operator = Operator::EQUALS) {
        return $this->getByFields($this->createFieldArray($field, $value, $operator), $returnTotal, $start, $limit, $orderBy, $orderWay);
    }
    
    public function getByFieldCount($field, $value, $operator = Operator::EQUALS) {
        return $this->getByFieldsCount($this->createFieldArray($field, $value, $operator), $operator);
    }
	
	public function createFieldArray($field, $value, $operator) {
        return array($field => array('value' => $value, 'operator' => $operator));
    }
    
    /**
     * Create object
     *
     * @return \models\Model
     */
    public function createModel($param = array(), $fromDB = false, $lang = '', $useOfAllLang = false) {
        $folder = empty($this->module) ? _SITE_LIBRARY_DIR : _SITE_MOD_DIR . $this->module . '/';
		$folder.='models/';
		if(file_exists($folder . $this->className . '.php')){
			$namespace = str_replace(_SITE_ROOT_DIR_ . '/', '', $folder);
			$namespace = str_replace('/', '\\', $namespace);
			$finalClass = $namespace.$this->className;
			return new $finalClass($param, $fromDB, $lang, $useOfAllLang);
		}else{
			throw new \Exception('Model file does not exist');
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
        $result = $this->changeValue($model, 'active', $active);
		$this->requireValidation = true;
		return $result;
    }
	
	 protected function changeValue($model, $field, $value) {
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
	
	public function setUseOfAllLang($useOfAllLang)
    {
		$this->useOfAllLang = $useOfAllLang;
    }
	
	public function setLang($lang)
    {
		$this->lang = $lang;
    }
	
	public function setUseOfLang($useOfLang)
    {
		$this->useOfLang = $useOfLang;
    }
	
	public function setLanguages($languages)
    {
		$this->languages = $languages;
    }
	
	public function setSaveOfLangField($saveOfLangField)
    {
		$this->saveOfLangField = $saveOfLangField;
    }
}