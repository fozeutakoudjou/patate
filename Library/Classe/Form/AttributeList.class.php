<?php
/**
 * Description of AttributList
 *
 * @author FFOZEU
 */
namespace Library\Classe\Form;

class AttributeList extends ListArray {

	public function __toString() {

		$o = '';
		if (!empty($this->array)) {

			foreach($this->array as $a=>$v) {

				$o .= sprintf(' %s="%s"', $a, htmlspecialchars($v));
			}
		}
		return $o;
	}
}

?>
