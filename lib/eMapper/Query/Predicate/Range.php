<?php
namespace eMapper\Query\Predicate;

use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Query\Field;
use eMapper\Engine\Generic\Driver;
use eMapper\Query\SQL\ColumnTranslator;

/**
 * The Range class defines a predicate for values between the specified range.
 * @author emaphp
 */
class Range extends SQLPredicate {
	/**
	 * From expression
	 * @var mixed
	 */
	protected $from;
	
	/**
	 * To expression
	 * @var mixed
	 */
	protected $to;
	
	public function __construct(Field $field, $negate) {
		parent::__construct($field, $negate);
	}
	
	public function setFrom($from) {
		$this->from = $from;
	}
	
	public function getFrom() {
		return $this->from;
	}
	
	public function setTo($to) {
		$this->to = $to;
	}
	
	public function getTo() {
		return $this->to;
	}

	public function evaluate(ColumnTranslator $translator, Driver $driver, \ArrayObject &$args, \ArrayObject &$joins = null, $arg_index = 0) {
		$column = $translator->translate($this->field, $joins, $this->alias);
		
		//add from argument
		$from_index = $this->getArgumentIndex($arg_index);
		$args[$from_index] = $this->from;
		$from_expression = $this->buildArgumentExpression($translator, $from_index, $arg_index);
		
		//add to argument
		$to_index = $this->getArgumentIndex($arg_index);
		$args[$to_index] = $this->to;
		$to_expression = $this->buildArgumentExpression($translator, $to_index, $arg_index);
		
		if ($this->negate)
			return sprintf("%s NOT BETWEEN %s AND %s", $column, $from_expression, $to_expression);
		
		return sprintf("%s BETWEEN %s AND %s", $column, $from_expression, $to_expression);
	}
	
	public function render(Driver $driver) {
		if ($this->negate)
			return "%s NOT BETWEEN %s AND %s";
		
		return "%s BETWEEN %s AND %s";
	}
}
?>