<?php
namespace core\dao\pdo;

use core\constant\dao\Operator;
use core\constant\dao\LogicalOperator;
use core\dao\DAO;
use core\dao\DAOImplementation;

class DAOPDO extends DAO implements DAOImplementation{
    
    /** @var \PDO Database connection */
    protected $db;
    protected static $operatorList = array(
		Operator::EQUALS => '%s = %s',
		Operator::DIFFERENT => '%s <> %s',
		Operator::CONTAINS => array('field' => '%s LIKE %s', 'value' => '%%%s%%'),
		Operator::START_WITH => array('field' => '%s LIKE %s', 'value' => '%s%%'),
		Operator::END_WITH => array('field' => '%s LIKE %s', 'value' => '%%%s'),
	);
	protected static $logicalOperatorList = array(
		LogicalOperator::AND_ => 'AND',
		LogicalOperator::OR_ => 'OR'
	);
    public function __construct($param){
        parent::__construct($param);
        $this->db = $param['db'];
		$this->isImplementation = true;
		$this->implementation = null;
    }
     
    /**
     * Add object
     *
     * @param \models\Model $model
     * @return bool
     */
    public function _add($model) {
		$fieldsString ='(';
        $valuesString ='(';
        $first = true;
        foreach ($this->definition['fields'] as $field => $value) {
            $canFieldBeSet = $this->canFieldBeSet($model, $field);
            if($canFieldBeSet && !$model->isLangField($field)){
                if ($first) {
                    $first = false;
                }else{
                    $fieldsString.=', ';
                    $valuesString.=', ';
                }
                $fieldsString.='`'.$field.'`';
                $valuesString.=':'.$field;
            }
        }
        $fieldsString.=')';
        $valuesString.=');';
        $sql = 'INSERT INTO '.'`'._DB_PREFIX_.$this->definition['table'].'`'.$fieldsString.' VALUES '.$valuesString;
        $query =$this->db->prepare($sql);  
        foreach ($this->definition['fields'] as $field => $value) {
            if ($this->canFieldBeSet($model, $field) && !$model->isLangField($field)) {
                $this->addModelParam($query, $model, $field);
            }
        }
		var_dump($sql);
        $result = $query->execute();
		return $result;
    }
    
    /**
     * Update object
     *
     * @param \models\Model $model
     * @param array $identifiers
     * @param array $fieldsToUpdate
     * @return bool
     */
    public function _update($model, $fieldsToUpdate = array(), $identifiers = array()) {
        $fieldsString ='';
        $first = true;
        foreach ($fieldsToUpdate as $field) {
            if($this->canFieldBeSet($model, $field)){
                if ($first) {
                    $first = false;
                }else{
                    $fieldsString.=', ';
                }
                $fieldsString.=$field.' = :'.$field;
            }
        }
        $identifiers = $this->formatIdentifiers($model, $identifiers);
        $condition = $this->getRestrictionFromArray($identifiers);
        $sql = 'UPDATE '._DB_PREFIX_.$this->definition['table'].' SET '.$fieldsString .' WHERE '.$condition;
        $query =$this->db->prepare($sql);
        foreach ($fieldsToUpdate as $field) {
            if ($this->canFieldBeSet($model, $field)) {
                $this->addModelParam($query, $model, $field);
            }
        }
        $this->addParamsFromArray($query, $identifiers);
        $result = $query->execute();
        return $result;
    }
    
    /**
     * Delete object
     *
     * @param \models\Model $model
     * @param array $identifiers
     * @return bool
     */
    public function _delete($model, $identifiers = array()) {
        $identifiers = $this->formatIdentifiers($model, $identifiers);
        $condition = $this->getRestrictionFromArray($identifiers);
        $sql = 'DELETE FROM  '._DB_PREFIX_.$this->definition['table'].' WHERE '.$condition;
        $query =$this->db->prepare($sql);
        $this->addParamsFromArray($query, $identifiers);
        $result = $query->execute();
        return $result;
    }
    
