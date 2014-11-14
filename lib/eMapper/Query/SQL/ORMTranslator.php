<?php
namespace eMapper\Query\SQL;

use eMapper\Query\Field;
use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Query\Predicate\SQLPredicate;
use eMapper\Query\Attr;

class ORMTranslator implements ColumnTranslator {
	protected $profile;
	
	public function __construct(ClassProfile $profile) {
		$this->profile = $profile;
	}
	
	public function translate(Field $field, \ArrayObject $joins = null, $alias = null) {
		static $counter = 0;
		list($associations, $target) = $field->getAssociations($this->profile);

		if (is_null($associations))
			return empty($alias) ? $field->getColumnName($this->profile) : $alias . '.' . $field->getColumnName($this->profile);
		
		//append joins if required
		$diff = array_keys(array_diff_key($associations, $joins->getArrayCopy()));
		
		foreach ($diff as $key) {
			$join = $associations[$key];
				
			//build table alias
			$join->setAlias('_t' . $counter++);
				
			//set join parent (if any)
			$parent = $join->getParentName();
				
			if (!is_null($parent))
				$join->setParent($joins[$parent]);
				
			//add join to list
			$joins[$key] = $join;
		}
		
		$alias = $joins[$field->getFullPath()]->getAlias();
		$field = Attr::__callstatic($field->getName());
		return $alias . '.' . $field->getColumnName($target);
	}
	
	public function getProfile() {
		return $this->profile;
	}
}
?>