<?php

/**
 * Description of FormEmail
 *
 * @author FFOZEU
 */

namespace Library\Classe\Form;

class FormEmail extends FormText {

	protected function _init() {

		if (!isset(self::$error_list['invalid_email'])) {

			self::$error_list['invalid_email'] = "Ce n'est pas une adresse e-mail valide.";
		}
	}

	public function is_valid($value) {

		if (parent::is_valid($value)) {

			if (0 < preg_match('`^[[:alnum:]]([-_.]?[[:alnum:]])*@[[:alnum:]]([-.]?[[:alnum:]])*\.([a-z]{2,4})$`', $value)) {

				return true;
			}
			$this->_error('invalid_email');
			return false;
		}
		return false;
	}
}
?>
