<?php
namespace eMapper\Result\Mapper;

use eMapper\Type\TypeManager;
use eMapper\Reflection\Profiler;
use eMapper\Type\TypeHandler;

abstract class ComplexTypeMapper {
	/**
	 * Type manager
	 * @var TypeManager
	 */
	public $typeManager;
	
	/**
	 * Result map
	 * @var string
	 */
	public $resultMap;
	
	/**
	 * An array containing all column types
	 * @var array
	 */
	protected $columnTypes;
	
	/**
	 * Property list
	 * @var array
	 */
	protected $propertyList;
	
	public function __construct(TypeManager $typeManager, $resultMap = null) {
		$this->typeManager = $typeManager;
		$this->resultMap = $resultMap;
	}
	
	/**
	 * Obtains a column default type handler
	 * @param mixed $column
	 * @return TypeHandler | FALSE
	 */
	protected function columnHandler($column) {
		return $this->typeManager->getTypeHandler($this->columnTypes[$column]);
	}
	
	/**
	 * Builds a result map property list
	 * @throws \UnexpectedValueException
	 */
	protected function validateResultMap() {
		$fields = Profiler::getClassProperties($this->resultMap);
		$this->propertyList = array();
		
		foreach ($fields as $name => $field) {
			//get column
			$column = $field->has('column') ? $field->get('column') : $name;
			
			if (!array_key_exists($column, $this->columnTypes)) {
				throw new \UnexpectedValueException("Column '$column' was not found on this result");
			}
			
			$this->propertyList[$name] = array('column' => $column);
			
			//get type handler
			if ($field->has('type')) {
				$type = $field->get('type');
				$typeHandler = $this->typeManager->getTypeHandler($type);
			
				if ($typeHandler === false) {
					throw new \UnexpectedValueException("No typehandler assigned to type '$type' defined at property $name");
				}
				
				$this->propertyList[$name]['handler'] = $typeHandler;
			}
			else {
				$this->propertyList[$name]['handler'] = $this->columnHandler($column);
			}
			
			//get setter method
			if ($field->has('setter')) {
				$this->propertyList[$name]['setter'] = $field->get('setter');
			}
			
			//get getter method
			if ($field->has('getter')) {
				$this->propertyList[$name]['getter'] = $field->get('getter');
			}
		}
		
		//validate setter methods and properties accesibility
		if ($this instanceof ObjectTypeMapper) {
			$reflectionClass = null;
			
			if (Profiler::isEntity($this->resultMap)) {
				$reflectionClass = Profiler::getReflectionClass($this->resultMap);
			}
			else {
				//obtain class from annotation
				$profile = Profiler::getClassProfile($this->resultMap);
				$defaultClass = $profile->has('defaultClass') ? $profile->get('defaultClass') : $this->defaultClass;
				
				if ($defaultClass != 'stdClass' && $defaultClass != 'ArrayObject') {
					$reflectionClass = Profiler::getReflectionClass($defaultClass);
				}
			}
			
			if (isset($reflectionClass)) {
				foreach ($this->propertyList as $name => $props) {
					if (array_key_exists('setter', $props)) {
						//validate setter method
						if (!$reflectionClass->hasMethod($setter)) {
							throw new \UnexpectedValueException(sprintf("Setter method $setter not found in class %s", $reflectionClass->getName()));
						}
							
						$method = $reflectionClass->getMethod($setter);
							
						if (!$method->isPublic()) {
							throw new \UnexpectedValueException(sprintf("Setter method $setter does not have public access in class %s", $reflectionClass->getName()));
						}
					}
					else {
						//validate property
						if (!$reflectionClass->hasProperty($name)) {
							throw new \UnexpectedValueException(sprintf("Unknown property $name in class %s", $reflectionClass->getName()));
						}
							
						$property = $reflectionClass->getProperty($name);
							
						if (!$property->isPublic()) {
							throw new \UnexpectedValueException(sprintf("Property $name does not public access in class %s", $reflectionClass->getName()));
						}
					}
				}
			}
		}
	}
}