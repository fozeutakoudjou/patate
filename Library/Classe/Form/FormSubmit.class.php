<?php
/**
 * Description of FormSubmit
 *
 * @author FFOZEU
 */
namespace Library\Classe\Form;

class FormSubmit extends FormInput {

	public function __construct($name, $form) {

		parent::__construct($name, $form);
		$this->attrs['type'] = 'submit';
	}

	public function __toString() {

		$tab = func_num_args() > 0 ? func_get_arg(0) : '';
		

		$this->_generate_class();

		// Pas d'auto_id pour les champs Submit...
		$label = (!empty($this->label)) ? '<label>'.$this->label.$this->form->label_suffix().'</label>'."\n$tab" : '';
		$value = empty($this->value) ? '' : ' value="'.$this->value.'"';

		$field = '<input'.$this->attrs.$value.' />';
		return $tab.sprintf("%2\$s%1\$s", $field, $label);
	}
}

?>
