<?php
namespace eMapper\SQL\Predicate;

use eMapper\Engine\Generic\Driver;

/**
 * The LessThanEqual class defines a predicate for values less than or equal than
 * a given expression.
 * @author emaphp
 */
class LessThanEqual extends ComparisonPredicate {
	public function render(Driver $driver) {
		$op = $this->negate ? '>' : '<=';
		return "%s $op %s";
	}
	
	protected function buildComparisonExpression(Driver $driver) {
		 if ($this->negate)
		 	return '%s > %s';
		 return '%s <= %s';
	}
}
?>