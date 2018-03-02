<?php
namespace dao\pdo;


use dao\DAO;

abstract class DAOPDO extends DAO{
    
    /** @var \PDO Database connection */
    protected $db;
    
    public function __construct($param){
        parent::__construct($param);
        $this->db = $param['db'];
    }
     
    /**
     * Add object
     *
     * @param \models\Model $model
     * @return bool
     */
    protected function _add($model) {
        $fieldsString ='(';
        $valuesString ='(';
        $first = true;
        foreach ($this->definition['fields'] as $key => $value) {
            $canFieldBeSet = $this->canFieldBeSet($model, $key);
            if($canFieldBeSet){
                if ($first) {
                    $first = false;
                }else{
                    $fieldsString.=', ';
                    $valuesString.=', ';
                }
                $fieldsString.=$key;
                $valuesString.=':'.$key;
            }
        }
        $fieldsString.=')';
        $valuesString.=')';
        $sql = 'INSERT INTO '._DB_PREFIX_.$this->definition['table'].$fieldsString.' VALUES '.$valuesString;
        $query =$this->db->prepare($sql);  
        foreach ($this->definition['fields'] as $key => $value) {
            if ($this->canFieldBeSet($model, $key)) {
                $this->addQueryParam($query, $model, $key);
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
     * @param array $fieldsToExclude
     * @param array $fieldsToUpdate
     * @return bool
     */
    protected function _update($model, $identifiers = array(), $fieldsToExclude = array(), $fieldsToUpdate = array()) {
        $fieldsString ='';
        $first = true;
        foreach ($this->definition['fields'] as $key => $value) {
            $canFieldBeSet = $this->canFieldBeSet($model, $key);
            $updateField = (empty($fieldsToUpdate))? true: in_array($key, $fieldsToUpdate);
            if($canFieldBeSet && !in_array($key, $fieldsToExclude) && $updateField){
                if ($first) {
                    $first = false;
                }else{
                    $fieldsString.=', ';
                }
                $fieldsString.=$key.' = :'.$key;
            }
        }
        $condition = $this->getCondition($model, $identifiers);
        $sql = 'UPDATE '._DB_PREFIX_.$this->definition['table'].' SET '.$fieldsString .' WHERE '.$condition;
        $query =$this->db->prepare($sql);
        foreach ($this->definition['fields'] as $key => $value) {
            if ($this->canFieldBeSet($model, $key) && !in_array($key, $fieldsToExclude)) {
                $this->addQueryParam($query, $model, $key);
            }
        }
        $this->setRestrictionParams($query, $model, $identifiers);
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
    protected function _delete($model, $identifiers = array()) {
        $condition = $this->getCondition($model, $identifiers);
        $sql = 'DELETE FROM  '._DB_PREFIX_.$this->definition['table'].' WHERE '.$condition;
        $query =$this->db->prepare($sql);
        $this->setRestrictionParams($query, $model, $identifiers);
        $result = $query->execute();
        return $result;
    }
    
    /**
     * getByField object
     *
     * @param array $fields
     * @return array
     */
    protected function _getByFields($fields, $returnTotal = false, $start = 0, $limit = 0,
            $orderBy = OrderBy::PRIMARY, $orderWay = OrderWay::DESC, $logicalOperator = LogicalOperator::AND_) {
        $restriction=$this->getRestrictionFromArray($fields, $logicalOperator);
        $sql = 'SELECT * FROM  '._DB_PREFIX_.$this->definition['table'].(empty($restriction)?'':' WHERE '.$restriction).
        $this->getOrderString($orderBy, $orderWay) . $this->getLimitString($start, $limit);
        $query =$this->db->prepare($sql);
        foreach ($fields as $key => $value) {
            $keyTmp = $key.'cond_tmp';
            $this->{$keyTmp} = $value;
            $query->bindParam(':'.$key, $this->{$keyTmp});
        }
        $query->execute();
        return $this->getAllAsObjectFromQuery($query);
    }
    
    protected function getAllAsObjectFromQuery($query){
        $result = array();
        while ($data = $query->fetch(\PDO::FETCH_ASSOC)){
            $result[]=$this->createModel($data);
        }
        $query->closeCursor();
        return $result;
    }
    
    protected function getOrderString($orderBy, $orderWay){
        
    }
    
    protected function getLimitString($start, $limit){
        
    }
    
    protected function addQueryParam($query, $model, $field, $suffix = '_tmp', $lang = false) {
        $method = 'get'.ucfirst($field);
        $keyTmp = $key.$suffix;
        $this->{$keyTmp} = $model->$method();
        $query->bindParam(':'.$key, $this->{$keyTmp});
    }
    
    protected function addConditionQueryParam($query, $model, $field) {
        self::addQueryParam($query, $model, $field, '_cond_tmp');
    }
    
    protected function getCondition($model, $identifiers) {
        $condition = '';
        if(!empty($identifiers)){
            $first = true;
            $condition.=$this->getRestrictionFromArray($identifiers);
        }else{
            if (!is_array($this->definition['primary'])) {
                $condition .= $this->definition['primary'] .' = :'.$this->definition['primary'];
            }else{
                $first = true;
                foreach ($this->definition['primary'] as $field) {
                    if ($first) {
                        $first = false;
                    }else{
                        $condition.=' AND ';
                    }
                    $condition.='('.$field.' = :'.$field.')';
                }
            }
        }
        return $condition;
    }
    
    protected function getRestrictionFromArray($params, $logicalOperator = LogicalOperator::AND_) {
        $condition = '';
        $first = true;
        foreach ($params as $key => $value) {
            if ($first) {
                $first = false;
            }else{
                $condition.=' ' .(($logicalOperator === LogicalOperator::AND_) ? 'AND' : 'OR').' ';
            }
            $condition.=$key.' = :'.$key;
        }
        return $condition;
    }
    
    protected  function setRestrictionParams($query, $model, $identifiers) {
        if(!empty($identifiers)){
            foreach ($identifiers as $key => $value) {
                $keyTmp = $key.'cond_tmp';
                $this->{$keyTmp} = $value;
                $query->bindParam(':'.$key, $this->{$keyTmp});
            }
        }else{
            if (!is_array($this->definition['primary'])) {
                $this->addConditionQueryParam($query, $model, $this->definition['primary']);
            }else{
                foreach ($this->definition['primary'] as $field) {
                    $this->addConditionQueryParam($query, $model, $field);
                }
            }
        }
    }
    
	public function saveMultilangFields($model, $update = false)
    {
		$result = true;
		if($this->saveOfLangField && $this->definition['multilang'] && !is_array($this->definition['primary'])){
			$langFields = $model->getLangFields();
			$method = 'get' . ucfirst($this->definition['primary']);
			$idObject = $model->$method();
			if($update){
				$sqlInit = $this->getLangUpdateSqlInit($model);
			}else{
				$addSqlInit= $this->getLangAddSqlInit($model);
			}
			foreach ($this->languages as $lang => $langObject){
				if($update && $this->isObjectSavedForLang($idObject, $lang)){
					$sql = $sqlInit;
				}else{
					if(!isset($addSqlInit)){
						$addSqlInit = $this->getLangAddSqlInit($model);
					}
					$sql = $addSqlInit;
				}
				$req=$this->dao->prepare($sql);
				$req->bindParam(':id_' . $this->definition['table'], $idObject);
				$req->bindParam(':lang', $lang);
				foreach ($langFields as $field){
					$method = 'get'.ucfirst($field);
					if (is_callable(array($model, $method)) && property_exists($model, $field)){
						$req->bindParam(':'.$field, $model->$method()[$lang]);
					} 
				}
				$result = ($result && (bool)$req->execute());
			}
		}
		return $result;
    }
    
    public function getLangAddSqlInit($model)
    {
		$langFields = $model->getLangFields();
    	$sqlInit='INSERT INTO ' . _DB_PREFIX_ . $this->definition['table']. '_lang (' . 'id_'.$this->definition['table'] . ', lang, ' .
      	implode(',', $langFields) . ') VALUES(:id_' . $this->definition['table'] . ', :lang, :' . implode(',:', $langFields).')';
      	return $sqlInit;
    }
    
    public function getLangUpdateSqlInit($model)
    {
		$langFields = $model->getLangFields();
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
    	$req=$this->dao->prepare($sql);
    	$req->bindParam(':idObject', $idObject);
    	$req->bindParam(':lang', $lang);
    	$req->execute();
    	$data = $req->fetch(\PDO::FETCH_OBJ);
    	return ((int)$data->number > 0);
    }
    
}