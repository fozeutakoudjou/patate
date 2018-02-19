<?php
/**
 * Description of FormInput
 *
 * @author FFOZEU
 */
namespace Library\Classe\Form;

abstract class FormInput extends FormField {

	public function __construct($name, $form) {

		parent::__construct($name, $form);
		$this->attrs['type'] = 'text';
	}

	public function get_cleaned_value($value) {

		$value = trim($value);
		return parent::get_cleaned_value($value);
	}

	// abstract public function __toString($tab = '');
}

?>
