<?php
/**
 * Description of Form
 *
 * @author FFOZEU
 */
namespace Library\Classe\Form;

// ---------------------------------------------------------------------------
class Form {

	protected $fields;
	protected $hidden_fields;
	protected $submit_fields;
	protected $fieldsets;
	protected $errors;
	protected $auto_id;
	protected $label_suffix;
	protected $bounded_data;
	protected $cleaned_data;
	protected $attrs;
	protected $uniqid;
    protected $closeForm;
    protected $formType;
    protected $languages;
    protected $activeLang;

    protected static $instances = array();
    
    private $chemin = '\\Library\\Classe\\Form\\';

    public function __construct($uniqid, $method = 'post', $formType = 'form-horizontal', $languages = array(), $activeLang = '') {

		$this->fields= array();
		$this->hidden_fields = array();
		$this->submit_fields = array();
		$this->fieldsets     = array();
		$this->errors        = array();
		$this->auto_id       = 'id_%s';
		$this->label_suffix  = ' :';
		$this->bounded_data  = array();
		$this->cleaned_data  = array();
		$this->attrs         = new AttributeList(array('method' => $method));
        $this->closeForm = true;
        $this->formType = $formType;
        $this->languages = $languages;
        $this->activeLang = $activeLang;
        

		if (false !== $uniqid && in_array($uniqid, self::$instances)) {

			trigger_error("Un formulaire identifié par '$uniqid' existe déjà ! Conflits potentiels détectés !", E_USER_WARNING);

		} else {

			self::$instances[] = $uniqid;
			$this->uniqid = $uniqid;
			$this->add('Hidden', 'uniqid')->value($uniqid);
		}
	}

	public function getActiveLang() {
		return $this->activeLang;
	}
	public function getLanguages() {
		return $this->languages;
	}
	public function setActiveLang($activeLang) {
		return $this->activeLang = $activeLang;
	}
	public function setLanguages($languages) {
		return $this->languages = $languages;
	}
	public function is_valid(array $values) {

		if ($this->is_submited()) {
             
			$valid = true;
            
			foreach($this->fields as $name => $f) {
                
				$values[$name] = (isset($values[$name])) ? $values[$name] : null;
                
				$valid = $f->is_valid($values[$name]) && $valid;
			}
			if ($valid) {

				$this->cleaned_data = array();

				foreach($this->fields as $name => $f) {

					$this->cleaned_data[$f->get_name()] = $f->get_cleaned_value($values[$name]);
				}
			}
			return $valid;
		}
		return false;
	}

	public function fields() {

		return $this->fields;
	}

	public function field($name) {

		return $this->fields[$name];
	}

	public function errors() {

		$this->errors = array();

		foreach($this->fields as $name=>$f) {

			$this->errors[$name] = $f->errors();
		}
		return $this->errors;
	}

	public function bound($data) {

		return $this->bind($data);
	}

	public function bind($data) {

		foreach($data as $k=>$v) {

			$this->bounded_data[$k] = $v;
		}
		return $this;
	}

	public function label_suffix() {

		return $this->label_suffix;
	}

	public function auto_id() {

		return $this->auto_id;
	}

	public function action($action) {

		$this->attrs['action'] = $action;
		return $this;
	}
    
    public function set_id($id) {

		$this->attrs['id'] = $id;
		return $this;
	}

	public function enctype($enctype) {

		$enctype = strtolower($enctype);

		if (in_array($enctype, array('multipart/form-data', 'application/x-www-form-urlencoded'))) {

			$this->attrs['enctype'] = $enctype;
		}
		return $this;
	}

	public function method($method) {

		$method = strtolower($method);

		if (in_array($method, array('get', 'post'))) {

			$this->attrs['method'] = $method;
		}
		return $this;
	}

	public function fieldsets($array) {

		$this->fieldsets = $array;
	}

	public function is_submited() {

		return $this->is_submitted();
	}

	public function is_submitted() {

		$check = ($_SERVER['REQUEST_METHOD'] == 'POST') ? $_POST : $_GET;
        
		if (!empty($check['uniqid']) && $check['uniqid'] == $this->uniqid) {
            //
			foreach($this->submit_fields as $s) {
                
				if (isset($check[$s->get_name()])) {
                    return true;
                }
			}
		}
		return false;
	}

