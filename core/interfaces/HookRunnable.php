<?php
namespace core\interfaces;
interface HookRunnable{
	public function onHookCall($code, $params);
}