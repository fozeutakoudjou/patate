<?php

/**
 * Description of FormTime
 *
 * @author FFOZEU
 */
namespace Library\Classe\Form;

class FormTime extends FormText{
    //put your code here
    
	protected $format;

	protected function _init() {

		if (!isset(self::$error_list['invalid_time'])) {

			self::$error_list['invalid_time'] = "L'heure entrée n'existe pas.";
		}
		if (!isset(self::$error_list['invalid_time_format'])) {

			self::$error_list['invalid_time_format'] = "L'heure ne respecte pas le format imposé (%s).";
		}
	}
	
	public function format($format) {

		$this->format = $format;
		return $this;
	}
	
    public function is_valid($value) {

		if (parent::is_valid($value)) {

			$from = array('HH', 'MM', 'SS');
			$to   = array('%H', '%M', '%S');
			$format = str_replace($from, $to, $this->format);

			date_default_timezone_set('Europe/Paris');
			$datetime = strptime($value, $format);
			if (false !== ($datetime)) {

				if (!$this->checkHour($datetime)) {

					$this->_error('invalid_time');
					return false;
				}
				return true;
			}
			$this->_error('invalid_time_format');
			return false;
		}
		return false;
	}
    
    public function checkHour($datetime){
        
        return true;
    }
	
	protected function _custom_errors($id_error, $error) {

		if ('invalid_time_format' == $id_error) {

			$this->error_messages[$id_error] = vsprintf($error, $this->format);
			return true;
		}
		return false;
	}
}

?>
