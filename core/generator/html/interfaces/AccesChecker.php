<?php
namespace core\generator\html\interfaces;
interface AccesChecker{
	public function checkUserAccess($action, $idWrapper = null);
}