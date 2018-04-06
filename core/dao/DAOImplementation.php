<?php
namespace core\dao;

use core\constant\dao\Operator;
use core\constant\dao\LogicalOperator;
use core\constant\dao\OrderWay;
use core\constant\dao\OrderBy;

interface DAOImplementation{
    
	function saveMultilangFields($model, $update = false, $fieldsToUpdate = array());
    
    
    /**
     * Add object
     *
     * @param \models\Model $model
     * @return bool
     */
    function _add($model);
    
    
    /**
     * Update object
     *
     * @param \models\Model $model
     * @param array $identifiers
     * @param array $fieldsToExclude
     * @param array $fieldsToUpdate
     * @return bool
     */
    function _update($model, $fieldsToUpdate = array(), $identifiers = array());
    
    /**
     * Delete object
     *
     * @param \models\Model $model
     * @param array $identifiers
     * @return bool
     */
    function _delete($model, $identifiers = array());
    
    /**
     * getByField object
     *
     * @param \models\Model $model
     * @param array $fields
     * @return array
     */
    function _getByFields($fields, $returnTotal = false, $association = array(), $start = 0, $limit = 0,
            $orderBy = OrderBy::PRIMARY, $orderWay = OrderWay::DESC, $logicalOperator = LogicalOperator::AND_);
    
    
    function _getByFieldsCount($fields, $logicalOperator = LogicalOperator::AND_);
}