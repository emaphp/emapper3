<?php
namespace eMapper\Engine\MySQL;

use eMapper\Engine\Generic\Driver;
use eMapper\Engine\MySQL\Type\MySQLTypeManager;
use eMapper\Engine\MySQL\Statement\MySQLStatement;
use eMapper\Engine\MySQL\Result\MySQLResultIterator;
use eMapper\Engine\MySQL\Exception\MySQLException;
use eMapper\Engine\MySQL\Exception\MySQLConnectionException;
use eMapper\Engine\MySQL\Exception\MySQLQueryException;
use eMapper\Engine\MySQL\Regex\MySQLRegex;
use eMapper\Type\TypeManager;
use eMapper\Engine\MySQL\Procedure\MySQLStoredProcedure;
use eMapper\Mapper;

/**
 * The MySQLDriver class provides access to MySQL database engines.
 * @author emaphp
 */
class MySQLDriver extends Driver {
	public function __construct($database, $host = null, $user = null, $password = null, $port = null, $socket = null, $charset = 'UTF-8', $autocommit = true) {
		parent::__construct();
		
		if ($database instanceof \mysqli)
			$this->connection = $database;
		else {
			if (empty($database))
				throw new \InvalidArgumentException("Invalid database specified");
				
			//initialize configuration
			$this->config['database'] = $database;
				
			if (isset($host)) {
				if (!is_string($host) || empty($host))
					throw new \InvalidArgumentException("Invalid host specified");
					
				$this->config['host'] = $host;
			}
				
			if (isset($user)) {
				if (!is_string($user) || empty($user))
					throw new \InvalidArgumentException("Invalid user specified");
					
				$this->config['user'] = $user;
			}
			
			//allow empty passwords for testing
			if (isset($password))
				$this->config['password'] = (string) $password;
				
			if (isset($port)) {
				if (!is_string($port) || !is_integer($port) || empty($port))
					throw new \InvalidArgumentException("Invalid port specified");
					
				$this->config['port'] = strval($port);
			}
				
			if (isset($socket)) {
				if (!is_string($socket) || empty($socket))
					throw new \InvalidArgumentException("Invalid socket specified");
					
				$this->config['socket'] = $socket;
			}
				
			if (isset($charset)) {
				if (!is_string($charset) || empty($charset))
					throw new \InvalidArgumentException("Invalid charset specified");
					
				$this->config['charset'] = $charset;
			}
				
			//aet autocommit option
			$this->config['autocommit'] = (bool) $autocommit;
			
			//build regex
			$this->regex = new MySQLRegex();
		}
	}
	
	public static function build($config) {
		if (!is_array($config))
			throw new \InvalidArgumentException("Static method 'build' expects an array as first argument");
		
		//validate database filename
		if (!array_key_exists('database', $config))
			throw new \InvalidArgumentException("Configuration value 'database' not found");
		
		$database = $config['database'];
		$host = array_key_exists('host', $config) ? $config['host'] : null;
		$username = array_key_exists('username', $config) ? $config['username'] : null;
		$password = array_key_exists('password', $config) ? $config['password'] : null;
		$port = array_key_exists('port', $config) ? $config['port'] : null;
		$socket = array_key_exists('socket', $config) ? $config['socket'] : null;
		$charset = array_key_exists('charset', $config) ? $config['charset'] : null;
		$autocommit = array_key_exists('autocommit', $config) ? $config['autocommit'] : null;
		
		return new static($database, $host, $username, $password, $port, $socket, $charset, $autocommit);
	}
	
	/*
	 * CONNECTION METHODS
	 */
	
	public function connect() {
		//check if connection is already opened
		if ($this->connection instanceof \mysqli)
			return $this->connection;
		
		//get connection values
		$database = $this->config['database'];
		$host     = array_key_exists('host', $this->config) ? $this->config['host'] : ini_get("mysqli.default_host");
		$user     = array_key_exists('user', $this->config) ? $this->config['user'] : ini_get("mysqli.default_user");
		$password = array_key_exists('password', $this->config) ? $this->config['password'] : ini_get("mysqli.default_pw");
		$port     = array_key_exists('port', $this->config) ? $this->config['port'] : ini_get("mysqli.default_port");
		$socket   = array_key_exists('socket', $this->config) ? $this->config['socket'] : ini_get("mysqli.default_socket");
		
		//open connection
		$mysqli = @mysqli_connect($host, $user, $password, $database, $port, $socket);
		
		if (!($mysqli instanceof \mysqli))
			throw new MySQLConnectionException(mysqli_connect_error() . '(' . mysqli_connect_errno() . ')');
		
		//set autocommit
		$mysqli->autocommit($this->config['autocommit']);
		
		//set charset
		if (array_key_exists('charset', $this->config))
			$mysqli->set_charset($this->config['charset']);
		
		//store open connection
		return $this->connection = $mysqli;
	}
	
	public function query($query) {
		return $this->connection->query($query);
	}
	
	public function freeResult($result) {
		//free result
		if ($result instanceof \mysqli_result)
			$result->free();
		
		//free additional results
		while ($this->connection->more_results() && $this->connection->next_result()) {
			$result = $this->connection->use_result();
		
			if ($result instanceof \mysqli_result)
				$result->free();
		}
	}
	
	public function close() {
		if ($this->connection instanceof \mysqli)
			return $this->connection->close();
	}
	
	public function getLastError() {
		if (!($this->connection instanceof \mysqli))
			throw new MySQLException("No valid MySQL connection available");
	
		return mysqli_error($this->connection);
	}
	
	public function getLastId() {
		if (!($this->connection instanceof \mysqli))
			throw new MySQLException("No valid MySQL connection available");
	
		return $this->connection->insert_id;
	}
	
	/*
	 * TRANSACTION METHODS
	 */
	
	public function begin() {
		if (!($this->connection instanceof \mysqli))
			throw new MySQLException("No valid MySQL connection available");
	
		if (version_compare(PHP_VERSION, '5.5.0') >= 0)
			return $this->connection->begin_transaction();
		
		return $this->connection->query("START TRANSACTION");
	}
	
	public function commit() {
		if (!($this->connection instanceof \mysqli))
			throw new MySQLException("No valid MySQL connection available");
	
		return $this->connection->commit();
	}
	
	public function rollback() {
		if (!($this->connection instanceof \mysqli))
			throw new MySQLException("No valid MySQL connection available");
	
		return $this->connection->rollback();
	}
	
	/*
	 * BUILDER METHODS
	 */
	
	public function buildTypeManager() {
		return new MySQLTypeManager();
	}
	
	public function buildStatement(TypeManager $typeManager) {
		return new MySQLStatement($this, $typeManager);
	}
	
	public function buildResultIterator($result) {
		return new MySQLResultIterator($result);
	}

	public function buildProcedureCall(Mapper $mapper, $procedure) {
		return new MySQLStoredProcedure($mapper, $procedure);
	}
	
	/*
	 * EXCEPTION METHODS
	 */
	
	public function throwQueryException($query) {
		throw new MySQLQueryException(mysqli_error($this->connection), $query);
	}
}