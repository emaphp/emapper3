<?php
namespace eMapper\SQL\Fluent\Clause;

/**
 * The WhereClause class is an abstraction of the sql WHERE clause
 * @author emaphp
 */
class WhereClause extends ArgumentClause {
	public function getName() {
		return 'WHERE';
	}
}
?>