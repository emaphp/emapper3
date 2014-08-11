<?php
namespace eMapper\Query\Aggregate;

use eMapper\Reflection\Profile\ClassProfile;

/**
 * The SQLMax class represents the generic MAX aggregate function.
 * @author emaphp
 */
class SQLMax extends SQLFunction {
	public function getExpression(ClassProfile $profile) {
		return sprintf("MAX(%s)", $this->field->getColumnName($profile));
	}
}
?>