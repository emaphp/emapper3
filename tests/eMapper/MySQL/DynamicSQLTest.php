<?php
namespace eMapper\MySQL;

use eMapper\Engine\MySQL\Statement\MySQLStatement;
use eMapper\Type\TypeManager;
use Acme\Entity\Product;

/**
 * 
 * @author emaphp
 * @group mysql
 */
class DynamicSQLTest extends MySQLTest {
	public $statement;
	
	public function __construct() {
		self::setUpBeforeClass();
		$this->statement = new MySQLStatement(self::$conn, new TypeManager(), null);
	}
	
	public function testSimpleValue() {
		$result = $this->statement->build('[[ null ]]', array(), self::$env_config);
		$this->assertEquals('', $result);
		
		//is this ok???
		$result = $this->statement->build('{{ null }}', array(), self::$env_config);
		$this->assertEquals('NULL', $result);
		
		$result = $this->statement->build('[[ 10 ]]', array(), self::$env_config);
		$this->assertEquals('10', $result);
		
		$result = $this->statement->build('{{ 10 }}', array(), self::$env_config);
		$this->assertEquals("'10'", $result);
		
		$result = $this->statement->build('[[ "test" ]]', array(), self::$env_config);
		$this->assertEquals('test', $result);
		
		$result = $this->statement->build('{{ "test" }}', array(), self::$env_config);
		$this->assertEquals("'test'", $result);
		
		$result = $this->statement->build('[[ (%0) ]]', array(100), self::$env_config);
		$this->assertEquals('100', $result);
		
		$result = $this->statement->build('{{ (%0) }}', array(100), self::$env_config);
		$this->assertEquals("'100'", $result);
		
		$result = $this->statement->build('[[ (%0) ]]', array("test"), self::$env_config);
		$this->assertEquals('test', $result);
		
		$result = $this->statement->build('{{ (%0) }}', array("test"), self::$env_config);
		$this->assertEquals("'test'", $result);
	}
	
	public function testSimpleArgument() {
		$result = $this->statement->build('[[ (if (int? (%0)) "%{i}" "%{s}") ]]', array(25), self::$env_config);
		$this->assertEquals(25, $result);
		
		$result = $this->statement->build('[[ (if (int? (%0)) "%{i}" "%{s}") ]]', array('joe'), self::$env_config);
		$this->assertEquals("'joe'", $result);
		
		$result = $this->statement->build('SELECT * FROM [[ (if (int? (%0)) "user_id = %{i}") ]]', array('joe'), self::$env_config);
		$this->assertEquals('SELECT * FROM ', $result);
		
		$result = $this->statement->build('SELECT * FROM users WHERE [[ (if (int? (%0)) "user_id = %{i}" "user_name = %{s}") ]]', array(25), self::$env_config);
		$this->assertEquals("SELECT * FROM users WHERE user_id = 25", $result);
		
		$result = $this->statement->build('SELECT * FROM users WHERE [[ (if (string? (%0)) "user_name = %{s}" "user_id = %{i}") ]]', array('joe'), self::$env_config);
		$this->assertEquals("SELECT * FROM users WHERE user_name = 'joe'", $result);
		
		$result = $this->statement->build('ORDER BY [[ (. (%0) " " (strtoupper (%1))) ]]', array('name', 'desc'), self::$env_config);
		$this->assertEquals("ORDER BY name DESC", $result);
	}
	
	public function testComplexArgument() {
		//array
		$result = $this->statement->build('ORDER BY [[ (. (if (#order_field?) (#order_field) "user_name") " " (if (#order_type?) (strtoupper (#order_type)) "DESC")) ]]', array(['order_field' => 'user_id']), self::$env_config);
		$this->assertEquals("ORDER BY user_id DESC", $result);
		
		$result = $this->statement->build('ORDER BY [[ (. (if (#order_field?) (#order_field) "user_name") " " (if (#order_type?) (strtoupper (#order_type)) "DESC"))]]', array(['order_field' => 'user_id', 'order_type' => 'asc']), self::$env_config);
		$this->assertEquals("ORDER BY user_id ASC", $result);
		
		//ArrayObject
		$result = $this->statement->build('ORDER BY [[ (. (if (#order_field?) (#order_field) "user_name") " " (if (#order_type?) (strtoupper (#order_type)) "DESC")) ]]', array(new \ArrayObject(['order_field' => 'user_id'])), self::$env_config);
		$this->assertEquals("ORDER BY user_id DESC", $result);
		
		$result = $this->statement->build('ORDER BY [[ (. (if (#order_field?) (#order_field) "user_name") " " (if (#order_type?) (strtoupper (#order_type)) "DESC"))]]', array(new \ArrayObject(['order_field' => 'user_id', 'order_type' => 'asc'])), self::$env_config);
		$this->assertEquals("ORDER BY user_id ASC", $result);
		
		//stdClass
		$order = new \stdClass();
		$order->field = 'user_id';
		$result = $this->statement->build('ORDER BY [[ (. (if (#field?) (#field) "user_name") " " (if (#type?) (strtoupper (#type)) "DESC")) ]]', array($order), self::$env_config);
		$this->assertEquals("ORDER BY user_id DESC", $result);
		
		$order->type = 'asc';
		$result = $this->statement->build('ORDER BY [[ (. (if (#field?) (#field) "user_name") " " (if (#type?) (strtoupper (#type)) "DESC"))]]', array($order), self::$env_config);
		$this->assertEquals("ORDER BY user_id ASC", $result);
		
		//entity
		$product = new Product();
		$product->code = 'ZXY321';
		$product->setCategory('Clothes');
		
		$result = $this->statement->build('WHERE [[ (if (#code?) "product_code = #{code}" "1") ]]', array($product), self::$env_config);
		$this->assertEquals("WHERE product_code = 'ZXY321'", $result);
		
		$result = $this->statement->build('WHERE [[ (if (#category?) "category = #{category}" "1") ]]', array($product), self::$env_config);
		$this->assertEquals("WHERE category = 'Clothes'", $result);
	}
}
?>