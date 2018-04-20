<?php
namespace core\models;

use core\Validate;
use core\Tools;

class Model{
    
    const TYPE_INT     = 1;
    const TYPE_BOOL    = 2;
    const TYPE_STRING  = 3;
    const TYPE_FLOAT   = 4;
    const TYPE_DATE    = 5;
    const TYPE_HTML    = 6;
    const TYPE_NOTHING = 7;
    const TYPE_SQL     = 8;
    const TYPE_DECIMAL = 9;
    
    protected $fieldsValidated = false;
    protected $definition = array();
	
    protected $langFields = null;
	
    protected $simpleFields = null;
	
    protected $associateds = array();
    
    public function __construct($data = array(), $fromDB = false, $lang = '', $useOfAllLang = false, $languages = array(), $preffix = ''){
		if(!empty($data)){
			$this->hydrate($data, $fromDB, $lang, $useOfAllLang, $languages, $preffix);
		}
    }
    
    public function hydrate(array $data, $fromDB = false, $lang = '', $useOfAllLang = false, $languages = array(), $preffix = '')
    {
		foreach ($data as $field => $value) {
            $this->setFieldValue($field, $value, $lang, $useOfAllLang, $preffix);
        }
		if(!$fromDB && $this->isMultilang()){
			$langFields = $this->getLangFields();
			foreach ($languages as $tmpLang => $langObject) {
				foreach ($langFields as $field) {
					$fieldKey = $preffix . Tools::getLangFieldKey($field, $tmpLang);
					if (!isset($data[$fieldKey])) {
						$this->setFieldValue($field, $data[$fieldKey], $tmpLang, true, '');
					}
				}
			}
		}
    }
	public function copyFromPost($languages = array(), $preffix = '')
    {
		$data = Tools::getAllValues();
		$primaries = is_array($this->definition['primary']) ? $this->definition['primary'] : array($this->definition['primary']);
		foreach($primaries as $primary){
			$postedPrimary = $preffix . $primary . '_' . $this->definition['table'];
			if(!isset($data[$preffix . $primary]) && isset($data[$postedPrimary])){
				$data[$preffix . $primary] = $data[$postedPrimary];
			}
		}
		$this->hydrate($data, false, '', true, $languages, $preffix);
    }
    public function setFieldValue($field, $value, $lang = '', $useOfAllLang = false, $preffix = ''){
		if(empty($preffix) || (strpos($field, $preffix) === 0)){
			$primaries = is_array($this->definition['primary']) ? $this->definition['primary'] : array($this->definition['primary']);
			$field = str_replace($preffix, '', $field);
			if (isset($this->definition['fields'][$field]) || in_array($field, $primaries)){
				if($this->isLangField($field) && $useOfAllLang && !empty($lang)){
					$fieldValue = $this->getPropertyValue($field);
					$tmpValue = is_array($fieldValue) ? $fieldValue: array();
					$tmpValue[$lang] = $value;
					$value = $tmpValue;
				}
				$this->setPropertyValue($field, $value); 
			}
		}
    }
	
    public function formatFields($languages = array(), $defaultLang = '') {
        if(isset($this->definition['fields'])){
            foreach ($this->definition['fields'] as $fieldName=> $fieldDefinition) {
                $purify = false;
				$isLangField = $this->isLangField($fieldName);
                if(isset($fieldDefinition['validate'])){
                    $fieldValidations = is_array($fieldDefinition['validate']) ? $fieldDefinition['validate']
						:array($fieldDefinition['validate']);
                    $purify = in_array('isCleanHtml', $fieldValidations);
                }
                $value = $this->getPropertyValue($fieldName);
				if($this->isFieldEmpty($fieldName) && isset($fieldDefinition['default'])){
					$value = $fieldDefinition['default'];
				}
                if($isLangField && !is_array($value)){
					$value = array($defaultLang => $value);
				}
                if (isset($fieldDefinition['foreign']) && $fieldDefinition['foreign'] && empty($value)) {
                    $value = null;
                }else{
					$value = is_array($value) ? $value : array($value);
					foreach($value as $key => $val){
						$value[$key] = self::formatValue($val, $fieldDefinition['type'], false, $purify);
					}
					if(!$isLangField && isset($value[0])){
						$value = $value[0];
					}
                }
				$this->setPropertyValue($fieldName, $value);
				if($isLangField){
					$this->fillMultilangEmptyFields($fieldName, $languages, $defaultLang);
				}
            }
        }
    }
    
