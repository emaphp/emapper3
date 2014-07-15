<?php
namespace eMapper\PostgreSQL\Result\ObjectMapper;

use eMapper\PostgreSQL\PostgreSQLTest;
use eMapper\Result\Mapper\ObjectTypeMapper;
use eMapper\Engine\PostgreSQL\Type\PostgreSQLTypeManager;
use eMapper\Engine\PostgreSQL\Result\PostgreSQLResultIterator;

/**
 * Test the ObjectTypeMapper class with different results
 *
 * @author emaphp
 * @group result
 * @group postgre
 */
class ObjectMapperTest extends PostgreSQLTest {
	public function testRow() {
		$mapper = new ObjectTypeMapper(new PostgreSQLTypeManager(), null, 'Acme\Generic\GenericUser');
		$result = pg_query(self::$conn, "SELECT * FROM users WHERE user_id = 1");
		$user = $mapper->mapResult(new PostgreSQLResultIterator($result));
	
		$this->assertInstanceOf('Acme\Generic\GenericUser', $user);
	
		$this->assertInternalType('integer', $user->user_id);
		$this->assertEquals(1, $user->user_id);
	
		$this->assertInternalType('string', $user->user_name);
		$this->assertEquals('jdoe', $user->user_name);
	
		$this->assertInstanceOf('DateTime', $user->birth_date);
		$this->assertEquals('1987-08-10', $user->birth_date->format('Y-m-d'));
	
		$this->assertInternalType('string', $user->newsletter_time);
		$this->assertEquals('12:00:00', $user->newsletter_time);
	
		$this->assertInternalType('string', $user->avatar);
		$this->assertEquals(self::$blob, $user->avatar);
	
		pg_free_result($result);
	}
	
	public function testList() {
		$mapper = new ObjectTypeMapper(new PostgreSQLTypeManager(), null, 'Acme\Generic\GenericUser');
		$result = pg_query(self::$conn, "SELECT * FROM users ORDER BY user_id ASC");
		$users = $mapper->mapList(new PostgreSQLResultIterator($result));
	
		$this->assertInternalType('array', $users);
		$this->assertCount(5, $users);
		$this->assertArrayHasKey(0, $users);
		$this->assertArrayHasKey(1, $users);
		$this->assertArrayHasKey(2, $users);
		$this->assertArrayHasKey(3, $users);
		$this->assertArrayHasKey(4, $users);
	
		$user = $users[0];
		$this->assertInstanceOf('Acme\Generic\GenericUser', $user);
	
		$this->assertInternalType('integer', $user->user_id);
		$this->assertEquals(1, $user->user_id);
	
		$this->assertInternalType('string', $user->user_name);
		$this->assertEquals('jdoe', $user->user_name);
	
		$this->assertInstanceOf('DateTime', $user->birth_date);
		$this->assertEquals('1987-08-10', $user->birth_date->format('Y-m-d'));
	
		$this->assertInternalType('string', $user->newsletter_time);
		$this->assertEquals('12:00:00', $user->newsletter_time);
	
		$this->assertInternalType('string', $user->avatar);
		$this->assertEquals(self::$blob, $user->avatar);
	
		pg_free_result($result);
	}
	
	public function testIndexedList() {
		$mapper = new ObjectTypeMapper(new PostgreSQLTypeManager(), null, 'Acme\Generic\GenericUser');
		$result = pg_query(self::$conn, "SELECT * FROM users ORDER BY user_id ASC");
		$users = $mapper->mapList(new PostgreSQLResultIterator($result), 'user_id');
	
		$this->assertInternalType('array', $users);
		$this->assertCount(5, $users);
	
		$this->assertArrayHasKey(1, $users);
		$this->assertArrayHasKey(2, $users);
		$this->assertArrayHasKey(3, $users);
		$this->assertArrayHasKey(4, $users);
		$this->assertArrayHasKey(5, $users);
	
		$user = $users[1];
		$this->assertInstanceOf('Acme\Generic\GenericUser', $user);
	
		$this->assertInternalType('integer', $user->user_id);
		$this->assertEquals(1, $user->user_id);
	
		$this->assertInternalType('string', $user->user_name);
		$this->assertEquals('jdoe', $user->user_name);
	
		$this->assertInstanceOf('DateTime', $user->birth_date);
		$this->assertEquals('1987-08-10', $user->birth_date->format('Y-m-d'));
	
		$this->assertInternalType('string', $user->newsletter_time);
		$this->assertEquals('12:00:00', $user->newsletter_time);
	
		$this->assertInternalType('string', $user->avatar);
		$this->assertEquals(self::$blob, $user->avatar);
	
		pg_free_result($result);
	}
	
