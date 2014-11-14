<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;
use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Query\Field;
use eMapper\Query\SQL\ColumnTranslator;

/**
 * The ComparisonPredicate class adds an expression property which is used for comparison.
 * @author emaphp
 */
abstract class ComparisonPredicate extends SQLPredicate {
	/**
	 * Exprossion for comparison
	 * @var mixed
	 */
	protected $expression;
		
	public function setExpression($expression) {
		$this->expression = $expression;
	}
	
	public function getExpression() {
		return $this->expression;
	}
	
	public function render(Driver $driver) {
		$op = $this->negate ? "NOT" : "";
		$eq_op = $this->negate ? '<>' : '=';
		return "%s [? (if (null? (%%0)) 'IS $op NULL' '$eq_op %s') ?]";
	}
	
	public function evaluate(ColumnTranslator $translator, Driver $driver, \ArrayObject &$args, \ArrayObject &$joins = null, $arg_index = 0) {
		$column = $translator->translate($this->field, $joins, $this->alias);

		if ($this->expression instanceof Field)
			$expression = $translator->translate($this->expression, $joins, $this->alias);
		else {
			//store expression in argument list
			$index = $this->getArgumentIndex($arg_index);
			$args[$index] = $this->formatExpression($driver, $this->expression);
			
			//build expression
			$expression = $this->buildArgumentExpression($translator, $index, $arg_index);
		}
	
		//build predicate expression
		return sprintf($this->buildComparisonExpression($driver), $column, $expression);
	}
	
	/**
	 * Formats a expression for the current comparison predicate
	 * @param Driver $driver
	 * @param mixed $expression
	 * @return mixed
	 */
	protected function formatExpression(Driver $driver, $expression) {
		return $expression;
	}
	
	/**
	 * Obtains a string expression containing the comparison predicate
	 * @param Driver $driver
	 */
	protected abstract function buildComparisonExpression(Driver $driver);
}
?>