<?php

namespace core\constant\dao;

abstract class Operator{
	const EQUALS = 1;
	const CONTAINS = 2;
	const START_WITH = 3;
	const END_WITH = 4;
	const DIFFERENT = 5;
	const BETWEEN = 6;
	const IN_LIST = 7;
	const NOT_IN_LIST = 8;
}