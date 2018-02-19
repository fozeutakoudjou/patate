<?php
/**
 * Description of FormCheckbox
 *
 * @author FFOZEU
 */
namespace Library\Classe\Form;

class FormCheckbox extends FormInput {

	public function __construct($name, $form) {

		parent::__construct($name, $form);
		$this->attrs['type'] = 'checkbox';
	}

	protected function _init() {

		if (!isset(self::$error_list['incorrect_value'])) {

			self::$error_list['incorrect_value'] = "La valeur fournie est interdite.";
		}
	}

	public function is_valid($value) {

		if (parent::is_valid($value)) {

			if ($this->required && !empty($this->value) && $value != $this->value) {

				$this->_error('incorrect_value');
				return false;
			}
			return true;
		}
		return false;
	}

	public function __toString() {

		$tab = func_num_args() > 0 ? func_get_arg(0) : '';
		
		$this->_generate_class();

		$id = '';
		$label = '';
		if (!empty($this->label)) {

			list($for, $id) = self::_generate_for_id($this->form->auto_id(), $this->attrs['name']);
			$label = "\n$tab".'<label'.$for.'>'.$this->label.'</label>';
		}
		$errors = $this->error_messages->__toString($tab);
		if (!empty($errors)) { $errors = "\n".$errors; }

		$value = (!empty($this->value)) ? ' value="'.htmlspecialchars($this->value).'"' : '';
		$checked = ($this->value == $this->form->get_bounded_data($this->attrs['name'])) ? ' checked="checked"' : '';

		$field = '<input'.$id.$this->attrs.$value.$checked.' />';
		return $tab.sprintf("%1\$s%2\$s%3\$s", $field, $label, $errors);
	}
}

?>