    public function validateFields() {
        $errors=array();
        foreach ($this->definition['fields'] as $fieldName => $fieldDefinition) {
            $value = $this->getPropertyValue($fieldName);
			$value = is_array($value) ? $value : array($value);
            if($this->isFieldEmpty($fieldName)){
                if(isset($fieldDefinition['required']) && $fieldDefinition['required']
                        && !isset($fieldDefinition['default'])){
                    $errors[$fieldName] = array('errors' => array(Validate::VALIDATE_REQUIRED));
                }
            }else if(isset($fieldDefinition['validate'])){
                $fieldValidations = is_array($fieldDefinition['validate']) ? $fieldDefinition['validate'] : array($fieldDefinition['validate']);
                foreach ($fieldValidations as $validation) {
                    if (method_exists('core\\Validate', $validation)) {
						foreach($value as $key => $val){
							if (!Validate::$validation($val)) {
								if (isset($errors[$fieldName])) {
									$errors[$fieldName]['errors'][]=$validation;
								}else{
									$errors[$fieldName] = array('errors' => array($validation));
									if($this->isLangField($fieldName)){
										$errors[$fieldName]['lang'] = $key;
									}
								}
							}
						}
                    }
                }
            }
            
        }
        $this->fieldsValidated = empty($errors) ? true : false;
        return $errors;
    }
    
    public static function formatValue($value, $type, $with_quotes = false, $purify = true, $allow_null = false)
    {
        if ($allow_null && $value === null) {
            return array('type' => 'sql', 'value' => 'NULL');
        }
        
        switch ($type) {
            case self::TYPE_INT:
                return (int)$value;
                
            case self::TYPE_BOOL:
                return (int)$value;
                
            case self::TYPE_FLOAT:
                return (float)str_replace(',', '.', $value);
                
            case self::TYPE_DECIMAL:
                return (float)str_replace(',', '.', $value);
                
            case self::TYPE_DATE:
                if (!$value) {
                    $value = '0000-00-00';
                }
                
                if ($with_quotes) {
                    return '\''.pSQL($value).'\'';
                }
                return pSQL($value);
                
            case self::TYPE_HTML:
                if ($purify) {
                    $value = Tools::purifyHTML($value);
                }
                if ($with_quotes) {
                    return '\''.pSQL($value, true).'\'';
                }
                return pSQL($value, true);
                
            case self::TYPE_SQL:
                if ($with_quotes) {
                    return '\''.pSQL($value, true).'\'';
                }
                return pSQL($value, true);
                
            case self::TYPE_NOTHING:
                return $value;
                
            case self::TYPE_STRING:
            default :
                if ($with_quotes) {
                    return '\''.pSQL($value).'\'';
                }
                return pSQL($value);
        }
    }
    public function isMultilangFieldEmpty($field)
    {
        $emptyField = true;
		$value = $this->getPropertyValue($field);
		$values = is_array($value) ? $value : array($value);
		foreach ($values as $value) {
			if (Validate::isRequired($value)) {
				$emptyField = false;
				break;
			}
		}
        return $emptyField;
    }
	public function isFieldEmpty($field)
    {
		$value = $this->getPropertyValue($field);
		if($value === null){
			return true;
		}
		if($this->isLangField($field)){
			return $this->isMultilangFieldEmpty($field);
		}else{
			if($this->definition['fields'][$field]['type'] == self::TYPE_BOOL){
				return false;
			}else{
				return !Validate::isRequired($value);
			}
		}
    }
	protected function fillMultilangEmptyFields($field, $languages, $defaultLang)
    {
		$values = $this->getPropertyValue($field);
        $defaultValue = ((isset($values[$defaultLang]) && (!empty($values[$defaultLang]))) ? $values[$defaultLang] : '');
        //Find not empty value
        if (empty($defaultValue)) {
            foreach ($values as $value) {
                if (!empty($value)) {
                    $defaultValue= $value;
                    break;
                }
            }
        }
        foreach ($values as $key => $value) {
            if (empty($value)) {
                $values[$key] = $defaultValue;
            }
			unset($languages[$key]);
        }
		foreach ($languages as $key => $lang) {
            if (!isset($values[$key])) {
                $values[$key] = $defaultValue;
            }
        }
        $this->setPropertyValue($field, $values);
    }
    public function isFieldsValidated() {
        return $this->fieldsValidated;
    }
    
