<?php
namespace eMapper\PostgreSQL\Mapper\ScalarMapper;

use eMapper\PostgreSQL\PostgreSQLTest;

/**
 * Tests Mapper class with integer values
 * @author emaphp
 * @group postgre
 * @group mapper
 * @group integer
 */
class IntegerTypeTest extends PostgreSQLTest {
	public function testInteger() {
		$value = self::$mapper->type('integer')->query("SELECT 2");
		$this->assertEquals(2, $value);
	
		$value = self::$mapper->type('int')->query("SELECT user_id FROM users WHERE user_name = 'jkirk'");
		$this->assertEquals(3, $value);
	
		$value = self::$mapper->type('i')->query("SELECT * FROM users WHERE user_name = 'ishmael'");
		$this->assertEquals(5, $value);
	
		$result = self::$mapper->type('i')->query("SELECT birth_date FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(1987, $result);
	
		$result = self::$mapper->type('i')->query("SELECT last_login FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(2013, $result);
	
		$result = self::$mapper->type('i')->query("SELECT newsletter_time FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(12, $result);
	
		$result = self::$mapper->type('i')->query("SELECT price FROM products WHERE product_id = 1");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(150, $result);
	
		$result = self::$mapper->type('i')->query("SELECT rating FROM products WHERE product_id = 1");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(4, $result);
	
		$result = self::$mapper->type('i')->query("SELECT refurbished FROM products WHERE product_id = 1");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(0, $result);
	
		$result = self::$mapper->type('i')->query("SELECT manufacture_year FROM products WHERE product_id = 1");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(2011, $result);
	
		$result = self::$mapper->type('i')->query("SELECT discount FROM sales WHERE sale_id = 1");
		$this->assertInternalType('integer', $result);
		$this->assertEquals(0, $result);
	}
	
	public function testIntegerColumn() {
		$value = self::$mapper->type('integer', 'product_id')->query("SELECT * FROM sales WHERE sale_id = 1");
	
		$this->assertInternalType('integer', $value);
		$this->assertEquals(5, $value);
	}
	
	public function testIntegerList() {
		$values = self::$mapper->type('integer[]')->query("SELECT user_id FROM sales ORDER BY sale_id ASC");
	
		$this->assertInternalType('array', $values);
		$this->assertCount(4, $values);
	
		$this->assertEquals(1, $values[0]);
		$this->assertEquals(5, $values[1]);
		$this->assertEquals(2, $values[2]);
		$this->assertEquals(3, $values[3]);
	}
	
	public function testIntegerColumnList() {
		$values = self::$mapper->type('integer[]', 'user_id')->query("SELECT * FROM sales ORDER BY sale_id ASC");
	
		$this->assertInternalType('array', $values);
		$this->assertCount(4, $values);
	
		$this->assertEquals(1, $values[0]);
		$this->assertEquals(5, $values[1]);
		$this->assertEquals(2, $values[2]);
		$this->assertEquals(3, $values[3]);
	}
}

?>