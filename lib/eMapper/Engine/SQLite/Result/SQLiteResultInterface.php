<?php
namespace eMapper\Engine\SQLite\Result;

use eMapper\Result\ResultInterface;

class SQLiteResultInterface extends ResultInterface {
	public $resultTypes = array(self::BOTH => SQLITE3_BOTH, self::ASSOC => SQLITE3_ASSOC, self::NUM => SQLITE3_NUM);
	public $numRows;
	
	/* (non-PHPdoc)
	 * @see \eMapper\Result\ResultInterface::countRows()
	 */
	public function countRows() {
		if (is_null($this->numRows)) {
			for ($this->numRows = 0; $this->result->fetchArray(); $this->numRows++) {
			}
			
			$this->result->reset();
		}
		
		return $this->numRows;
	}
	
	public function columnTypes($resultType = self::ASSOC) {
		$num_columns = $this->result->numColumns();
		$types = array();
		
		for ($i = 0; $i < $num_columns; $i++) {
			$name = $this->result->columnName($i);
			
			switch ($this->result->columnType($i)) {
				case SQLITE3_INTEGER:
					$type = 'integer';
					break;
					
				case SQLITE3_FLOAT:
					$type = 'float';
					break;
					
				case SQLITE3_BLOB:
					$type = 'blob';
					break;
					
				case SQLITE3_NULL:
					//For some reason columType does not return an useful value
					//Instead, always returns SQLITE3_NULL, which at the end produces bad indexation an a lot of other issues
					//In order to avoid this, all values use 'string' as a default type
					//$type = 'null';
					//break;
					
				case SQLITE3_TEXT:
				default:
					$type = 'string';
					break;
			}
			
			//store type
			if ($resultType & self::NUM) {
				$types[$i] = $type;
			}
			
			if ($resultType & self::ASSOC) {
				$types[$name] = $type;
			}
		}
		
		return $types;
	}

	public function fetchArray($resultType = self::BOTH) {
		return $this->result->fetchArray($this->resultTypes[$resultType]);
	}
	
	/* (non-PHPdoc)
	 * @see \eMapper\Result\ResultInterface::fetchObject()
	 */
	public function fetchObject($className = null) {
		// TODO: Auto-generated method stub
		return (object) $this->result->fetchArray(SQLITE3_ASSOC);
	}

}
?>