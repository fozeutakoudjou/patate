<?php

/**
 * Description of FormHidden
 *
 * @author FFOZEU
 */
namespace Library\Classe\Form;

class FormHidden extends FormInput {

	public function __construct($name, $form) {

		parent::__construct($name, $form);
		$this->attrs['type'] = 'hidden';
	}

	public function __toString() {

		$tab = func_num_args() > 0 ? func_get_arg(0) : '';
		list($for, $id) = self::_generate_for_id($this->form->auto_id(), $this->attrs['name']);
		return $tab.'<input'.$id.$this->attrs.' value="'.htmlspecialchars($this->value).'" />';
	}
}

?>
