<?php
namespace core\dao\pdo;

use core\Tools;
use core\constant\dao\Operator;
use core\constant\dao\LogicalOperator;
use core\constant\dao\JoinType;
use core\constant\dao\OrderWay;
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
	protected static $orderWayList = array(
		OrderWay::ASC => 'ASC',
		OrderWay::DESC => 'DESC'
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
        $sql = 'INSERT INTO '.'`'._DB_PREFIX_.$this->definition['entity'].'`'.$fieldsString.' VALUES '.$valuesString;
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
        $condition = $this->getRestrictionFromArray($identifiers, LogicalOperator::AND_, '_cond');
        $sql = 'UPDATE '._DB_PREFIX_.$this->definition['entity']. ' '.self::DEFAULT_PREFFIX .' SET '.$fieldsString .' WHERE '.$condition;
        $query =$this->db->prepare($sql);
        foreach ($fieldsToUpdate as $field) {
            if ($this->canFieldBeSet($model, $field)) {
                $this->addModelParam($query, $model, $field);
            }
        }
        $this->addParamsFromArray($query, $identifiers, '_cond');
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
        $condition = $this->getRestrictionFromArray($identifiers, LogicalOperator::AND_, '', false);
        $sql = 'DELETE FROM  '._DB_PREFIX_.$this->definition['entity'].' WHERE '.$condition;
		$query =$this->db->prepare($sql);
        $this->addParamsFromArray($query, $identifiers);
        $result = $query->execute();
        return $result;
    }
	protected function formatRestrictionFields($fields, $associations){
		$result = array('associations' => $associations, 'fields' => array());
		foreach ($fields as $fieldKey => $value) {
			if(isset($value['group']) && $value['group'] && isset($value['fields'])){
				$formatted = $this->formatRestrictionFields($value['fields'], $associations);
				$result['fields'][$fieldKey]['fields'] = $formatted['fields'];
				$result['associations'] = $formatted['associations'];
			}
			$fieldName = isset($value['field']) ? $value['field'] : $fieldKey;
			$values = is_array($value) ? $value : array('value'=>$value);
			$tab = Tools::extractForeignField($fieldName);
			if(isset($tab['externalField'])){
				$values['preffix'] = $tab['field'];
				$values['modelField'] = $tab['externalField'];
				$result['associations'][$tab['field']]['join'] = isset($values['join']) ? $values['join'] : JoinType::LEFT;
				$result['associations'][$tab['field']]['restrictionKey'] = $fieldKey;
			}
			$result['fields'][$fieldKey] = $values;
		}
		return $result;
	}
    protected function formatAssociations($fields, $associations, $lang, $useOfLang, $useOfAllLang, $orderBy=''){
		$result = array();
		$result['associationsToGet'] = array();
		$result['associationsLang'] = array();
		$result['associationSelect'] = '';
		$result['associationJoin'] = '';
		$result['fields'] = $fields;
		if(isset($this->definition['referenced']) && $this->definition['referenced']){
			$result['fields'] = array();
			$orderByManuallyAdded = false;
			if(!empty($orderBy) && !isset($fields[$orderBy])){
				$fields[$orderBy] = null;
				$orderByManuallyAdded = true;
			}
			$formatted = $this->formatRestrictionFields($fields, $associations);
			$result['fields'] = $formatted['fields'];
			$associations = $formatted['associations'];
			foreach($associations as $field => $association){
				$useOfLangTmp = isset($association['useOfLang']) ? $association['useOfLang'] : $useOfLang;
				$useOfAllLangTmp = isset($association['useOfAllLang']) ? $association['useOfAllLang'] : $useOfAllLang;
				$dao = $this->createForeignDAO($field);
				$dao->setDefinition();
				if(isset($association['restrictionKey']) && isset($result['fields'][$association['restrictionKey']]) &&
					$dao->defaultModel->isLangField($result['fields'][$association['restrictionKey']]['modelField'])){
					$result['fields'][$association['restrictionKey']]['preffix'].='_l';
				}
				if(!isset($association['get']) || $association['get']){
					$result['associationSelect'] .= ', '.$dao->getSelect($field, $useOfLangTmp, $useOfAllLangTmp, $field, false, true);
					$result['associationsToGet'][$field] = array('dao' =>$dao, 'useOfAllLang' =>$useOfAllLangTmp, 'useOfLang' =>$useOfLangTmp);
				}
				if($useOfLangTmp && $dao->defaultModel->isMultilang() && !$useOfAllLangTmp){
					$result['associationsLang'][] = $field;
				}
				$join = isset($association['join']) ? $association['join'] : JoinType::LEFT;
				$referenceField = isset($this->definition['fields'][$field]['reference']['field'])?$this->definition['fields'][$field]['reference']['field']:$dao->definition['primary'];
				$result['associationJoin'] .= ' '.$dao->getTableSelect($lang, $useOfLangTmp, $useOfAllLangTmp, $field, true, $field, $referenceField, self::DEFAULT_PREFFIX, $join);
			}
			if(!empty($orderBy) && isset($result['fields'][$orderBy])){
				if(isset($result['fields'][$orderBy]['preffix'])){
					$result['orderByPreffix'] = $result['fields'][$orderBy]['preffix'];
					$result['orderByField'] = $result['fields'][$orderBy]['modelField'];
				}
				if($orderByManuallyAdded){
					unset($result['fields'][$orderBy]);
				}
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
    public function _getByFields($fields, $returnTotal = false, $lang = null, $useOfLang = true, $useOfAllLang = false, $associations = array(), $start = 0, $limit = 0,
            $orderBy = '', $orderWay = OrderWay::DESC, $logicalOperator = LogicalOperator::AND_) {
		$lang = empty($lang)?$this->defaultLang : $lang;
		$formatted = $this->formatAssociations($fields, $associations, $lang, $useOfLang, $useOfAllLang, $orderBy);
        $restriction=$this->getRestrictionFromArray($formatted['fields'], $logicalOperator);
		$sharedSql = $this->getTableSelect($lang, $useOfLang, $useOfAllLang) . $formatted['associationJoin'] . (empty($restriction)?'':' WHERE '.$restriction);
        $sql = 'SELECT ' . $this->getSelect($lang, $useOfLang, $useOfAllLang) . $formatted['associationSelect'] .$sharedSql.
			$this->getOrderString($orderBy, $orderWay, $formatted) . $this->getLimitString($start, $limit);
		$query =$this->db->prepare($sql);
		$this->addParamsFromArray($query, $formatted['fields']);
		$this->addLangParam($query, $lang, $useOfLang, $useOfAllLang, $formatted['associationsLang']);
        $query->execute();
		$result = $this->getAllAsObjectFromQuery($query, $useOfLang, $useOfAllLang, $formatted['associationsToGet']);
		if($returnTotal){
			$params = array('sharedSql'=>$sharedSql, 'formatted'=>$formatted);
			$total = $this->getByFieldsCountFromFormatted($formatted['fields'], $logicalOperator, $lang, $useOfLang, $useOfAllLang, $params);
			$result = array('list' => $result, 'total' => $total);
		}
		return $result;
    }
	
	public function _getByFieldsCount($fields, $logicalOperator = LogicalOperator::AND_, $lang = null, $useOfLang = true, $useOfAllLang = false){
		$lang = empty($lang)?$this->defaultLang : $lang;
		$formatted = $this->formatAssociations($fields, array(), $lang, $useOfLang, $useOfAllLang, '');
        $restriction=$this->getRestrictionFromArray($formatted['fields'], $logicalOperator);
		$sharedSql = $this->getTableSelect($lang, $useOfLang, $useOfAllLang) . $formatted['associationJoin'] . (empty($restriction)?'':' WHERE '.$restriction);
		$params = array('sharedSql'=>$sharedSql, 'formatted'=>$formatted);
		return $this->getByFieldsCountFromFormatted($formatted['fields'], $logicalOperator, $lang, $useOfLang, $useOfAllLang, $params);
	}
	
	protected function getByFieldsCountFromFormatted($fields, $logicalOperator, $lang, $useOfLang, $useOfAllLang, $params){
		$primaries = $this->defaultModel->getPrimaries();
		$first = true;
		$sql = 'SELECT COUNT(DISTINCT ';
		foreach($primaries as $primary){
			if(!$first){
				$sql.=', ';
			}
			$sql.=self::DEFAULT_PREFFIX.'.`'. bqSQL($primary).'`';
			$first = false;
		}
		$sql .= ') AS number '.$params['sharedSql'];
		$query =$this->db->prepare($sql);
        $this->addParamsFromArray($query, $fields);
		$this->addLangParam($query, $lang, $useOfLang, $useOfAllLang, $params['formatted']['associationsLang']);
        $query->execute();
		$data = $query->fetch(\PDO::FETCH_OBJ);
		return (int)$data->number;
	}
	protected function getTableSelect($lang, $useOfLang, $useOfAllLang, $preffix = '', $foreign = false, $foreignField = '', $referenceField = '', $parentPreffix = '', $join='')
    {
		$preffix = empty($preffix) ? self::DEFAULT_PREFFIX : $preffix;
		$sql = '';
		$protectedPreffix = bqSQL($preffix);
		$tableSql = ' `'.bqSQL(_DB_PREFIX_.$this->definition['entity']) .'` `'. $protectedPreffix.'` ';
		if($foreign){
			$sql.=' '.self::$joinTypeList[$join].' '.$tableSql.
				' ON (`'. $protectedPreffix.'`.`'. bqSQL($referenceField) .'` = `'. $parentPreffix.'`.`'. bqSQL($foreignField) .'`)';
		}else{
			$sql.=' FROM '.$tableSql;
		}
		$sql .= $this->getLangJoin($preffix, $lang, $useOfLang, $useOfAllLang, $foreign);
		return $sql;
    }
	protected function getSelect($lang, $useOfLang, $useOfAllLang, $preffix = '', $useMultipleSelect = true, $foreign = false)
    {
		$preffix = empty($preffix) ? self::DEFAULT_PREFFIX : $preffix;
		$string = $useMultipleSelect ? bqSQL($preffix).'.*' : $this->formatSelectFields($this->defaultModel->getSimpleFields(), $preffix , $foreign);
		$string .= $this->getLangSelect($preffix, $lang, $useOfLang, $useOfAllLang, $useMultipleSelect, $foreign);
		return $string;
    }
	
	protected function formatSelectFields($fields, $preffix, $foreign = false, $lang = false)
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
			$string .= '`'. $protectedPreffix .($lang ? '_l':'') .'`.`'.bqSQL($field).'` ' .
				($foreign ? ' AS `'.$protectedPreffix.$field.'`' : '');
        }
        return $string;
    }
	
	protected function getLangJoin($preffix, $lang, $useOfLang, $useOfAllLang, $foreign = false)
    {
		
		$join = ' ';
		if($this->defaultModel->isMultilang() && $useOfLang && !is_array($this->definition['primary'])){
			$protectedPreffix = bqSQL($preffix);
			$langPreffix = $protectedPreffix.'_l';
			$join .= ' LEFT JOIN `'.bqSQL(_DB_PREFIX_ . $this->definition['entity']).'_lang` `'.$langPreffix.'` ON ((`'.$langPreffix.'`.`'.bqSQL('id_' .$this->definition['entity']) .'` = `'.
			$protectedPreffix.'`.`' . $this->definition['primary'] . '`) '. ($useOfAllLang ? '' : ' AND (`'.$langPreffix.'`.lang = :'.$preffix.'lang)').') ';
		}
        return $join;
    }
	protected function getLangSelect($preffix, $lang, $useOfLang, $useOfAllLang, $useMultipleSelect = true, $foreign = false)
    {
		$sql = ' ';
		if($this->defaultModel->isMultilang() && $useOfLang && !is_array($this->definition['primary'])){
			$langFields = $this->defaultModel->getLangFields();
			$langFields[]='lang';
			$sql = ', '.($useMultipleSelect ? bqSQL($preffix.'_l').'.*' : $this->formatSelectFields($langFields, $preffix, $foreign, true));
		}
        return $sql;
    }
	protected function addLangParam($query, $lang, $useOfLang, $useOfAllLang, $others = array())
    {
		if($useOfLang && $this->defaultModel->isMultilang() && !$useOfAllLang && !is_array($this->definition['primary'])){
			$others[] = self::DEFAULT_PREFFIX;
		}
		foreach($others as $preffix){
			$query->bindValue(':'.$preffix.'lang', $lang);
		}
    }
    
    protected function getAllAsObjectFromQuery($query, $useOfLang = true, $useOfAllLang = false, $associationsToGet = array()){
        $result = array();
		$ids = array();
		$i = 0;
		$primary = is_array($this->definition['primary']) ? $this->definition['primary'][0] : $this->definition['primary'];
		$currentDef = array(
			'' => array('dao'=>$this, 'useOfAllLang' =>$useOfAllLang, 'useOfLang' =>$useOfLang)
		);
		$objectsToget = array_merge($currentDef, $associationsToGet);
		while ($data = $query->fetch(\PDO::FETCH_ASSOC)){
			$id = $data[$primary];
			$useOfAllLang = false;
			$increment = false;
			$associateds = array();
			foreach($objectsToget as $preffix => $params){
				if($params['dao']->defaultModel->isMultilang() && $params['useOfAllLang'] && isset($ids[$id])){
					$model = empty($preffix) ? $result[$ids[$id]] : $result[$ids[$id]]->getAssociated($preffix);
					$langFields = $model->getLangFields();
					foreach($langFields as $field){
						$model->setFieldValue($field, $data[$preffix.$field], $data[$preffix.'lang'], $params['useOfAllLang']);
					}
				}else{
					if(!empty($preffix)){
						$data[$preffix.$params['dao']->definition['primary']] = $data[$preffix];
					}
					$lang = ($params['dao']->defaultModel->isMultilang() && $params['useOfLang'] && isset($data[$preffix.'lang'])) ? $data[$preffix.'lang'] : '';
					$model = $params['dao']->createModel($data, true, $lang, $params['useOfAllLang'], array(),$preffix);
					if(empty($preffix)){
						$mainModel = $model;
						$result[] = $model;
						$increment = true;
					}else{
						$mainModel->setAssociated($preffix, $model);
					}
					
				}
				$useOfAllLang = ($useOfAllLang || $params['useOfAllLang']);
			}
			if($increment && $useOfAllLang){
				$ids[$id] = $i;
				$i++;
			}
        }
        $query->closeCursor();
        return $result;
    }
    
    protected function getOrderString($orderBy, $orderWay, $formatted= array()){
		$sql = '';
		if(!empty($orderBy)){
			if(!isset(self::$orderWayList[$orderWay])){
				throw new \Exception('Invalid order way');
			}else{
				if(isset($formatted['orderByPreffix'])){
					$preffix = $formatted['orderByPreffix'];
					$orderBy = $formatted['orderByField'];
				}else{
					$preffix = self::DEFAULT_PREFFIX .($this->defaultModel->isLangField($orderBy) ? '_l' :'');
				}
				$sql = ' ORDER BY `'.bqSQL($preffix).'`.`'.bqSQL($orderBy).'` '.self::$orderWayList[$orderWay].' ';
			}
		}
        return $sql;
    }
    
    protected function getLimitString($start, $limit){
        return ($limit>0) ? ' LIMIT '.(int)$start.', '.(int)$limit : '';
    }
    
    protected function addModelParam($query, $model, $field, $lang = false) {
        $value = $model->getPropertyValue($field);
		$value = is_array($value) ? $value[$lang] : $value;
        $query->bindParam(':'.$field, $value);
    }
    
    protected function getRestrictionFromArray($params, $logicalOperator = LogicalOperator::AND_, $valueSuffix = '', $usePrefix = true) {
		$condition = '';
        $first = true;
        foreach ($params as $fieldKey => $value) {
			if ($first) {
				$first = false;
			}else{
				$condition.=' ' .(isset(self::$logicalOperatorList[$logicalOperator]) ? self::$logicalOperatorList[$logicalOperator] : 'AND').' ';
			}
			if(isset($value['group']) && $value['group'] && isset($value['fields'])){
				$logicalOperatorTmp = isset($value['logicalOperator']) ? $value['logicalOperator'] : LogicalOperator::AND_;
				$condition.= '('.$this->getRestrictionFromArray($value['fields'], $logicalOperatorTmp, $fieldKey.$valueSuffix, $usePrefix).')';
			}else{
				$fieldName = isset($value['field']) ? $value['field'] : $fieldKey;
				$operator = (is_array($value) && isset($value['operator'])) ? $value['operator'] : Operator::EQUALS;
				if(!is_array($value) || !isset($value['preffix'])){
					$preffix = self::DEFAULT_PREFFIX .($this->defaultModel->isLangField($fieldName) ? '_l' :'');
				}else{
					$preffix = $value['preffix'];
				}
				$modelField = is_array($value) && isset($value['modelField']) ? $value['modelField'] : $fieldName;
				$condition .= $this->getOperatorQuery($fieldKey, $value, $operator, $preffix, $modelField, $valueSuffix, $usePrefix);
			}
        }
        return $condition;
    }
	
    protected function getOperatorQuery($fieldKey, $value, $operator, $preffix, $modelField, $valueSuffix, $usePrefix) {
		$sql = '(';
		$values = is_array($value) ? $value['value'] : $value;
		$protectedPreffix = bqSQL($preffix);
		if($values===null){
			$sql .= ($usePrefix ? '`'.$protectedPreffix.'`.' : '').'`'.bqSQL($modelField).'` IS NULL';
		}elseif(isset(self::$operatorList[$operator])){
			$formatter = is_array(self::$operatorList[$operator]) ? self::$operatorList[$operator]['field'] : self::$operatorList[$operator];
			$sql .= sprintf($formatter, ($usePrefix ? '`'.$protectedPreffix.'`.' : '').'`'.bqSQL($modelField).'`', ':'.$fieldKey.$valueSuffix);
		}elseif($operator == Operator::BETWEEN){
			$i = 1;
			$values = is_array($values) ? $values : array('' => $values);
			foreach($values as $key => $val){
				$sql .= ($i==1) ? ('`'.$protectedPreffix.'`.`'.bqSQL($modelField).'`'.' BETWEEN :' .$fieldKey . $key.$valueSuffix) : (' AND :' . $fieldKey . $key.$valueSuffix);
				$i++;
				if($i==3){
					break;
				}
			}
		}elseif(($operator == Operator::IN_LIST)||($operator == Operator::NOT_IN_LIST)){
			$sql .= ($usePrefix ? '`'.$protectedPreffix.'`.' : '').'`'.bqSQL($modelField).'` '.(($operator == Operator::IN_LIST) ? 'IN' : 'NOT IN').'(';
			$values = is_array($values) ? $values : array('' => $values);
			
			$sql .= Tools::joinArray($values, function($arrayKey, $arrayValue, $params){
				return ':' .$params['fieldKey'] . $arrayKey.$params['valueSuffix'];
			}, ',', array('fieldKey' => $fieldKey, 'valueSuffix' => $valueSuffix));
			$sql .=')';
		}
		$sql .= ')';
		return $sql;
    }
	
	protected  function addParamsFromArray($query, $params, $valueSuffix='') {
		$tmpValues = array();
        foreach ($params as $fieldKey => $value) {
			if(isset($value['group']) && $value['group'] && isset($value['fields'])){
				$this->addParamsFromArray($query, $value['fields'], $fieldKey.$valueSuffix);
			}else{
				$values = is_array($value) ? $value['value'] : $value;
				$operator = (is_array($value) && isset($value['operator'])) ? $value['operator'] : Operator::EQUALS;
				if($values!==null){
					$formatter = (isset(self::$operatorList[$operator]) && is_array(self::$operatorList[$operator])) ? self::$operatorList[$operator]['value'] : '';
					$values = is_array($values) ? $values : array('' => $values);
					foreach($values as $key => $val){
						$formattedValue = empty($formatter) ? $val : sprintf($formatter, $val);
						$tmpValues[$fieldKey][$key] = $formattedValue;
						$query->bindParam(':'.$fieldKey.$key.$valueSuffix, $tmpValues[$fieldKey][$key]);
					}
				}
			}
		}
    }
    
	public function saveMultilangFields($model, $saveOfLangField, $languages, $update = false, $fieldsToUpdate = array())
    {
		$result = true;
		$langFields = ($update) ? $fieldsToUpdate : $model->getLangFields();
		if($saveOfLangField && $model->isMultilang() && !is_array($this->definition['primary']) && !empty($langFields)){
			$idObject = $model->getPropertyValue($this->definition['primary']);
			if($update){
				$sqlInit = $this->getLangUpdateSqlInit($langFields);
			}else{
				$addSqlInit= $this->getLangAddSqlInit($langFields);
			}
			foreach ($languages as $lang => $langObject){
				if($update && $this->isObjectSavedForLang($idObject, $lang)){
					$sql = $sqlInit;
				}else{
					if(!isset($addSqlInit)){
						$addSqlInit = $this->getLangAddSqlInit($langFields);
					}
					$sql = $addSqlInit;
				}
				$query=$this->db->prepare($sql);
				$query->bindParam(':id_' . $this->definition['entity'], $idObject);
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
    	$sqlInit='INSERT INTO ' . _DB_PREFIX_ . $this->definition['entity']. '_lang (' . 'id_'.$this->definition['entity'] . ', lang, ' .
      	implode(',', $langFields) . ') VALUES(:id_' . $this->definition['entity'] . ', :lang, :' . implode(',:', $langFields).')';
      	return $sqlInit;
    }
    
    public function getLangUpdateSqlInit($langFields)
    {
    	$sqlInit= 'UPDATE '._DB_PREFIX_.$this->definition['entity'].'_lang SET ';
    	$first= true;
    	foreach ($langFields as $field){
    		if(!$first){
    			$sqlInit.=', ';
    		}
    		$sqlInit.=$field.' = :'.$field;
    		$first = false;
    	}
    	$sqlInit.=' WHERE (id_'.$this->definition['entity'] . ' = :id_' . $this->definition['entity'] . ') AND (lang = :lang)';
    	return $sqlInit;
    }
    
    public function isObjectSavedForLang($idObject, $lang)
    {
    	$sql = 'SELECT COUNT(*) AS number FROM '._DB_PREFIX_.$this->definition['entity'].
    	'_lang WHERE (id_'.$this->definition['entity'].' = :idObject) AND (lang = :lang)';
    	$query=$this->db->prepare($sql);
    	$query->bindParam(':idObject', $idObject);
    	$query->bindParam(':lang', $lang);
    	$query->execute();
    	$data = $query->fetch(\PDO::FETCH_OBJ);
    	return ((int)$data->number > 0);
    }
    
}