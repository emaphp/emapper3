<?php
namespace eMapper\Type\Handler;

use eMapper\Type\TypeHandler;

/**
 * @Safe
 */
class BooleanTypeHandler extends TypeHandler {
	protected function cast_to_boolean($value) {
		if (is_string($value) && (strtolower($value) == 'f' || strtolower($value) == 'false'))
			return false;
	
		return (bool) $value;
	}
	
	public function getValue($value) {
		return $this->cast_to_boolean($value);
	}
	
	public function castParameter($parameter) {
		return $this->cast_to_boolean($parameter);
	}
	
	public function setParameter($parameter) {
		return ($parameter) ? 'TRUE' : 'FALSE';
	}
}