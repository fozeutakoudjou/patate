<?php
/**
 * Description of ErrorList
 *
 * @author FFOZEU
 */
namespace Library\Classe\Form;

class ErrorList extends ListArray {

	public function as_array() {

		return $this->array;
	}

	public function __toString() {

		$tab = func_num_args() > 0 ? func_get_arg(0) : '';
		
		if (!empty($this->array)) {

			return sprintf($tab."<ul class=\"error\">\n\t$tab<li>%s</li>\n$tab</ul>", implode("</li>\n\t$tab<li>", $this->array));
		}
		return '';
	}
}

?>
