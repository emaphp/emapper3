<?php
namespace eMapper\Dynamic\Runtime;

use eMacros\Applicable;
use eMacros\Scope;
use eMacros\GenericList;
use eMapper\Reflection\Parameter\ParameterWrapper;

class PropertyGet implements Applicable {
	/**
	 * Property to obtain
	 * @var mixed
	 */
	protected $property;
	
	public function __construct($property = null) {
		$this->property = $property;
	}
	
	public function apply(Scope $scope, GenericList $arguments) {
		$useParameterMap = false;
		
		//get property and value
		if (is_null($this->property)) {
			if (empty($arguments)) {
				throw new \BadFunctionCallException("PropertyGet: No parameters found.");
			}
			
			$property = $arguments[0]->evaluate($scope);
			
			if (count($arguments) == 1) {
				if (!array_key_exists(0, $scope->arguments)) {
					throw new \BadFunctionCallException("PropertyGet: Expected value of type array/object as second parameter but none found.");
				}
				
				$value = $scope->arguments[0];
				$useParameterMap = true;
			}
			else {
				$value = $arguments[1]->evaluate($scope);
			}
		}
		else {
			$property = $this->property;
			
			if (count($arguments) == 0) {
				if (!array_key_exists(0, $scope->arguments)) {
					throw new \BadFunctionCallException("PropertyGet: Expected value of type array/object as first parameter but none found.");
				}
				
				$value = $scope->arguments[0];
				$useParameterMap = true;
			}
			else {
				$value = $arguments[0]->evaluate($scope);
			}
		}
		
		//check value type
		if (!is_array($value) && !is_object($value)) {
			throw new \InvalidArgumentException(sprintf("PropertyGet: Expected value of type array/object but %s found instead", gettype($value)));
		}
		
		//wrap argument using the parameter map (if any)
		if ($useParameterMap && $scope->hasConfig('map.parameter')) {
			$value = ParameterWrapper::wrapValue($value, $scope->getConfig('map.parameter'));
		}
		else {
			$value = ParameterWrapper::wrapValue($value);
		}
		
		if (!$value->offsetExists($property)) {
			throw new \InvalidArgumentException(sprintf("PropertyGet: Property '%s' not found.", strval($property)));
		}
		
		return $value->offsetGet($property);
	}
}
?>