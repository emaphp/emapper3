<?php
namespace eMapper\Query;

use eMapper\Reflection\Profiler;
use eMapper\Reflection\ClassProfile;
use eMapper\SQL\Predicate\Equal;
use eMapper\SQL\Predicate\Contains;
use eMapper\SQL\Predicate\In;
use eMapper\SQL\Predicate\GreaterThan;
use eMapper\SQL\Predicate\GreaterThanEqual;
use eMapper\SQL\Predicate\LessThan;
use eMapper\SQL\Predicate\LessThanEqual;
use eMapper\SQL\Predicate\StartsWith;
use eMapper\SQL\Predicate\EndsWith;
use eMapper\SQL\Predicate\Range;
use eMapper\SQL\Predicate\Regex;
use eMapper\SQL\Predicate\IsNull;
use eMapper\SQL\Builder\AssociationJoin;

/**
 * The Field class represents an entity attribute or table column.
 * @author emaphp
 */
abstract class Field {
	/**
	 * Field name
	 * @var string
	 */
	protected $name;
	
	/**
	 * Field associated type
	 * @var string
	 */
	protected $columnType;
	
	/**
	 * Column alias
	 * @var string
	 */
	protected $columnAlias;
	
	/**
	 * Field path
	 * @var array:string
	 */
	protected $path;
	
	public function __construct($name, $type = null) {
		if (strstr($name, '__')) {
			//path stores the associated field path
			//assoc1__name => ['assoc1'], 'name'
			//assoc1__assoc2__name => ['assoc1', 'assoc2'], 'name'
			$this->path = explode('__', $name);
			$this->name = array_pop($this->path);
		}
		else
			$this->name = $name;
		 
		$this->columnType = $type;
	}
		
	/**
	 * Obtains the referenced column of this field
	 * @param \eMapper\Reflection\ClassProfile $profile
	 * @return string
	 */
	public abstract function getColumnName(ClassProfile $profile);
	
	/**
	 * Obtains field's name
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Determines if the field has an associated type
	 * @return boolean
	 */
	public function hasType() {
		return isset($this->columnType);
	}
	
	/**
	 * Obtains current field type
	 * @return string
	 */
	public function getType() {
		return $this->columnType;
	}
	
	/**
	 * Obtains column path
	 * @return array
	 */
	public function getPath() {
		return $this->path;
	}
	
	/**
	 * Obtains field full path
	 * @return NULL | string
	 */
	public function getStringPath() {
		if (is_null($this->path))
			return null;
		return implode('_', $this->path);
	}
		
	/**
	 * Obtains column alias
	 * @return string
	 */
	public function getColumnAlias() {
		return $this->columnAlias;
	}
	
	/**
	 * Sets the field alias
	 * @param string $alias
	 * @return \eMapper\Query\Field
	 */
	public function alias($alias) {
		$this->columnAlias = $alias;
		return $this;
	}
	
	/**
	 * Sets field type
	 * @param string $type
	 * @return \eMapper\Query\Field
	 */
	public function type($type) {
		$this->columnType = $type;
		return $this;
	}
	
	/*
	 * PREDICATES
	 */
	
	/**
	 * Returns an Equal predicate for the current field
	 * @param mixed $expression
	 * @param boolean $condition
	 * @return \eMapper\SQL\Predicate\Equal
	 */
	public function eq($expression, $condition = true) {
		return new Equal($this, !$condition, $expression);
	}
	
	/**
	 * Return a Contains predicate for the current field
	 * @param string $expression
	 * @param boolean $condition
	 * @return \eMapper\SQL\Predicate\Contains
	 */
	public function contains($expression, $condition = true) {
		return new Contains($this, true, !$condition, $expression);
	}
	
	/**
	 * Returns an case-insentive Contains predicate for the current field
	 * @param string $expression
	 * @param boolean $condition
	 * @return \eMapper\SQL\Predicate\Contains
	 */
	public function icontains($expression, $condition = true) {
		return new Contains($this, false, !$condition, $expression);
	}
	