    /**
     * getByField object
     *
     * @param array $fields
     * @return array
     */
    public function _getByFields($fields, $returnTotal = false, $start = 0, $limit = 0,
            $orderBy = OrderBy::PRIMARY, $orderWay = OrderWay::DESC, $logicalOperator = LogicalOperator::AND_) {
        $restriction=$this->getRestrictionFromArray($fields, $logicalOperator);
        $sql = 'SELECT t.*' . $this->getLangSelect() .' FROM  '._DB_PREFIX_.$this->definition['table']. ' t' . $this->getLangJoin() .
		(empty($restriction)?'':' WHERE '.$restriction).
        $this->getOrderString($orderBy, $orderWay) . $this->getLimitString($start, $limit);
		$query =$this->db->prepare($sql);
		$this->addParamsFromArray($query, $fields);
		$this->addLangParam($query);
        $query->execute();
        $result = $this->getAllAsObjectFromQuery($query);
		if($returnTotal){
			$total = $this->getByFieldsCount($fields, $logicalOperator);
			$result = array('list' => $result, 'total' => $total);
		}
		return $result;
    }
	
	public function _getByFieldsCount($fields, $logicalOperator = LogicalOperator::AND_){
		$restriction=$this->getRestrictionFromArray($fields, $logicalOperator);
		$sql = 'SELECT COUNT(*) AS number FROM  '._DB_PREFIX_.$this->definition['table']. ' t' . (empty($restriction)?'':' WHERE '.$restriction);
        $query =$this->db->prepare($sql);
		$this->addParamsFromArray($query, $fields);
        $query->execute();
		$data = $query->fetch(\PDO::FETCH_OBJ);
    	return (int)$data->number;
	}
	
	public function getLangJoin()
    {
		$join = ' ';
		if($this->defaultModel->isMultilang() && $this->useOfLang && !is_array($this->definition['primary'])){
			$join .= ' LEFT JOIN ' . _DB_PREFIX_ . $this->definition['table'].'_lang tl ON (tl.id_' .$this->definition['table'] .
			' = t.' . $this->definition['primary'] . ') '. ($this->useOfAllLang ? '' : ' AND (tl.lang = :lang) ');
		}
        return $join;
    }
	public function getLangSelect()
    {
		$sql = ' ';
		if($this->defaultModel->isMultilang() && $this->useOfLang && !is_array($this->definition['primary'])){
			$sql .= ', tl.* ';
		}
        return $sql;
    }
	public function addLangParam($query)
    {
		if($this->useOfLang && $this->defaultModel->isMultilang() && !$this->useOfAllLang && !is_array($this->definition['primary'])){
			$query->bindValue(':lang', $this->lang);
		}
    }
    
    protected function getAllAsObjectFromQuery($query){
        $result = array();
		$ids = array();
		$i = 0;
		$primary = is_array($this->definition['primary']) ? $this->definition['primary'][0] : $this->definition['primary'];
        while ($data = $query->fetch(\PDO::FETCH_ASSOC)){
			$id = $data[$primary];
			if($this->useOfAllLang && isset($ids[$id])){
				$langFields = $result[$ids[$id]]->getLangFields();
				foreach($langFields as $field){
					$result[$ids[$id]]->setFieldValue($field, $data[$field], $data['lang'], $this->useOfAllLang);
				}
			}else{
				$lang = ($this->defaultModel->isMultilang() && $this->useOfLang && isset($data['lang'])) ? $data['lang'] : '';
				$result[] = $this->createModel($data, true, $lang, $this->useOfAllLang);
				if($this->useOfAllLang){
					$ids[$id] = $i;
					$i++;
				}
			}
        }
        $query->closeCursor();
        return $result;
    }
    
    protected function getOrderString($orderBy, $orderWay){
        return '';
    }
    
    protected function getLimitString($start, $limit){
        return '';
    }
    
    protected function addModelParam($query, $model, $field, $lang = false) {
        $value = $model->getPropertyValue($field);
		$value = is_array($value) ? $value[$lang] : $value;
        $query->bindParam(':'.$field, $value);
    }
    
    protected function getRestrictionFromArray($params, $logicalOperator = LogicalOperator::AND_) {
        $condition = '';
        $first = true;
        foreach ($params as $field => $value) {
			$operator = (is_array($value) && isset($value['operator'])) ? $value['operator'] : Operator::EQUALS;
            if ($first) {
                $first = false;
            }else{
                $condition.=' ' .(isset(self::$logicalOperatorList[$logicalOperator]) ? self::$logicalOperatorList[$logicalOperator] : 'AND').' ';
            }
            $condition .= $this->getOperatorQuery($field, $value, $operator);
        }
        return $condition;
    }
	
