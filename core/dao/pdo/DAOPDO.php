<?php
namespace core\dao\pdo;

use core\constant\dao\Operator;
use core\constant\dao\LogicalOperator;
use core\constant\dao\JoinType;
use core\dao\DAO;
use core\dao\Factory;
use core\dao\DAOImplementation;

class DAOPDO extends DAO implements DAOImplementation{
    const DEFAULT_PREFFIX = 't';
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
	protected static $joinTypeList = array(
		JoinType::INNER => 'INNER JOIN',
		JoinType::LEFT => 'LEFT JOIN',
		JoinType::RIGHT => 'RIGHT JOIN',
		JoinType::RIGHT => 'LEFT JOIN'
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
	
    protected function formatAssociations($fields, $associations){
		$result = array();
		$result['associationsToGet'] = array();
		$result['associationSelect'] = '';
		$result['associationJoin'] = '';
		$result['fields'] = $fields;
		if(isset($this->definition['referenced']) && $this->definition['referenced']){
			$newFields = array();
			foreach ($fields as $field => $value) {
				$values = is_array($value) ? $value : array('value'=>$value);
				$tab = $this->extractForeignField($field);
				if(isset($tab['externalField'])){
					$values['foreign'] = $tab['field'];
					$values['externalField'] = $tab['externalField'];
					$associations[$tab['field']]['join'] = isset($values['join']) ? $values['join'] : JoinType::LEFT;
				}
				$newFields[$field] = $values;
			}
			$result['fields'] = $newFields;
			foreach($associations as $field => $association){
				$reference = $this->definition['fields'][$field]['reference'];
				$module = isset($reference['module']) ? $reference['module'] : '';
				$dao = Factory::getDAOInstance($reference['class'], $module);
				$useOfLang = isset($association['useOfLang']) ? $association['useOfLang'] : $this->useOfLang;
				$dao->setUseOfLang($useOfLang);
				$useOfAllLang = isset($association['useOfAllLang']) ? $association['useOfAllLang'] : $this->useOfAllLang;
				$dao->setUseOfAllLang($useOfAllLang);
				$dao->setDefinition();
				if(!isset($association['get']) || $association['get']){
					$result['associationSelect'] .= ', '.$dao->getSelect($field, false, true);
					$result['associationsToGet'][$field] = array('dao' =>$dao);
				}
				$join = isset($association['join']) ? $association['join'] : JoinType::LEFT;
				$result['associationJoin'] .= ' '.$dao->getTableSelect($field, true, $field, self::DEFAULT_PREFFIX, $join);
			}
		}
		return $result;
	}
    /**
     * getByField object
     *
     * @param array $fields
     * @return array
     */
    public function _getByFields($fields, $returnTotal = false, $associations = array(), $start = 0, $limit = 0,
            $orderBy = OrderBy::PRIMARY, $orderWay = OrderWay::DESC, $logicalOperator = LogicalOperator::AND_) {
		$formatted = $this->formatAssociations($fields, $associations);
        $restriction=$this->getRestrictionFromArray($fields, $logicalOperator);
        $sql = 'SELECT ' . $this->getSelect() . $formatted['associationSelect'] .$this->getTableSelect() . $formatted['associationJoin'] .
		(empty($restriction)?'':' WHERE '.$restriction).
        $this->getOrderString($orderBy, $orderWay) . $this->getLimitString($start, $limit);
		var_dump($sql);
		$query =$this->db->prepare($sql);
		$this->addParamsFromArray($query, $fields);
		$this->addLangParam($query);
        $query->execute();
		$result = $this->getAllAsObjectFromQuery($query, $formatted['associationsToGet']);
		if($returnTotal){
			$total = $this->getByFieldsCount($fields, $logicalOperator);
			$result = array('list' => $result, 'total' => $total);
		}
		if($this->className == 'Language'){
			$dao = Factory::getDAOInstance('Group');
			$dao->setDefinition();
			$sql = $dao->getSelect() . $dao->getTableSelect();
			var_dump($sql);
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
	protected function getTableSelect($preffix = '', $foreign = false, $foreignField = '', $parentPreffix = '', $join='')
    {
		$preffix = empty($preffix) ? self::DEFAULT_PREFFIX : $preffix;
		$sql = '';
		$protectedPreffix = bqSQL($preffix);
		$tableSql = ' `'.bqSQL(_DB_PREFIX_.$this->definition['table']) .'` `'. $protectedPreffix.'` ';
		if($foreign){
			$sql.=' '.self::$joinTypeList[$join].' '.$tableSql.
				' ON (`'. $protectedPreffix.'`.`'. bqSQL($this->definition['primary']) .'` = `'. $parentPreffix.'`.`'. bqSQL($foreignField) .'`)';
		}else{
			$sql.=' FROM '.$tableSql;
		}
		$sql .= $this->getLangJoin($preffix, $foreign);
		return $sql;
    }
	protected function getSelect($preffix = '', $useMultipleSelect = true, $foreign = false)
    {
		$preffix = empty($preffix) ? self::DEFAULT_PREFFIX : $preffix;
		$string = $useMultipleSelect ? bqSQL($preffix).'.*' : $this->formatSelectFields($this->defaultModel->getSimpleFields(), $preffix , $foreign);
		$string .= $this->getLangSelect($preffix, $useMultipleSelect, $foreign);
		return $string;
    }
	
	protected function formatSelectFields($fields, $preffix, $foreign = false)
    {
		$string = '';
		$first = true;
		$protectedPreffix = bqSQL($preffix);
		if($this->defaultModel->isAutoIncrement() && !$foreign){
			$fields[] = $this->definition['primary'];
		}
        foreach ($fields as $field) {
            if ($first) {
				$first = false;
			}else{
				$string.=', ';
			}
			$string .= '`'. $protectedPreffix .'`.`'.bqSQL($field).'` ' .($foreign ? ' AS `'.bqSQL($this->formatForeignField($preffix, $field)).'`' : '');
        }
        return $string;
    }
	
	protected function getLangJoin($preffix, $foreign = false)
    {
		
		$join = ' ';
		if($this->defaultModel->isMultilang() && $this->useOfLang && !is_array($this->definition['primary'])){
			$protectedPreffix = bqSQL($preffix);
			$langPreffix = $protectedPreffix.'_l';
			$join .= ' LEFT JOIN `'.bqSQL(_DB_PREFIX_ . $this->definition['table']).'_lang` `'.$langPreffix.'` ON ((`'.$langPreffix.'`.`'.bqSQL('id_' .$this->definition['table']) .'` = `'.
			$protectedPreffix.'`.`' . $this->definition['primary'] . '`) '. ($this->useOfAllLang ? '' : ' AND (`'.$langPreffix.'`.lang = :lang)').') ';
		}
        return $join;
    }
	protected function getLangSelect($preffix, $useMultipleSelect = true, $foreign = false)
    {
		$sql = ' ';
		if($this->defaultModel->isMultilang() && $this->useOfLang && !is_array($this->definition['primary'])){
			$langFields = $this->defaultModel->getLangFields();
			$langFields[]='lang';
			$sql = ', '.($useMultipleSelect ? bqSQL($preffix.'_l').'.*' : $this->formatSelectFields($langFields, $preffix.'_l', $foreign));
		}
        return $sql;
    }
	protected function addLangParam($query)
    {
		if($this->useOfLang && $this->defaultModel->isMultilang() && !$this->useOfAllLang && !is_array($this->definition['primary'])){
			$query->bindValue(':lang', $this->lang);
		}
    }
    
    protected function getAllAsObjectFromQuery($query, $associationsToGet = array()){
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
			
			if(!is_array($value) || !isset($value['preffix'])){
				$preffix = self::DEFAULT_PREFFIX .($this->defaultModel->isLangField($field) ? '_l' :'');
			}else{
				$preffix = $value['preffix'];
			}
            $condition .= $this->getOperatorQuery($field, $value, $operator, $preffix);
        }
        return $condition;
    }
	
    protected function getOperatorQuery($field, $value, $operator, $preffix) {
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