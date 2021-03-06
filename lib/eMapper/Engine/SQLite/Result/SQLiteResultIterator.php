<?php
namespace eMapper\Engine\SQLite\Result;

use eMapper\Engine\Generic\Result\ResultIterator;
use eMapper\Result\ArrayType;

/**
 * The SQLiteResultIterator class provides an iterator interface for SQLite results.
 * @author emaphp
 */
class SQLiteResultIterator extends ResultIterator {
	/**
	 * Array result types
	 * @var array
	 */
	protected $resultTypes = [ArrayType::BOTH => SQLITE3_BOTH, ArrayType::ASSOC => SQLITE3_ASSOC, ArrayType::NUM => SQLITE3_NUM];
	
	/**
	 * Total rows
	 * @var int
	 */
	protected $numRows;
	
	public function countRows() {
		if (is_null($this->numRows)) {
			for ($this->numRows = 0; $this->result->fetchArray(); $this->numRows++) {
			}
			
			$this->result->reset();
		}
		
		return $this->numRows;
	}
	
	public function getColumnTypes($resultType = ArrayType::ASSOC) {
		$num_columns = $this->result->numColumns();
		$types = [];
		
		for ($i = 0; $i < $num_columns; $i++) {
			$name = $this->result->columnName($i);
			
			switch ($this->result->columnType($i)) {
				default:
					//For some reason columType does not return an useful value
					//Instead, always returns SQLITE3_NULL, which at the end produces bad indexation an a lot of other issues
					//In order to avoid this, all values use 'default' as a default type
					//This type handler just returns the value without further processing
					$type = 'default';
			}
			
			//store type
			if ($resultType & ArrayType::NUM)
				$types[$i] = $type;
			
			if ($resultType & ArrayType::ASSOC)
				$types[$name] = $type;

		}
		
		return $types;
	}

	public function fetchArray($resultType = ArrayType::BOTH) {
		return $this->result->fetchArray($this->resultTypes[$resultType]);
	}
	
	public function fetchObject() {
		return (object) $this->result->fetchArray(SQLITE3_ASSOC);
	}
	
	public function free() {
		$this->result->finalize();
	}
}