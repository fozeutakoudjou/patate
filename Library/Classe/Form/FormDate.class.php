<?php
/**
 * Description of FormDate
 *
 * @author FFOZEU
 */
namespace Library\Classe\Form;

class FormDate extends FormText {

	protected $format;

	protected function _init() {

		if (!isset(self::$error_list['invalid_date'])) {

			self::$error_list['invalid_date'] = "La date entrée n'existe pas.";
		}
		if (!isset(self::$error_list['invalid_date_format'])) {

			self::$error_list['invalid_date_format'] = "La date entrée ne respecte pas le format imposé (%s).";
		}
	}

	public function format($format) {

		$this->format = $format;
		return $this;
	}

	public function is_valid($value) {

		if (parent::is_valid($value)) {

			$from = array('dd', 'mm', 'yyyy', 'yy', 'HH', 'MM', 'SS');
			$to   = array('%d', '%m',  '%Y',  '%y', '%H', '%M', '%S');
			$format = str_replace($from, $to, $this->format);
            
			date_default_timezone_set('Europe/Paris');
			$datetime = strptime($value, $format);

			if (false !== ($datetime)) {
				if (!checkdate($datetime['tm_mon']+1, $datetime['tm_mday'], $datetime['tm_year']+1900)) {

					$this->_error('invalid_date');
					return false;
				}
				return true;
			}
			$this->_error('invalid_date_format');
			return false;
		}
		return false;
	}

	protected function _custom_errors($id_error, $error) {

		if ('invalid_date_format' == $id_error) {

			$this->error_messages[$id_error] = vsprintf($error, $this->format);
			return true;
		}
		return false;
	}
}

?>
