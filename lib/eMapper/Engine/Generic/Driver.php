<?php
namespace eMapper\Engine\Generic;

use eMapper\Configuration\Configuration;

abstract class Driver {
	use Configuration;
	
	/**
	 * Database connection
	 * @var mixed
	 */
	public $connection;
	
	/**
	 * Builds a driver from a configuration array
	 * @param unknown $config
	 */
	public static abstract function build($config);
	
	/**
	 * Connects to database
	 */
	public abstract function connect();
	
	/**
	 * Executes a query and returns a result
	 * @param string $query
	 */
	public abstract function query($query);
	
	/**
	 * Frees a result
	 * @param mixed $result
	 */
	public abstract function free_result($result);
	
	/**
	 * Closes a database connection
	 */
	public abstract function close();
	
	/**
	 * Obtains last generated error message
	 */
	public abstract function get_last_error();
	
	/**
	 * Obtains last generated id
	 */
	public abstract function get_last_id();
	
	/**
	 * Begins a transaction
	 */
	public abstract function begin();
	
	/**
	 * Commits current transaction
	 */
	public abstract function commit();
	
	/**
	 * Rollbacks current transaction
	 */
	public abstract function rollback();
	
	/**
	 * Returns a TypeManager instance for current engine
	 */
	public abstract function build_type_manager();
	
	/**
	 * Returns a statement instance for current engine
	 * @param TypeManager $typeManager
	 * @param object $parameterMap
	 */
	public abstract function build_statement($typeManager, $parameterMap);
	
	/**
	 * Returns a result iterator for the given result
	 * @param mixed $result
	 */
	public abstract function build_result_iterator($result);
	
	/**
	 * Builds a procedure call
	 * @param string $procedure
	 * @param array $tokens
	 * @param array $config
	 */
	public abstract function build_call($procedure, $tokens, $config);
	
	/**
	 * Throws a generic exception
	 * @param string $message
	 * @param \Exception $previous
	 */
	public abstract function throw_exception($message, \Exception $previous = null);
	
	/**
	 * Throws a query exception
	 * @param string $query
	 */
	public abstract function throw_query_exception($query);
}
?>