    public function getDefinition() {
        return $this->definition;
    }
	
	public function getLangFields() {
		if($this->langFields === null){
			$this->splitFields();
		}
        return $this->langFields;
    }
	
	public function getSimpleFields() {
		if($this->simpleFields === null){
			$this->splitFields();
		}
        return $this->simpleFields;
    }
	
	protected function splitFields() {
		$this->langFields = array();
		$this->simpleFields = array();
		foreach ($this->definition['fields'] as $fieldName => $fieldDefinition) {
			if($this->isLangField($fieldName)){
				$this->langFields[] = $fieldName;
			}else{
				$this->simpleFields[] = $fieldName;
			}
		}
    }
	
	public function isLangField($field) {
		return $this->isMultilang() && isset($this->definition['fields'][$field]) && isset($this->definition['fields'][$field]['lang']) && $this->definition['fields'][$field]['lang'];
    }
	
	public function isMultilang() {
		return isset($this->definition['multilang']) && $this->definition['multilang'];
    }
	
	public function isAutoIncrement() {
		return isset($this->definition['auto_increment']) && $this->definition['auto_increment'];
    }
	
	public function getGetterName($field){
		$getter = (isset($this->definition['fields'][$field]) && $this->definition['fields'][$field]['type'] == self::TYPE_BOOL) ? 'is' : 'get';
		return $getter . ucfirst($field);
	}
	
	public function getPropertyValue($field){
		$getter = $this->getGetterName($field);
		return $this->$getter();
	}
	
	public function setPropertyValue($field, $value){
		$setter = 'set' . ucfirst($field);
		return $this->$setter($value);
	}
	
	public function setAssociated($field, $value){
		$this->associateds[$field] = $value;
	}
	
	public function getAssociated($field){
		return isset($this->associateds[$field]) ? $this->associateds[$field] : null;
	}
	
	public function toArray($preffix = '', $getAssociated = false, $createSinglePrimary = false){
		$data = array();
		$primaries = $this->getPrimaries();
		foreach($primaries as $field){
			if(!isset($this->definition['fields'][$field])){
				$data[$preffix.$field] = $this->getPropertyValue($field);
			}
		}
		foreach($this->definition['fields'] as $field => $fieldDefinition){
			$data[$preffix.$field] = $this->getPropertyValue($field);
		}
		if($getAssociated){
			foreach($this->associateds as $field => $associated){
				$associatedData = $associated->toArray(Tools::formatForeignField($preffix.$field, ''), false);
				$data = array_merge($data, $associatedData);
			}
		}
		if($createSinglePrimary && is_array($this->definition['primary'])){
			$data[$preffix.$this->createSinglePrimary()] = $this->getSinglePrimaryValue();
		}
	}
	public function getPrimaries(){
		return is_array($this->definition['primary']) ? $this->definition['primary'] : array($this->definition['primary']);
	}
	public function createSinglePrimary(){
		return is_array($this->definition['primary']) ? implode(Separator::PRIMARIES_FIELD, $this->definition['primary']) : $this->definition['primary'];
	}
	
	public function getSinglePrimaryValue(){
		
		if(is_array($this->definition['primary'])){
			$first = true;
			$value = '';
			foreach($this->definition['primary'] as $field){
				if(!$first){
					$value.=Separator::PRIMARIES_DATA;
				}
				$value.= $this->getPropertyValue($field);
				$first = false;
			}
		}else{
			$value = $this->getPropertyValue($this->definition['primary']);
		}
		return $value;
	}
	
	public function getPrimaryValue($data){
		$value = array();
		if(is_array($this->definition['primary'])){
			$values = explode(Separator::PRIMARIES_DATA, $data);
			foreach($this->definition['primary'] as $key => $field){
				if(isset($values[$key])){
					$value[$field] = $values[$key];
				}else{
					throw new \Exception('data does not match primaries');
				}
			}
		}else{
			$value[$this->definition['primary']] = $data;
		}
		return $value;
	}
	
	public function isLoaded(){
		$primaries = $this->getPrimaries();
		$result = true;
		foreach($primaries as $primary){
			$value = $this->getPropertyValue($primary);
			if(empty($value)){
				$result = false;
				break;
			}
		}
		return $result;
	}
}