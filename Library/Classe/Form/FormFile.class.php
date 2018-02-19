<?php

/**
 * Description of FormFile
 *
 * @author FFOZEU
 */
namespace Library\Classe\Form;

class FormFile extends FormInput {

	protected $extensions;
	protected $max_size;

	public function __construct($name, $form) {

		parent::__construct($name, $form);
		$this->attrs['type'] = 'file';
		$this->form->enctype('multipart/form-data');
		$this->extensions = array();
		$this->max_size = 0;
	}

	public function filter_extensions($extensions) {

		if (!is_array($extensions)) {

			$extensions = func_get_args();
		}
		$this->extensions = $extensions;

		return $this;
	}

	protected function _init() {

		if (!isset(self::$error_list['invalid_file_extension'])) {

			self::$error_list['invalid_file_extension'] = "Cette extension est interdite ! (sont autorisées : %s).";
		}
		if (!isset(self::$error_list['file_too_big'])) {

			self::$error_list['file_too_big'] = "Fichier trop volumineux ! (maximum : %d octets).";
		}
	}

	public function is_valid($value) {

		$name = $this->attrs['name'];

		if (isset($_FILES[$name])) {

			$value = isset($_FILES[$name]) ? $_FILES[$name]['name'] : null;

			if (parent::is_valid($value)) {

				if (!$this->required) {

					return true;
				}

				if (!empty($this->extensions)) {

					$ext = pathinfo($value, PATHINFO_EXTENSION);
					if (!in_array($ext, $this->extensions)) {

						$this->_error('invalid_file_extension');
						$valid = false;
					}
				}

				if (0 < $this->max_size && $this->max_size < $_FILES[$name]['size']) {

					$this->_error('file_too_big');
					$valid = false;
				}

				return ($_FILES[$name]['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES[$name]['tmp_name']));
			}
		}
		return false;
	}

	public function get_cleaned_value($value) {

		return isset($_FILES[$this->attrs['name']]) ? $_FILES[$this->attrs['name']]['tmp_name'] : null;
	}

	public function max_size($size) {

		$this->form->add('Hidden', 'POST_MAX_SIZE')->value($size);
		$this->max_size = $size;

		return $this;
	}

	protected function _custom_errors($id_error, $error) {

		if ('invalid_file_extension' == $id_error) {

			$this->error_messages[$id_error] = vsprintf($error, implode(', ', $this->extensions));
			return true;
		}
		if ('file_too_big' == $id_error) {

			$this->error_messages[$id_error] = vsprintf($error, implode(', ', $this->max_size));
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
			$label = '<label'.$for.'>'.$this->label.$this->form->label_suffix().'</label>'."\n$tab";
		}

		$errors = $this->error_messages->__toString($tab);
		if (!empty($errors)) { $errors = "\n".$errors; }

		$field = '<input'.$id.$this->attrs.' />';
		return $tab.sprintf("%2\$s%1\$s%3\$s", $field, $label, $errors);
	}
}
?>
