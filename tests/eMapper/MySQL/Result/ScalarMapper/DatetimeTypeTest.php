<?php
namespace eMapper\MySQL\Result\ScalarMapper;

use eMapper\MySQL\MySQLTest;
use eMapper\Result\Mapper\ScalarTypeMapper;
use eMapper\Type\Handler\DatetimeTypeHandler;
use eMapper\Engine\MySQL\Result\MySQLResultInterface;

/**
 * Test ScalarTypeMapper with boolean values
 * @author emaphp
 * @group mysql
 * @group result
 * @group date
 */
class DatetimeTypeTest extends MySQLTest {
	public function testDatetime() {
		$mapper = new ScalarTypeMapper(new DatetimeTypeHandler(new \DateTimeZone('America/Argentina/Buenos_Aires')));
		$result = self::$conn->query("SELECT NOW()");
		$value = $mapper->mapResult(new MySQLResultInterface($result));
		$this->assertInstanceOf('DateTime', $value);
		$this->assertRegExp('/([\d]{4})-([\d]{2})-([\d]{2}) ([\d]{2}):([\d]{2}):([\d]{2})/', $value->format('Y-m-d H:i:s'));
	
		$result = self::$conn->query("SELECT last_login FROM users WHERE user_id = 1");
		$value = $mapper->mapResult(new MySQLResultInterface($result));
		$this->assertInstanceOf('DateTime', $value);
		$this->assertEquals('2013-08-10 19:57:15', $value->format('Y-m-d H:i:s'));
	
		$result = self::$conn->query("SELECT * FROM users WHERE user_id = 3");
		$value = $mapper->mapResult(new MySQLResultInterface($result), 'last_login');
		$this->assertInstanceOf('DateTime', $value);
		$this->assertEquals('2013-02-16 20:00:33', $value->format('Y-m-d H:i:s'));
	
		$result = self::$conn->query("SELECT last_login FROM users ORDER BY user_id ASC");
		$value = $mapper->mapList(new MySQLResultInterface($result));
		$this->assertInternalType('array', $value);
		$this->assertCount(5, $value);
	
		$values = array();
	
		foreach ($value as $dt) {
			$this->assertInstanceOf('DateTime', $dt);
			$values[] = $dt->format('Y-m-d H:i:s');
		}
	
		$this->assertEquals(array('2013-08-10 19:57:15', '2013-01-06 12:34:10', '2013-02-16 20:00:33', '2013-03-26 10:01:45', '2013-05-22 14:23:32'), $values);
	
		$result = self::$conn->query("SELECT * FROM users ORDER BY user_id DESC");
		$value = $mapper->mapList(new MySQLResultInterface($result), 'last_login');
		$this->assertInternalType('array', $value);
		$this->assertCount(5, $value);
	
		$values = array();
	
		foreach ($value as $dt) {
			$this->assertInstanceOf('DateTime', $dt);
			$values[] = $dt->format('Y-m-d H:i:s');
		}
	
		$this->assertEquals(array('2013-05-22 14:23:32', '2013-03-26 10:01:45', '2013-02-16 20:00:33', '2013-01-06 12:34:10', '2013-08-10 19:57:15'), $values);
	}
	
	public function testDatetimeColumn() {
		$mapper = new ScalarTypeMapper(new DatetimeTypeHandler(new \DateTimeZone('America/Argentina/Buenos_Aires')));
		$result = self::$conn->query("SELECT * FROM sales WHERE sale_id = 2");
		$value = $mapper->mapResult(new MySQLResultInterface($result), 'sale_date');
		
		$this->assertInstanceOf('\DateTime', $value);
		$this->assertEquals('2013-05-17 14:22:50', $value->format('Y-m-d H:i:s'));
		
		$result->free();
	}
	
	public function testDatetimeList() {
		$mapper = new ScalarTypeMapper(new DatetimeTypeHandler(new \DateTimeZone('America/Argentina/Buenos_Aires')));
		$result = self::$conn->query("SELECT birth_date FROM users ORDER BY user_id ASC");
		$values = $mapper->mapList(new MySQLResultInterface($result));
		
		$this->assertInternalType('array', $values);
		$this->assertCount(5, $values);
		
		$this->assertInstanceOf('\DateTime', $values[0]);
		$this->assertEquals('1987-08-10', $values[0]->format('Y-m-d'));
		
		$this->assertInstanceOf('\DateTime', $values[1]);
		$this->assertEquals('1976-03-03', $values[1]->format('Y-m-d'));
		
		$this->assertInstanceOf('\DateTime', $values[2]);
		$this->assertEquals('1967-11-21', $values[2]->format('Y-m-d'));
		
		$this->assertInstanceOf('\DateTime', $values[3]);
		$this->assertEquals('1980-12-07', $values[3]->format('Y-m-d'));
		
		$this->assertInstanceOf('\DateTime', $values[4]);
		$this->assertEquals('1977-03-16', $values[4]->format('Y-m-d'));
		
		$result->free();
	}
	
	public function testDatetimeColumnList() {
		$mapper = new ScalarTypeMapper(new DatetimeTypeHandler(new \DateTimeZone('America/Argentina/Buenos_Aires')));
		$result = self::$conn->query("SELECT * FROM users ORDER BY user_id ASC");
		$values = $mapper->mapList(new MySQLResultInterface($result), 'last_login');
		
		$this->assertInternalType('array', $values);
		$this->assertCount(5, $values);
		
		$this->assertInstanceOf('\DateTime', $values[0]);
		$this->assertEquals('2013-08-10 19:57:15', $values[0]->format('Y-m-d H:i:s'));
		
		$this->assertInstanceOf('\DateTime', $values[1]);
		$this->assertEquals('2013-01-06 12:34:10', $values[1]->format('Y-m-d H:i:s'));
		
		$this->assertInstanceOf('\DateTime', $values[2]);
		$this->assertEquals('2013-02-16 20:00:33', $values[2]->format('Y-m-d H:i:s'));
		
		$this->assertInstanceOf('\DateTime', $values[3]);
		$this->assertEquals('2013-03-26 10:01:45', $values[3]->format('Y-m-d H:i:s'));
		
		$this->assertInstanceOf('\DateTime', $values[4]);
		$this->assertEquals('2013-05-22 14:23:32', $values[4]->format('Y-m-d H:i:s'));
		
		
		$result->free();
	}
}

?>