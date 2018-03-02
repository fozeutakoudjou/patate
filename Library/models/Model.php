<?php
namespace models;

use utilities\Validate;
use utilities\Tools;

class Model{
    
    const TYPE_INT     = 1;
    const TYPE_BOOL    = 2;
    const TYPE_STRING  = 3;
    const TYPE_FLOAT   = 4;
    const TYPE_DATE    = 5;
    const TYPE_HTML    = 6;
    const TYPE_NOTHING = 7;
    const TYPE_SQL     = 8;
    
    protected $fieldsValidated = false;
    protected $defaultLang;
    protected $languages;
    protected $definition = array();
    
    public function __construct($param = array(), $fromDB = false, $lang = '', $useOfAllLang = false, $preffix = ''){
        $this->hydrate($data, $fromDB, $lang, $useOfAllLang, $preffix);
    }
    
    public function hydrate(array $data, $fromDB = false, $lang = '', $useOfAllLang = false, $preffix = '')
    {
		foreach ($data as $field => $value) {
            $this->setFieldValue($field, $value, $fromDB, $lang, $useOfAllLang, $preffix);
        }
    }
	public function copyFromPost($preffix = '')
    {
		$data = Tools::getAllValues();
		$primaries = is_array($this->definition['primary']) ? $this->definition['primary'] : array($this->definition['primary']);
		foreach($primaries as $primary){
			$postedPrimary = $primary . '_' . $this->definition['table'];
			if(!isset($data[$primary]) && isset($data[$postedPrimary])){
				$data[$primary] = $data[$postedPrimary];
			}
		}
		$this->hydrate($data, false, '', true, $preffix);
    }
    public function setFieldValue($field, $value, $fromDB = false, $lang = '', $useOfAllLang = false, $preffix = ''){
		$primaries = is_array($this->definition['primary']) ? $this->definition['primary'] : array($this->definition['primary']);
		$field = str_replace($preffix, '', $field);
		if(!$fromDB && isset($this->definition['multilang']) && $this->definition['multilang'] && !isset($this->definition['fields'][$field]) && !in_array($field, $primaries)){
			$tmpField = Tools::removeLangKey($field);
			$isLangField = isset($this->definition['fields'][$tmpField]) && isset($this->definition['fields'][$tmpField]['lang']) && $this->definition['fields'][$tmpField]['lang'];
			if($isLangField){
				$lang = Tools::getLangFormField($field, $tmpField);
				$useOfAllLang = true;
				$field = $tmpField;
			}
		}else{
			$isLangField = isset($this->definition['multilang']) && $this->definition['multilang'] && isset($this->definition['fields'][$field]['lang']) && $this->definition['fields'][$field]['lang'];
		}
        $methode = 'set'.ucfirst($field);
		if (isset($this->definition['fields'][$field]) || in_array($field, $primaries)){
			if($isLangField && $useOfAllLang){
				$tmpValue = is_array($this->$field) ? $this->$field : array();
				$tmpValue[$lang] = $value;
				$value = $tmpValue;
			}
			$this->$methode($value); 
		}
    }
	
    public function formatFields($languages, $defaultLang) {
		$this->defaultLang = $defaultLang;
		$this->languages = $languages;
        if(isset($this->definition['fields'])){
            foreach ($this->definition['fields'] as $fieldName=> $fieldDefinition) {
                $purify = false;
				$isLangField = isset($this->definition['multilang']) && $this->definition['multilang'] && isset($fieldDefinition['lang']) && $fieldDefinition['lang'];
                if(isset($fieldDefinition['validate'])){
                    $fieldValidations = is_array($fieldDefinition['validate']) ? $fieldDefinition['validate']
						:array($fieldDefinition['validate']);
                    $purify = in_array('isCleanHtml', $fieldValidations);
                }
                $getter = 'get'.ucfirst($fieldName);
                $value = $this->$getter();
				if($this->isFieldEmpty($fieldName) && isset($fieldDefinition['default']) && $fieldDefinition['default']){
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
                }
				
				$setter = 'set'.ucfirst($fieldName);
                $this->$setter($value);
				if($isLangField){
					$this->fillMultilangEmptyFields($fieldName);
				}
            }
        }
    }
    
    public function validateFields() {
        $errors=array();
        foreach ($this->definition['fields'] as $fieldName => $fieldDefinition) {
            $method = 'get'.ucfirst($fieldName);
            $value = $this->$method();
			$value = is_array($value) ? $value : array($value);
            if($this->isFieldEmpty($fieldName)){
                if(isset($fieldDefinition['required']) && $fieldDefinition['required']
                        && !isset($fieldDefinition['default'])){
                    $errors[$fieldName] = array('errors' => array(Validate::VALIDATE_REQUIRED));
                }
            }else if(isset($['validate'])){
                $fieldValidations = is_array($fieldDefinition['validate']) ? $fieldDefinition['validate'] : array($fieldDefinition['validate']);
                foreach ($fieldValidations as $validation) {
                    if (method_exists('Validate', $validation)) {
						foreach($value as $key => $val){
							if (!Validate::$validation($value)) {
								if (isset($errors[$fieldName])) {
									$errors[$fieldName]['errors'][]=$validation;
								}else{
									$errors[$fieldName] = array('errors' => array($validation));
									if(isset($this->definition['multilang']) && $this->definition['multilang'] &&
										isset($fieldDefinition['lang']) && $fieldDefinition['lang']){
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
		if(is_array($this->$field)){
			foreach ($this->$field as $value) {
				if (Validate::isRequired($value)) {
					$emptyField = false;
					break;
				}
			}
		}else{
			return !Validate::isRequired($this->$field);
		}
        return $emptyField;
    }
	public function isFieldEmpty($field)
    {
		if(isset($this->definition['fields'][$field]['lang']) && $this->definition['fields'][$field]['lang']){
			return $this->isMultilangFieldEmpty($field)
		}else{
			return !Validate::isRequired($this->$field);
		}
    }
	protected function fillMultilangEmptyFields($field)
    {
		$languages = $this->languages;
		$values = $this->$field;
        $defaultValue = ((isset($values[$this->defaultLang]) && (!empty($values[$this->defaultLang]))) ? $values[$this->defaultLang] : '');
        //Recherche d'une valeur non nulle
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
        $this->$field = $values;
    }
    public function isFieldsValidated() {
        return $this->fieldsValidated;
    }
    
    public function getDefinition() {
        return $this->definition;
    }
	
	public function getLangFields() {
		if(isset($this->definition['multilang']) && $this->definition['multilang']){
			if(!isset($this->langFields)){
				$this->langFields = array();
				foreach ($this->definition['fields'] as $fieldName => $fieldDefinition) {
					if(isset($fieldDefinition['lang']) && $fieldDefinition['lang']){
						$this->langFields[] = $fieldName;
					}
				}
			}
		}else{
			return array();
		}
        return $this->langFields;
    }
}