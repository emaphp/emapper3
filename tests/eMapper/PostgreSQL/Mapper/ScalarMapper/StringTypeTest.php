<?php
namespace eMapper\PostgreSQL\Mapper\ScalarMapper;

use eMapper\PostgreSQL\PostgreSQLConfig;
use eMapper\Mapper\ScalarMapper\AbstractStringTypeTest;

/**
 * Tests Mapper class with string values
 * @author emaphp
 * @group postgre
 * @group mapper
 * @group string
 */
class StringTypeTest extends AbstractStringTypeTest {
	use PostgreSQLConfig;
	
	public function testString() {
		$value = $this->mapper->type('string')->query("SELECT 'hello'");
		$this->assertEquals('hello', $value);
	
		$value = $this->mapper->type('str')->query("SELECT user_name FROM users WHERE user_id = 3");
		$this->assertEquals('jkirk', $value);
	
		$value = $this->mapper->type('s', 'user_name')->query("SELECT * FROM users WHERE user_id = 5");
		$this->assertEquals('ishmael', $value);
	
		$result = $this->mapper->type('s')->query("SELECT user_id FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('string', $result);
		$this->assertEquals('1', $result);
	
		$result = $this->mapper->type('s')->query("SELECT birth_date FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('string', $result);
		$this->assertEquals('1987-08-10', $result);
	
		$result = $this->mapper->type('s')->query("SELECT last_login FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('string', $result);
		$this->assertEquals('2013-08-10 19:57:15', $result);
	
		$result = $this->mapper->type('s')->query("SELECT newsletter_time FROM users WHERE user_name = 'jdoe'");
		$this->assertInternalType('string', $result);
		$this->assertEquals('12:00:00', $result);
			
		$result = $this->mapper->type('s')->query("SELECT price FROM products WHERE product_id = 1");
		$this->assertInternalType('string', $result);
		$this->assertEquals('150.65', $result);
	
		$result = $this->mapper->type('s')->query("SELECT rating FROM products WHERE product_id = 1");
		$this->assertInternalType('string', $result);
		$this->assertEquals('4.5', $result);
	
		$result = $this->mapper->type('s')->query("SELECT refurbished FROM products WHERE product_id = 1");
		$this->assertInternalType('string', $result);
		$this->assertEquals('f', $result);
	
		$result = $this->mapper->type('s')->query("SELECT manufacture_year FROM products WHERE product_id = 1");
		$this->assertInternalType('string', $result);
		$this->assertEquals('2011', $result);
	
		$result = $this->mapper->type('s')->query("SELECT discount FROM sales WHERE sale_id = 1");
		$this->assertInternalType('string', $result);
		$this->assertEquals('0.25', $result);
	}
}
?>