	public function add($field, $name) {

		if (!isset($this->fields[$name])) {

			$field = $this->chemin.'Form'.ucfirst($field);
			$field_object = new $field($name, $this);

			if ($this->chemin.'FormSubmit' == $field) {

				$this->submit_fields[$name] = $field_object;

			} else if ($this->chemin.'FormHidden' == $field) {

				$this->hidden_fields[$name] = $field_object;
			}
			$this->fields[$name] = $field_object;

			return $field_object;

		} else {

			trigger_error("Un champ nommé '$name' existe déjà dans le formulaire identifié par '{$this->uniqid}'.", E_USER_WARNING);
		}
	}

	public function get_bounded_data($name = null) {

		if (null !== $name) {

			return isset($this->bounded_data[$name]) ? $this->bounded_data[$name] : '';

		} else {

			return $this->bounded_data;
		}
		return null;
	}

	public function get_cleaned_data($name = null) {

		if (!empty($this->cleaned_data)) {

			$out = array();

			if (func_num_args() > 1) {

				if (!is_array($name)) {

					$name = func_get_args();
				}

				foreach($name as $n) {

					$out[] = isset($this->cleaned_data[$n]) ? $this->cleaned_data[$n] : '';
				}

				return $out;
			}

			if (null !== $name) {

				return isset($this->cleaned_data[$name]) ? $this->cleaned_data[$name] : '';

			} else {

				return $this->cleaned_data;
			}
		}
		return null;
	}

	public function __toString() {

		$tab = func_num_args() > 0 ? func_get_arg(0) : '';
		
		$o = $tab.'<form class="'.$this->formType.'"'.$this->attrs.'>'."\n";

		if (empty($this->fieldsets)) {

			$o .= $this->_html_fields($tab."\t", array_diff_key($this->fields, $this->hidden_fields, $this->submit_fields));
			if (!empty($this->hidden_fields)) { $o .= $this->_html_hidden_fields($tab."\t", $this->hidden_fields); }
			$o .= $this->_html_fields($tab."\t", $this->submit_fields);

		} else {

			$hidden_fields = $this->hidden_fields;
			$submit_fields = $this->submit_fields;

			foreach ($this->fieldsets as $legend => $fields) {

				$o .= $this->_html_fieldset($tab, $legend, $fields);

				foreach($fields as $f) {

					unset($hidden_fields[$f], $submit_fields[$f]);
				}
			}
			if (!empty($hidden_fields)) { $o .= $this->_html_hidden_fields($tab."\t", $hidden_fields); }
			if (!empty($submit_fields)) { $o .= $this->_html_fields($tab."\t", $submit_fields); }
		}
		$o .= $tab.($this->closeForm?'</form>':'');
		return $o;
	}

	protected function _html_fields($tab, $fields, $filter = array()) {

		$o = '';
        
		foreach($fields as $f) {
			if (empty($filter) || in_array($f->get_name(), $filter)) {

				$o .= "$tab<div class='form-group ".$f->get_name()."'>\n".$f->__toString($tab."\t")."\n$tab</div>\n";
			}
		}
		return $o;
	}

	protected function _html_hidden_fields($tab, $fields, $filter = array()) {

		$o = ''; // "$tab<p>\n";

		foreach($fields as $f) {

			if (empty($filter) || in_array($f->get_name(), $filter)) {

				$o .= $tab.$f->__toString($tab)."\n";
			}
		}
		// $o .= "$tab</p>\n";
		return $o;
	}

	protected function _html_fieldset($tab, $legend, $fields) {

		$o  = "$tab\t".'<fieldset>'."\n";
		$o .= "$tab\t".'<legend>'.$legend.'</legend>'."\n";
		$o .= $this->_html_fields($tab."\t\t", $this->fields, $fields);
		$o .= "$tab\t".'</fieldset>'."\n";
		return $o;
	}
    /**
     * verifie si on ferme le formulaire ou pas
     * @param type $defaul
     * @return type 
     */
    public function closeForm($defaul = true){
        $this->closeForm = (boolean)$defaul; 
    }
    
    public function set_label_suffix($d=':'){
        $this->label_suffix = (string)$d;
    }
    /**
     * ajout d'attribut supplémentaire
     * @param array $attrib
     * @return \Library\Classe\Form\Form
     */
    public function addAttrs(array $attrib){
        if(is_array($attrib))
            foreach ($attrib as $key => $value)
                $this->attrs[$key] = $value;
        return $this;
    }
}
/**
  * Parse a time/date generated with strftime().
  *
  * This function is the same as the original one defined by PHP (Linux/Unix only),
  *  but now you can use it on Windows too.
  *  Limitation : Only this format can be parsed %S, %M, %H, %d, %m, %Y
  *
  * @author Lionel SAURON
  * @version 1.0
  * @public
  *
  * @param $sDate(string)    The string to parse (e.g. returned from strftime()).
  * @param $sFormat(string)  The format used in date  (e.g. the same as used in strftime()).
  * @return (array)  Returns an array with the <code>$sDate</code> parsed, or <code>false</code> on error.
  */