	/**
	 * Returns an In predicate for the current field
	 * @param array $expression
	 * @param boolean $condition
	 * @return \eMapper\SQL\Predicate\In
	 */
	public function in($expression, $condition = true) {
		return new In($this, !$condition, $expression);
	}
	
	/**
	 * Returns a GreaterThan predicate for the current field
	 * @param mixed $expression
	 * @param boolean $condition
	 * @return \eMapper\SQL\Predicate\GreaterThan
	 */
	public function gt($expression, $condition = true) {
		return new GreaterThan($this, !$condition, $expression);
	}
	
	/**
	 * Returns a GreaterThanEqual predicate for the current field
	 * @param mixed $expression
	 * @param boolean $condition
	 * @return \eMapper\SQL\Predicate\GreaterThanEqual
	 */
	public function gte($expression, $condition = true) {
		return new GreaterThanEqual($this, !$condition, $expression);
	}
	
	/**
	 * Returns a LessThan predicate for the current field
	 * @param mixed $expression
	 * @param boolean $condition
	 * @return \eMapper\SQL\Predicate\LessThan
	 */
	public function lt($expression, $condition = true) {
		return new LessThan($this, !$condition, $expression);
	}
	
	/**
	 * Returns a LessThanEqual predicate for the current field
	 * @param mixed $expression
	 * @param boolean $condition
	 * @return \eMapper\SQL\Predicate\LessThanEqual
	 */
	public function lte($expression, $condition = true) {
		return new LessThanEqual($this, !$condition, $expression);
	}
	
	/**
	 * Returns a StartsWith predicate for the current field
	 * @param string $expression
	 * @param boolean $condition
	 * @return \eMapper\SQL\Predicate\StartsWith
	 */
	public function startswith($expression, $condition = true) {
		return new StartsWith($this, true, !$condition, $expression);
	}
	
	/**
	 * Returns a case-insensitive StartsWith predicate for the current field
	 * @param string $expression
	 * @param boolean $condition
	 * @return \eMapper\SQL\Predicate\StartsWith
	 */
	public function istartswith($expression, $condition = true) {
		return new StartsWith($this, false, !$condition, $expression);
	}
	
	/**
	 * Returns a EndsWith predicate for the current field
	 * @param string $expression
	 * @param boolean $condition
	 * @return \eMapper\SQL\Predicate\EndsWith
	 */
	public function endswith($expression, $condition = true) {
		return new EndsWith($this, true, !$condition, $expression);
	}
	
	/**
	 * Returns a case-insensitive EndsWith predicate for the current field
	 * @param string $expression
	 * @param boolean $condition
	 * @return \eMapper\SQL\Predicate\EndsWith
	 */
	public function iendswith($expression, $condition = true) {
		return new EndsWith($this, false, !$condition, $expression);
	}
	
	/**
	 * Returns a Range predicate for the current field
	 * @param mixed $from
	 * @param mixed $to
	 * @param boolean $condition
	 * @return \eMapper\SQL\Predicate\Range
	 */
	public function range($from, $to, $condition = true) {
		$range = new Range($this, !$condition);
		$range->setFrom($from);
		$range->setTo($to);
		return $range;
	}
	
	/**
	 * Returns a Regex predicate for the current field
	 * @param string $expression
	 * @param boolean $condition
	 * @return \eMapper\SQL\Predicate\Regex
	 */
	public function matches($expression, $condition = true) {
		return new Regex($this, true, !$condition, $expression);
	}
	
	/**
	 * Returns a case-insensitive Regex predicate for the current field
	 * @param string $expression
	 * @param boolean $condition
	 * @return \eMapper\SQL\Predicate\Regex
	 */
	public function imatches($expression, $condition = true) {
		return new Regex($this, false, !$condition, $expression);
	}
	
	/**
	 * Returns a IsNull predicate for the current field
	 * @param string $condition
	 * @return \eMapper\SQL\Predicate\IsNull
	 */
	public function isnull($condition = true) {
		return new IsNull($this, !$condition);
	}
}