<?php
/**
 * Description of ListArray
 *
 * @author FFOZEU
 */
namespace Library\Classe\Form;

class ListArray implements \Iterator, \ArrayAccess {

	protected $array = array();
	private $valid = false;

	function __construct(Array $array = array()) {
		$this->array = $array;
	}

	/* Iterator */
	function rewind()  { $this->valid = (FALSE !== reset($this->array)); }
	function current() { return current($this->array);      }
	function key()     { return key($this->array);  }
	function next()    { $this->valid = (FALSE !== next($this->array));  }
	function valid()   { return $this->valid;  }

	/* ArrayAccess */
	public function offsetExists($offset) {
		return isset($this->array[$offset]);
	}
	public function offsetGet($offset) {
		return $this->array[$offset];
	}
	public function offsetSet($offset, $value) {
		return $this->array[$offset] = $value;
	}
	public function offsetUnset($offset) {
		unset($this->array[$offset]);
	}
}

?>
