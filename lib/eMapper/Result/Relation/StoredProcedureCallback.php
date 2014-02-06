<?php
namespace eMapper\Result\Relation;

use eMapper\Result\Argument\PropertyReader;
use eMapper\Reflection\Parameter\ParameterWrapper;

class StoredProcedureCallback extends DynamicAttribute {
	/**
	 * Stored procedure name
	 * @var string
	 */
	public $procedure;
	
	public function __construct($name, $attribute) {
		parent::__construct($name, $attribute);
		
		//obtain procedure name
		$this->procedure = $attribute->get('procedure');
	}
	
	protected function evaluateArgs($row, $parameterMap) {
		$args = array();
		$wrapper = ParameterWrapper::wrap($row, $parameterMap);
	
		foreach ($this->args as $arg) {
			if ($arg instanceof PropertyReader) {
				$args[] = $wrapper[$arg->property];
			}
			else {
				$args[] = $arg;
			}
		}
	
		return $args;
	}
	
	public function evaluate($row, $parameterMap, $mapper) {
		//evaluate condition
		if ($this->checkCondition($row, $parameterMap, $mapper->config) === false) {
			return null;
		}
		
		//build argument list
		$args = $this->evaluateArgs($row, $parameterMap);
		
		//merge mapper configuration
		$this->mergeConfig($mapper->config);
		
		//call stored procedure
		return call_user_func(array($mapper->merge($this->config), '__call'), $this->procedure, $args);
	}
}
?>