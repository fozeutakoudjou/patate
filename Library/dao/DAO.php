<?php
namespace dao;


use constant\dao\Operator;
use constant\dao\LogicalOperator;
use constant\dao\OrderWay;
use constant\dao\OrderBy;

abstract class DAO{
    
    /** @var DbFactory Database connection */
    protected $factory;
    
    protected $definition;
    
    protected $requireValidation = true;
	protected $lang;
    protected $languages;
    protected $useOfLang = true;
    protected $useOfAllLang = false;
    protected $saveOfLangField = true;
    
    public function __construct($param){
        $this->factory= $param['factory'];
    }
    
    protected function validation($model){
        if ($this->requireValidation && !$model->isFieldsValidated()) {
            throw new \Exception('Some fields are invalid.');
        }
        $this->requireValidation = true;
    }
    
    protected function setDefinition($model = null){
        if (!isset($this->definition)) {
            if($model===nul){
                $model = $this->createModel();
            }
            $this->definition = $model->getDefinition();
        }
    }
	
	abstract protected function saveMultilangFields($model, $update = false);
     
    /**
     * Add object
     *
     * @param \models\Model $model
     * @return bool
     */
    public function add($model) {
        $this->validation($model);
        $model->formatFields();
        $this->setDefinition($model);
        $result = $this->_add($model);
        if($result && !is_array($this->definition['primary']) && 
                isset($this->definition['auto_increment']) && $this->definition['auto_increment']){
            $methode = 'set'.ucfirst($this->definition['primary']);
            $model->$methode($this->getLastId());
        }
		if($result){
			$result = $this->saveMultilangFields($model, false);
		}
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
    public function update($model, $identifiers = array(), $fieldsToExclude = array(), $fieldsToUpdate = array()) {
        $this->validation($model);
        $model->formatFields();
        $this->setDefinition($model);
        $result = $this->_update($model, $identifiers, $fieldsToExclude, $fieldsToUpdate);
		if($result){
			$result = $this->saveMultilangFields($model, true);
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
    abstract protected function _update($model, $identifiers = array(), $fieldsToExclude = array(), $fieldsToUpdate = array());
    
    /**
     * Delete object
     *
     * @param \models\Model $model
     * @param array $identifiers
     * @return bool
     */
    public function delete($model, $identifiers = array()) {
        $this->setDefinition($model);
        return $this->_delete($model, $identifiers);
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
            $orderBy = OrderBy::PRIMARY, $orderWay = OrderWay::DESC, $logicalOperator = LogicalOperator::AND_, $operator = Operator::EQUAL) {
        $this->setDefinition();
        return $this->_getByFields($fields, $returnTotal, $start, $limit, $orderBy, $orderWay, $logicalOperator);
    }
    
    
    /**
     * getByField object
     *
     * @param array $fields
     * @return int
     */
    public function getByFieldsCount($fields, $logicalOperator = LogicalOperator::AND_, $operator = Operator::EQUAL) {
        $this->setDefinition();
        return $this->_getByFieldsCount($fields, $logicalOperator, $operator);
    }
    
    /**
     * getByField object
     *
     * @param \models\Model $model
     * @param array $fields
     * @return array
     */
    abstract protected function _getByFields($fields, $returnTotal = false, $start = 0, $limit = 0,
            $orderBy = OrderBy::PRIMARY, $orderWay = OrderWay::DESC, $operator = Operator::EQUAL, $logicalOperator = LogicalOperator::AND_);
    
    
    abstract protected function _getByFieldsCount($fields, $operator = Operator::EQUAL, $logicalOperator = LogicalOperator::AND_);
    
    /**
     * getByField object
     *
     * @param string $field
     * @param type $value
     * @return array
     */
    public function getByField($field, $value, $returnTotal = false, $start = 0, $limit = 0,
            $orderBy = OrderBy::PRIMARY, $orderWay = OrderWay::DESC, $operator = Operator::EQUAL) {
        return $this->getByFields(array($field => $value),$returnTotal, $start, $limit, $orderBy, $orderWay, $operator);
    }
    
    public function getByFieldCount($field, $value, $operator = Operator::EQUAL) {
        return $this->getByFieldsCount(array($field => $value), $operator);
    }
    
    /**
     * Create object
     *
     * @return \models\Model
     */
    public function createModel($params = array()) {
        
    }
    
    public function getLastId(){
        return $this->dbFactory->getLastInsertId();
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
        $this->setDefinition($model);
        $this->requireValidation = false;
        if(isset($this->definition['fields']['active'])){
            $result = $this->update($model,array(),array(),array('active' => (int)$active));
            if ($result) {
                $model->setActive((int)$active);
            }
            return $result;
        }else{
            throw new \Exception('Model must contain field active');
        }
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
        if (isset($this->definition['auto_increment']) && $this->definition['auto_increment'] &&
                !is_array($this->definition['primary']) && ($this->definition['primary']==$field)) {
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