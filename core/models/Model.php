<?php
namespace core\models;

use core\Validate;
use core\Context;
use core\Tools;
use core\constant\Separator;
use core\constant\DataType;
/*use core\dao\Factory;
use core\FileTools;*/

class Model implements DataType{
    /*private $fieldsValidated = false;
    private $allFieldsValidated = false;*/
    protected $definition = array();
	
    protected $langFields = null;
	
    protected $simpleFields = null;
	
    protected $associateds = array();
	
    protected $singlePrimaryField;
    private $validatedFields;
    private $validatedUniques;
	/*private static $dao;
	
	protected static function getDAO(){
		if(self::$dao === null){
			$class = get_called_class();
			self::$dao = Factory::getDAOInstance(Tools::getClassNameWithoutNamespace($class), FileTools::getModuleFromNamespace($class));
		}
		return self::$dao;
	}*/
    
    public function __construct($data = array(), $fromDB = false, $lang = '', $useOfAllLang = false, $languages = array(), $preffix = ''){
		if(!empty($data)){
			$this->hydrate($data, $fromDB, $lang, $useOfAllLang, $languages, $preffix);
		}
    }
    
    public function hydrate(array $data, $fromDB = false, $lang = '', $useOfAllLang = false, $languages = array(), $preffix = '', $fieldsToExclude = array())
    {
		foreach ($data as $field => $value) {
			if(!in_array($field, $fieldsToExclude)){
				$this->setFieldValue($field, $value, $lang, $useOfAllLang, $preffix);
			}
        }
		if(!$fromDB && $this->isMultilang()){
			$langFields = $this->getLangFields();
			foreach ($languages as $tmpLang => $langObject) {
				foreach ($langFields as $field) {
					if(!in_array($field, $fieldsToExclude)){
						$fieldKey = $preffix . Tools::getLangFieldKey($field, $tmpLang);
						if (isset($data[$fieldKey])) {
							$this->setFieldValue($field, $data[$fieldKey], $tmpLang, true, '');
						}
					}
				}
			}
		}
    }
	public function copyFromPost($languages = array(), $preffix = '', $fieldsToExclude = array())
    {
		$data = Tools::getAllValues(true);
		/*$primaries = is_array($this->definition['primary']) ? $this->definition['primary'] : array($this->definition['primary']);
		foreach($primaries as $primary){
			$postedPrimary = $preffix . $primary . '_' . $this->definition['table'];
			if(!isset($data[$preffix . $primary]) && isset($data[$postedPrimary])){
				$data[$preffix . $primary] = $data[$postedPrimary];
			}
		}*/
		$this->hydrate($data, false, '', true, $languages, $preffix, $fieldsToExclude);
    }
    public function setFieldValue($field, $value, $lang = '', $useOfAllLang = false, $preffix = ''){
		if(empty($preffix) || (strpos($field, $preffix) === 0)){
			$primaries = $this->getPrimaries();
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
				if($fieldName=='password'){
					$value = Tools::encrypt($value);
				}
				$this->setPropertyValue($fieldName, $value);
				if($isLangField){
					$this->fillMultilangEmptyFields($fieldName, $languages, $defaultLang);
				}
            }
        }
    }
    public function validateField($field, $dao = null, $update = false, $identifiers = array()) {
		if(!isset($this->definition['fields'][$field])){
			throw new \Exception('this field doest not exist');
		}
		$errors = array();
		$value = $this->getPropertyValue($field);
		$value = is_array($value) ? $value : array($value);
		$fieldDefinition = $this->definition['fields'][$field];
		if($this->isFieldEmpty($field)){
			if(isset($fieldDefinition['required']) && $fieldDefinition['required']
					&& !isset($fieldDefinition['default'])){
				$errors[Validate::VALIDATE_REQUIRED] = Validate::VALIDATE_REQUIRED;
			}
		}else if(isset($fieldDefinition['validate'])){
			$fieldValidations = is_array($fieldDefinition['validate']) ? $fieldDefinition['validate'] : array($fieldDefinition['validate']);
			foreach ($fieldValidations as $validation) {
				if (method_exists('core\\Validate', $validation)) {
					foreach($value as $key => $val){
						if (!Validate::$validation($val)) {
							if($this->isLangField($field) && isset($errors[$validation])){
								$errors[$validation]['lang'][] = $key; 
							}else{
								$errors[$validation]= $this->isLangField($field) ? array('langs' => array($key)) : $validation;
							}
						}
					}
				}
			}
		}
		if(isset($fieldDefinition['maxSize'])||isset($fieldDefinition['minSize'])){
			$sizeValidations = array('maxSize'=>Validate::VALIDATE_MAX_SIZE, 'minSize'=>Validate::VALIDATE_MIN_SIZE);
			foreach ($sizeValidations as $validationKey => $validation) {
				if (isset($fieldDefinition[$validationKey])) {
					foreach($value as $key => $val){
						$size = strlen($val);
						$sizeToCheck = $fieldDefinition[$validationKey];
						if((($validation==Validate::VALIDATE_MAX_SIZE) && ($size>$sizeToCheck)) ||
							(($validation==Validate::VALIDATE_MIN_SIZE) && ($size<$sizeToCheck))){
							if($this->isLangField($field) && isset($errors[$validation])){
								$errors[$validation]['langs'][] = $key; 
							}else{
								$errors[$validation]= $this->isLangField($field) ? array('langs' => array($key), 'param' => $sizeToCheck) : array('param' => $sizeToCheck);
							}
							
						}
					}
				}
			}
		}
		
		
		if(empty($errors) && ($dao!=null)){
			if(isset($fieldDefinition['unique']) && $fieldDefinition['unique']){
				if(!$dao->checkUnique($this, $field, $update, $identifiers)){
					$errors['errors'] = Validate::VALIDATE_UNIQUE;
				}
			}
			if(isset($this->definition['uniques']) && is_array($this->definition['uniques'])){
				foreach ($this->definition['uniques'] as $uniques) {
					if(in_array($field, $uniques)){
						$key = Tools::joinArray($uniques, function($key, $value, $params){return $this->getValidateKey($value);},'_');
						if(!isset($this->validatedUniques[$key])){
							$this->validatedUniques[$key] = $dao->checkUnique($this, $uniques, $update, $identifiers);
						}
						if(!$this->validatedUniques[$key]){
							$errors['errors'] = Validate::VALIDATE_UNIQUE;
						}
					}
				}
			}
		}
		if(empty($errors)){
			$this->validatedFields[] = $this->getValidateKey($field);
		}
		return $errors;
	}
	public function getValidateKey($field){
		return serialize(array($field =>$this->getPropertyValue($field)));
	}
    public function validateFields($fieldsToExclude = array(), $fieldsToValidate = array(), $dao = null, $update = false, $identifiers = array()) {
        $errors=array();
		$useFieldsToValidate = !empty($fieldsToValidate);
		foreach ($this->definition['fields'] as $fieldName => $fieldDefinition) {
			if(!in_array($fieldName, $fieldsToExclude) && (!$useFieldsToValidate || in_array($fieldName, $fieldsToValidate))){
				$fieldErrors = $this->validateField($fieldName, $dao, $update, $identifiers);
				if(!empty($fieldErrors)){
					$errors[$fieldName] = $fieldErrors;
				}
			}
        }
        $this->fieldsValidated = empty($errors) ? true : false;
        return $errors;
    }
	public function isFieldsValidated($fieldsToExclude = array(), $fieldsToValidate = array()) {
		$useFieldsToValidate = !empty($fieldsToValidate);
		$validated = true;
		foreach ($this->definition['fields'] as $fieldName => $fieldDefinition) {
			if(!in_array($fieldName, $fieldsToExclude) && (!$useFieldsToValidate || in_array($fieldName, $fieldsToValidate))){
				if(!in_array($this->getValidateKey($fieldName), $this->validatedFields)){
					$validated = false;
					break;					
				}
			}
        }
        return $validated;
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
	
	/*public function isFieldsValidated() {
        return $this->fieldsValidated;
    }*/
    
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
		$value = '';
		if(isset($this->definition['fields'][$field]) || in_array($field, $this->getPrimaries())){
			$getter = $this->getGetterName($field);
			$value = $this->$getter();
		}elseif($this->createSinglePrimary()==$field){
			$value = $this->getSinglePrimaryValue();
		}else{
			$extract = Tools::extractForeignField($field);
			if(isset($extract['externalField']) && isset($this->associateds[$extract['field']]) && ($this->associateds[$extract['field']] != null)){
				$value = $this->associateds[$extract['field']]->getPropertyValue($extract['externalField']);
			}
		}
		return $value;
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
		return $data;
	}
	public function getPrimaries(){
		return is_array($this->definition['primary']) ? $this->definition['primary'] : array($this->definition['primary']);
	}
	public function createSinglePrimary(){
		if($this->singlePrimaryField==null){
			$this->singlePrimaryField=is_array($this->definition['primary']) ? implode(Separator::PRIMARIES_FIELD, $this->definition['primary']) : $this->definition['primary'];
		}
		return $this->singlePrimaryField;
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
	
	public function getPrimaryValuesFromString($data){
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
	
	public function getPrimaryValues(){
		$value = array();
		$primaries = $this->getPrimaries();
		foreach($primaries as $primary){
			$value[$primary] = $this->getPropertyValue($primary);
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
	
	public function __toString()
    {
		if(isset($this->definition['fields']['label'])){
			$value = $this->getPropertyValue('label');
		}elseif(isset($this->definition['fields']['name'])){
			$value = $this->getPropertyValue('name');
		}else{
			$primaries = $this->getPrimaries();
			$value = array();
			foreach($primaries as $primary){
				$value[] = $this->getPropertyValue($primary);
			}
			$value = implode(', ', $value);
		}
		if(is_array($value)){
			$lang = Context::getInstance()->getLang();
			$value = isset($value[$lang]) ? $value[$lang] : $value;
		}
        return $value;
    }
}