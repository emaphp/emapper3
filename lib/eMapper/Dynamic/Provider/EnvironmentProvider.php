<?php
namespace eMapper\Dynamic\Provider;

use eMacros\Package\Package;

abstract class EnvironmentProvider {
	/**
	 * Environment list
	 * @var array
	 */
	public static $environments = array();
	
	/**
	 * Determines if the given environment has been created
	 * @param string $id
	 * @return boolean
	 */
	public static function hasEnvironment($id) {
		return array_key_exists($id, self::$environments);
	}
	
	/**
	 * Obtains a dynamic SQL environment instance by ID
	 * @param string $id
	 * @throws \InvalidArgumentException
	 */
	public static function getEnvironment($id) {
		if (!array_key_exists($id, self::$environments)) {
			throw new \InvalidArgumentException("Environment with ID $id does not exists");
		}
		
		return self::$environments[$id];
	}
	
	/**
	 * Generates a new environment
	 * @param string $id
	 * @param string $classname
	 * @param mixed $import
	 * @throws \InvalidArgumentException
	 * @return boolean
	 */
	public static function buildEnvironment($id, $classname, $import) {
		//validate id
		if (!is_string($id) || empty($id)) {
			throw new \InvalidArgumentException("Environment id must be defined as a valid string");
		}
		
		//validate class name
		if (!is_string($classname) || empty($classname)) {
			throw new \InvalidArgumentException("Parameter is not a valid environment class");
		}
		elseif (!class_exists($classname, true)) {
			throw new \InvalidArgumentException("Environment class $classname was not found");
		}
			
		$rc = new \ReflectionClass($classname);
		
		//validate if class is a valid environment
		if (!$rc->isSubclassOf('eMacros\Environment\Environment') && $classname != 'eMacros\Environment\Environment') {
			throw new \InvalidArgumentException("Class $classname is not a valid environment class");
		}
			
		self::$environments[$id] = new $classname();
			
		//import auxiliary packages
		if (is_array($import) && !empty($import)) {
			for ($i = 0; $i < count($import); $i++) {
				$package_class = $import[$i];
					
				if (!class_exists($package_class, true)) {
					throw new \InvalidArgumentException("Package class '$package_class' not found");
				}
					
				$rc = new \ReflectionClass($package_class);
					
				if (!$rc->isSubclassOf('eMacros\Package\Package')) {
					throw new \InvalidArgumentException("Class '$package_class' is not a valid package class");
				}
					
				$package = new $package_class;
					
				if (!self::$environments[$id]->hasPackage($package->id)) {
					self::$environments[$id]->import($package);
				}
			}
		}
		
		return true;
	}
}
?>