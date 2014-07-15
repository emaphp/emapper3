<?php
namespace eMapper\Query\Predicate;

use eMapper\Engine\Generic\Driver;

class LessThan extends ComparisonPredicate {	
	protected function buildComparisonExpression(Driver $driver) {
		 if ($this->negate) {
		 	return '%s >= %s';
		 }
		 
		 return '%s < %s';
	}
}
?>