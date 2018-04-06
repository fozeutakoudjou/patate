<?php

namespace core\constant\dao;

abstract class JoinType{
	const INNER = 1;
	const LEFT = 2;
	const RIGHT = 3;
	const FULL = 4;
}