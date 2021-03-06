<?php
namespace eMapper\SQL\Predicate;

use eMapper\Engine\Generic\Driver;

/**
 * The LessThan class defines a predicate for values less than than a given expression.
 * @author emaphp
 */
class LessThan extends ComparisonPredicate {
	public function generate(Driver $driver) {
		$op = $this->negate ? '>=' : '<';
		return "%s $op %s";
	}
	
	protected function buildComparisonExpression(Driver $driver) {
		return $this->negate ? '%s >= %s' : '%s < %s';
	}
}