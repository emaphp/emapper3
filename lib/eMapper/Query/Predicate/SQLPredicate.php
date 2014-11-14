<?php
namespace eMapper\Query\Predicate;

use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Engine\Generic\Driver;
use eMapper\Query\Field;
use eMapper\Query\Attr;
use eMapper\Query\SQL\ColumnTranslator;
use eMapper\Query\SQL\ORMTranslator;

/**
 * A SQLPredicate class encapsulates the generic behaviour defined for query conditional clauses.
 * @author emaphp
 */
abstract class SQLPredicate {
	/**
	 * Predicate field
	 * @var Field
	 */
	protected $field;
	
	/**
	 * Indicates if the predicate must be negated
	 * @var boolean
	 */
	protected $negate;
	
	/**
	 * Target table alias
	 * @var string
	 */
	protected $alias;
	
	/**
	 * Argument counter (used for additional parameters in query)
	 * @var integer
	 */
	protected static $counter = 0;
	
	public function __construct(Field $field, $negate = false) {
		$this->field = $field;
		$this->negate = $negate;
	}
	
	public function setAlias($alias) {
		$this->alias = $alias;
	}
	
	public function getField() {
		return $this->field;
	}
	
	public function getNegate() {
		return $this->negate;
	}
	
	/**
	 * Obtains an index for the current argument
	 * @param integer $arg_index
	 * @return number|string
	 */
	protected static function getArgumentIndex($arg_index) {
		if ($arg_index != 0)
			return self::$counter++;
	
		return 'arg' . self::$counter++;
	}
	
	/**
	 * Obtains the current field type
	 * @param ClassProfile $profile
	 * @return string|NULL
	 */
	protected function getFieldType(ClassProfile $profile) {
		if ($this->field->hasType())
			return $this->field->getType();
		
		if ($this->field instanceof Attr) {
			$property = $profile->getProperty($this->field->getName());
			
			if ($property === false)
				return null;
			
			return $property->getType();
		}
		
		return null;
	}
	
	/**
	 * Builds an argument expression for the current query
	 * @param ClassProfile $profile
	 * @param mixed $index Argument relative index
	 * @param unknown $arg_index Argument global index
	 * @return string
	 */
	protected function buildArgumentExpression(ColumnTranslator $translator, $index, $arg_index) {
		$profile = $translator instanceof ORMTranslator ? $translator->getProfile() : null;
		
		if ($arg_index != 0) {
			//check type
			$type = is_null($profile) ? null : $this->getFieldType($profile);
				
			//build expression
			if (isset($type))
				return '%{' . $arg_index . "[$index]" . ":$type}";
				
			return '%{' . $arg_index . "[$index]" . '}';
		}
	
		//check type
		$type = is_null($profile) ? null : $this->getFieldType($profile);
	
		//build expression
		if (isset($type))
			return '#{' . "$index:$type" . '}';
	
		return '#{' . $index . '}';
	}
	
	/**
	 * Evaluates a SQLPredicate getting any additional arguments
	 * @param ColumnTranslator $translator
	 * @param Driver $driver
	 * @param ArrayObject $args
	 * @param ArrayObject $joins
	 * @param int $arg_index
	 * @return string
	 */
	public abstract function evaluate(ColumnTranslator $translator, Driver $driver, \ArrayObject &$args, \ArrayObject &$joins = null, $arg_index = 0);
	
	/**
	 * Renders a SQLPredicate to the corresponding Dynamic SQL expression
	 * @param Driver $driver
	 */
	public abstract function render(Driver $driver);
}
?>