    protected function getOperatorQuery($field, $value, $operator) {
		$sql = '(';
		if(isset(self::$operatorList[$operator])){
			$formatter = is_array(self::$operatorList[$operator]) ? self::$operatorList[$operator]['field'] : self::$operatorList[$operator];
			$sql .= sprintf($formatter, $field, ':'.$field);
		}elseif($operator == Operator::BETWEEN){
			$i = 1;
			$values = is_array($value) ? $value['value'] : $value;
			$values = is_array($values) ? $values : array('' => $values);
			foreach($values as $key => $val){
				$sql .= ($i==1) ? $field.' BETWEEN :' .$field . $key : ' AND :' . $field . $key;
				$i++;
				if($i==3){
					break;
				}
			}
		}
		$sql .= ')';
		return $sql;
    }
	
	protected  function addParamsFromArray($query, $params) {
		$tmpValues = array();
        foreach ($params as $field => $value) {
			$values = is_array($value) ? $value['value'] : $value;
			$operator = (is_array($value) && isset($value['operator'])) ? $value['operator'] : Operator::EQUALS;
			$formatter = is_array(self::$operatorList[$operator]) ? self::$operatorList[$operator]['value'] : '';
			$values = is_array($values) ? $values : array('' => $values);
			foreach($values as $key => $val){
				$formattedValue = empty($formatter) ? $val : sprintf($formatter, $val);
				$tmpValues[$field][$key] = $formattedValue;
				$query->bindParam(':'.$field.$key, $tmpValues[$field][$key]);
			}
		}
    }
    
	public function saveMultilangFields($model, $update = false, $fieldsToUpdate = array())
    {
		$result = true;
		$langFields = ($update) ? $fieldsToUpdate : $model->getLangFields();
		if($this->saveOfLangField && $model->isMultilang() && !is_array($this->definition['primary']) && !empty($langFields)){
			$idObject = $model->getPropertyValue($this->definition['primary']);
			if($update){
				$sqlInit = $this->getLangUpdateSqlInit($langFields);
			}else{
				$addSqlInit= $this->getLangAddSqlInit($langFields);
			}
			foreach ($this->languages as $lang => $langObject){
				if($update && $this->isObjectSavedForLang($idObject, $lang)){
					$sql = $sqlInit;
				}else{
					if(!isset($addSqlInit)){
						$addSqlInit = $this->getLangAddSqlInit($langFields);
					}
					$sql = $addSqlInit;
				}
				$query=$this->db->prepare($sql);
				$query->bindParam(':id_' . $this->definition['table'], $idObject);
				$query->bindParam(':lang', $lang);
				foreach ($langFields as $field){
					if (property_exists($model, $field)){
						$this->addModelParam($query, $model, $field, $lang);
					} 
				}
				$result = ($result && (bool)$query->execute());
			}
		}
		return $result;
    }
    
    public function getLangAddSqlInit($langFields)
    {
    	$sqlInit='INSERT INTO ' . _DB_PREFIX_ . $this->definition['table']. '_lang (' . 'id_'.$this->definition['table'] . ', lang, ' .
      	implode(',', $langFields) . ') VALUES(:id_' . $this->definition['table'] . ', :lang, :' . implode(',:', $langFields).')';
      	return $sqlInit;
    }
    
    public function getLangUpdateSqlInit($langFields)
    {
    	$sqlInit= 'UPDATE '._DB_PREFIX_.$this->definition['table'].'_lang SET ';
    	$first= true;
    	foreach ($langFields as $field){
    		if(!$first){
    			$sqlInit.=', ';
    		}
    		$sqlInit.=$field.' = :'.$field;
    		$first = false;
    	}
    	$sqlInit.=' WHERE (id_'.$this->definition['table'] . ' = :id_' . $this->definition['table'] . ') AND (lang = :lang)';
    	return $sqlInit;
    }
    
    public function isObjectSavedForLang($idObject, $lang)
    {
    	$sql = 'SELECT COUNT(*) AS number FROM '._DB_PREFIX_.$this->definition['table'].
    	'_lang WHERE (id_'.$this->definition['table'].' = :idObject) AND (lang = :lang)';
    	$query=$this->db->prepare($sql);
    	$query->bindParam(':idObject', $idObject);
    	$query->bindParam(':lang', $lang);
    	$query->execute();
    	$data = $query->fetch(\PDO::FETCH_OBJ);
    	return ((int)$data->number > 0);
    }
    
}