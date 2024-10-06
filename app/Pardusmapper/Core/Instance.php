<?php
declare(strict_types=1);

namespace Pardusmapper\Core;

trait Instance {
	private $args = [];

	public static function getInstance(array $args = []) {
		static $aoInstance = [];

		$calledClassName = static::class;

		$argsSerialized = serialize($args);

		if (!isset($aoInstance[$calledClassName])) {
			$aoInstance[$calledClassName] = [];
		}

		if (!isset($aoInstance[$calledClassName][$argsSerialized])) {
			$newObject = new $calledClassName($args);
			$newObject->args = $args;

			$aoInstance[$calledClassName][0] = $aoInstance[$calledClassName][$argsSerialized] = $newObject;
		}

		if (is_array($args) && count($args) === 0) {
			return $aoInstance[$calledClassName][0];
		} else {
			return $aoInstance[$calledClassName][$argsSerialized];
		}
	}
}