if(function_exists("strptime") == false) {

	function strptime($sDate, $sFormat) {
		$aResult = array (
			'tm_sec'   => 0,
			'tm_min'   => 0,
			'tm_hour'  => 0,
			'tm_mday'  => 1,
			'tm_mon'   => 0,
			'tm_year'  => 0,
			'tm_wday'  => 0,
			'tm_yday'  => 0,
			'unparsed' => $sDate,
		);

		while(!empty($sFormat)) {
		// ===== Search a %x element, Check the static string before the %x =====

			$nIdxFound = strpos($sFormat, '%');

			if($nIdxFound === false)
			{
				// There is no more format. Check the last static string.
				$aResult['unparsed'] = ($sFormat == $sDate) ? "" : $sDate;
				break;
			}
			$sFormatBefore = substr($sFormat, 0, $nIdxFound);
			$sDateBefore   = substr($sDate,   0, $nIdxFound);
			if($sFormatBefore != $sDateBefore) break;

			// ===== Read the value of the %x found =====
			$sFormat = substr($sFormat, $nIdxFound);
			$sDate   = substr($sDate,   $nIdxFound);
			$aResult['unparsed'] = $sDate;
			$sFormatCurrent = substr($sFormat, 0, 2);
			$sFormatAfter   = substr($sFormat, 2);
			$nValue = -1;
			$sDateAfter = "";

			switch($sFormatCurrent) {

				case '%S': // Seconds after the minute (0-59)
					sscanf($sDate, "%2d%[^\\n]", $nValue, $sDateAfter);
					if(($nValue < 0) || ($nValue > 59)) return false;
					$aResult['tm_sec']  = $nValue;
				break;
				// ----------

				case '%M': // Minutes after the hour (0-59)
					sscanf($sDate, "%2d%[^\\n]", $nValue, $sDateAfter);
					if(($nValue < 0) || ($nValue > 59)) return false;
					$aResult['tm_min']  = $nValue;
				break;
				// ----------

				case '%H': // Hour since midnight (0-23)
					sscanf($sDate, "%2d%[^\\n]", $nValue, $sDateAfter);
					if(($nValue < 0) || ($nValue > 23)) return false;
					$aResult['tm_hour']  = $nValue;
				break;

				// ----------
				case '%d': // Day of the month (1-31)
					sscanf($sDate, "%2d%[^\\n]", $nValue, $sDateAfter);
					if(($nValue < 1) || ($nValue > 31)) return false;
					$aResult['tm_mday']  = $nValue;
				break;

				// ----------
				case '%m': // Months since January (0-11)
					sscanf($sDate, "%2d%[^\\n]", $nValue, $sDateAfter);
					if(($nValue < 1) || ($nValue > 12)) return false;
					$aResult['tm_mon']  = ($nValue - 1);
				break;

				// ----------
				case '%Y': // Years since 1900
					sscanf($sDate, "%4d%[^\\n]", $nValue, $sDateAfter);
					if($nValue < 1900) return false;
					$aResult['tm_year']  = ($nValue - 1900);
				break;

				// ----------
				default: break 2; // Break Switch and while
			}
			// ===== Next please =====
			$sFormat = $sFormatAfter;
			$sDate   = $sDateAfter;
			$aResult['unparsed'] = $sDate;
		}
		// END while($sFormat != "")

		// ===== Create the other value of the result array =====
		$nParsedDateTimestamp = mktime($aResult['tm_hour'], $aResult['tm_min'], $aResult['tm_sec'], $aResult['tm_mon'] + 1, $aResult['tm_mday'], $aResult['tm_year']+1900-28*floor((($aResult['tm_year']-100)/28)));

		// Before PHP 5.1 return -1 when error
		if(($nParsedDateTimestamp === false) || ($nParsedDateTimestamp === -1))
			return false;

		$aResult['tm_wday'] = strftime("%w", $nParsedDateTimestamp); // Days since Sunday (0-6)
		$aResult['tm_yday'] = (strftime("%j", $nParsedDateTimestamp) - 1); // Days since January 1 (0-365)

		return $aResult;
	}
	// END of function
}
// END if(function_exists("strptime") == false)

// end of file */

?>