	public function testOverrideIndexList() {
		$mapper = new ObjectTypeMapper(new PostgreSQLTypeManager(), null, 'Acme\Generic\Product');
		$result = pg_query(self::$conn, "SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new PostgreSQLResultIterator($result), 'category');
	
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
	
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
	
		////
		$product = $products['Clothes'];
		$this->assertInstanceOf('Acme\Generic\Product', $product);
		$this->assertEquals(3, $product->product_id);
	
		////
		$product = $products['Hardware'];
		$this->assertInstanceOf('Acme\Generic\Product', $product);
		$this->assertEquals(4, $product->product_id);
	
		////
		$product = $products['Smartphones'];
		$this->assertInstanceOf('Acme\Generic\Product', $product);
		$this->assertEquals(5, $product->product_id);
	
		pg_free_result($result);
	}
	
	public function testCustomIndexList() {
		$mapper = new ObjectTypeMapper(new PostgreSQLTypeManager(), null, 'Acme\Generic\GenericUser');
		$result = pg_query(self::$conn, "SELECT * FROM users ORDER BY user_id ASC");
		$users = $mapper->mapList(new PostgreSQLResultIterator($result), 'user_id', 'string');
	
		$this->assertInternalType('array', $users);
		$this->assertCount(5, $users);
	
		$this->assertArrayHasKey('1', $users);
		$this->assertArrayHasKey('2', $users);
		$this->assertArrayHasKey('3', $users);
		$this->assertArrayHasKey('4', $users);
		$this->assertArrayHasKey('5', $users);
	
		$user = $users['1'];
		$this->assertInstanceOf('Acme\Generic\GenericUser', $user);
	
		$this->assertInternalType('integer', $user->user_id);
		$this->assertEquals(1, $user->user_id);
	
		$this->assertInternalType('string', $user->user_name);
		$this->assertEquals('jdoe', $user->user_name);
	
		$this->assertInstanceOf('DateTime', $user->birth_date);
		$this->assertEquals('1987-08-10', $user->birth_date->format('Y-m-d'));
	
		$this->assertInternalType('string', $user->newsletter_time);
		$this->assertEquals('12:00:00', $user->newsletter_time);
	
		$this->assertInternalType('string', $user->avatar);
		$this->assertEquals(self::$blob, $user->avatar);
	
		pg_free_result($result);
	}
	
	public function testGroupedList() {
		$mapper = new ObjectTypeMapper(new PostgreSQLTypeManager(), null, 'Acme\Generic\Product');
		$result = pg_query(self::$conn, "SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new PostgreSQLResultIterator($result), null, null, 'category');
	
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
	
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
	
		$this->assertInternalType('array', $products['Clothes']);
		$this->assertCount(3, $products['Clothes']);
		$this->assertInternalType('array', $products['Hardware']);
		$this->assertCount(1, $products['Hardware']);
		$this->assertInternalType('array', $products['Smartphones']);
		$this->assertCount(1, $products['Smartphones']);
	
		$this->assertArrayHasKey(0, $products['Clothes']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Clothes'][0]);
		$this->assertEquals(1, $products['Clothes'][0]->product_id);
		$this->assertArrayHasKey(1, $products['Clothes']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Clothes'][1]);
		$this->assertEquals(2, $products['Clothes'][1]->product_id);
		$this->assertArrayHasKey(2, $products['Clothes']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Clothes'][2]);
		$this->assertEquals(3, $products['Clothes'][2]->product_id);
		$this->assertArrayHasKey(0, $products['Hardware']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Hardware'][0]);
		$this->assertEquals(4, $products['Hardware'][0]->product_id);
		$this->assertArrayHasKey(0, $products['Smartphones']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Smartphones'][0]);
		$this->assertEquals(5, $products['Smartphones'][0]->product_id);
	
		$product = $products['Clothes'][0];
	
		$this->assertInternalType('integer', $product->product_id);
		$this->assertEquals(1, $product->product_id);
	
		$this->assertInternalType('string', $product->description);
		$this->assertEquals('Red dress', $product->description);
	
		$this->assertInternalType('string', $product->category);
		$this->assertEquals('Clothes', $product->category);
	
		pg_free_result($result);
	}
	
	public function testGroupedIndexedList() {
		$mapper = new ObjectTypeMapper(new PostgreSQLTypeManager(), null, 'Acme\Generic\Product');
		$result = pg_query(self::$conn, "SELECT * FROM products ORDER BY product_id ASC");
		$products = $mapper->mapList(new PostgreSQLResultIterator($result), 'product_id', null, 'category');
	
		$this->assertInternalType('array', $products);
		$this->assertCount(3, $products);
	
		$this->assertArrayHasKey('Clothes', $products);
		$this->assertArrayHasKey('Hardware', $products);
		$this->assertArrayHasKey('Smartphones', $products);
	
		$this->assertInternalType('array', $products['Clothes']);
		$this->assertCount(3, $products['Clothes']);
		$this->assertInternalType('array', $products['Hardware']);
		$this->assertCount(1, $products['Hardware']);
		$this->assertInternalType('array', $products['Smartphones']);
		$this->assertCount(1, $products['Smartphones']);
	
		$this->assertArrayHasKey(1, $products['Clothes']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Clothes'][1]);
		$this->assertEquals(1, $products['Clothes'][1]->product_id);
		$this->assertArrayHasKey(2, $products['Clothes']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Clothes'][2]);
		$this->assertEquals(2, $products['Clothes'][2]->product_id);
		$this->assertArrayHasKey(3, $products['Clothes']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Clothes'][3]);
		$this->assertEquals(3, $products['Clothes'][3]->product_id);
		$this->assertArrayHasKey(4, $products['Hardware']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Hardware'][4]);
		$this->assertEquals(4, $products['Hardware'][4]->product_id);
		$this->assertArrayHasKey(5, $products['Smartphones']);
		$this->assertInstanceOf('Acme\Generic\Product', $products['Smartphones'][5]);
		$this->assertEquals(5, $products['Smartphones'][5]->product_id);
	
		$product = $products['Clothes'][1];
	
		$this->assertInternalType('integer', $product->product_id);
		$this->assertEquals(1, $product->product_id);
	
		$this->assertInternalType('string', $product->description);
		$this->assertEquals('Red dress', $product->description);
	
		$this->assertInternalType('string', $product->category);
		$this->assertEquals('Clothes', $product->category);
	
		pg_free_result($result);
	}
}

?>