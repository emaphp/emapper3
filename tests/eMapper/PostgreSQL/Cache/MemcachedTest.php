<?php
namespace eMapper\PostgreSQL\Cache;

use eMapper\PostgreSQL\PostgreSQLTest;
use eMapper\Cache\MemcachedProvider;
use eMapper\Mapper;

/**
 * Test MemcachedProvider with MySQLMapper class
 * @author emaphp
 * @group postgre
 * @group cache
 * @group memcached
 */
class MemcachedTest extends PostgreSQLTest {
	/**
	 * PostgreSQL mapper
	 * @var Mapper
	 */
	public $pgsql;
	
	/**
	 * Memcached provider
	 * @var MemcachedProvider
	 */
	public $provider;
	
	protected function setUp() {
		try {
			$this->provider = new MemcachedProvider();
			$this->provider->addServer('localhost', 11211);
		}
		catch (\RuntimeException $re) {
			$this->markTestSkipped(
					'The Memcached extension is not available.'
			);
		}
	
		$this->pgsql = new Mapper(self::$driver);
		$this->pgsql->setCacheProvider($this->provider);
	}
	
	public function testGetInteger() {
		$this->provider->delete('pgsql_get_integer');
	
		$this->provider->store('pgsql_get_integer', 100);
		$int = $this->pgsql->cache('pgsql_get_integer', 60)->type('integer')->query("SELECT 1 + 1");
		$this->assertEquals(100, $int);
	}
	
	public function testSetInteger() {
		$this->provider->delete('pgsql_set_integer');
	
		$this->pgsql->cache('pgsql_set_integer', 60)->type('integer')->query("SELECT 1 + 1");
		$this->assertTrue($this->provider->exists('pgsql_set_integer'));
		$this->assertEquals(2, $this->provider->fetch('pgsql_set_integer'));
	}
	
	public function testGetFloat() {
		$this->provider->delete('pgsql_get_float');
	
		$this->provider->store('pgsql_get_float', 10.5);
		$float = $this->pgsql->cache('pgsql_get_float', 60)->type('float')->query("SELECT price FROM products WHERE product_id = 1");
		$this->assertEquals(10.5, $float);
	}
	
	public function testSetFloat() {
		$this->provider->delete('pgsql_set_float');
	
		$this->pgsql->cache('pgsql_set_float', 60)->type('float')->query("SELECT price FROM products WHERE product_id = 1");
		$this->assertTrue($this->provider->exists('pgsql_set_float'));
		$this->assertEquals(150.65, $this->provider->fetch('pgsql_set_float'));
	}
	
	public function testGetString() {
		$this->provider->delete('pgsql_get_string');
	
		$this->provider->store('pgsql_get_string', "shawking");
		$float = $this->pgsql->cache('pgsql_get_string', 60)->type('string')->query("SELECT user_name FROM users WHERE user_id = 1");
		$this->assertEquals("shawking", $float);
	}
	
	public function testSetString() {
		$this->provider->delete('pgsql_set_string');
	
		$this->pgsql->cache('pgsql_set_string', 60)->type('string')->query("SELECT user_name FROM users WHERE user_id = 1");
		$this->assertTrue($this->provider->exists('pgsql_set_string'));
		$this->assertEquals("jdoe", $this->provider->fetch('pgsql_set_string'));
	}
	
	public function testGetArray() {
		$this->provider->delete('pgsql_get_array');
	
		$this->provider->store('pgsql_get_array', [1,2,3]);
		$value = $this->pgsql->cache('pgsql_get_array', 60)->type('array')->query("SELECT * FROM users WHERE user_id = 1");
		$this->assertEquals([1,2,3], $value);
	}
	
	public function testSetArray() {
		$this->provider->delete('pgsql_set_array');
	
		$this->pgsql->cache('pgsql_set_array', 60)->type('array')->query("SELECT * FROM users WHERE user_id = 1");
		$this->assertTrue($this->provider->exists('pgsql_set_array'));
		$value = $this->provider->fetch('pgsql_set_array');
	
		$this->assertInternalType('array', $value);
	
		$this->assertArrayHasKey('user_id', $value);
		$this->assertEquals(1, $value['user_id']);
	
		$this->assertArrayHasKey('user_name', $value);
		$this->assertEquals('jdoe', $value['user_name']);
	
		$this->assertArrayHasKey('birth_date', $value);
		$this->assertInstanceOf('DateTime', $value['birth_date']);
	
		$this->assertArrayHasKey('last_login', $value);
		$this->assertInstanceOf('DateTime', $value['last_login']);
	
		$this->assertArrayHasKey('newsletter_time', $value);
		$this->assertInternalType('string', $value['newsletter_time']);
	
		$this->assertArrayHasKey('avatar', $value);
		$this->assertEquals(self::$blob, $value['avatar']);
	}
	
	public function testSetArrayList() {
		$this->provider->delete('pgsql_set_arraylist');
	
		$this->pgsql->cache('pgsql_set_arraylist', 60)->type('array[user_id]')->query("SELECT * FROM users ORDER BY user_id ASC");
		$this->assertTrue($this->provider->exists('pgsql_set_arraylist'));
		$value = $this->provider->fetch('pgsql_set_arraylist');
	
		$this->assertInternalType('array', $value);
	
		$this->assertArrayHasKey(1, $value);
		$this->assertInternalType('array', $value[1]);
		$this->assertArrayHasKey('user_id', $value[1]);
		$this->assertEquals(1, $value[1]['user_id']);
	
		$this->assertArrayHasKey(2, $value);
		$this->assertInternalType('array', $value[2]);
		$this->assertArrayHasKey('user_id', $value[2]);
		$this->assertEquals(2, $value[2]['user_id']);
	
		$this->assertArrayHasKey(3, $value);
		$this->assertInternalType('array', $value[3]);
		$this->assertArrayHasKey('user_id', $value[3]);
		$this->assertEquals(3, $value[3]['user_id']);
	
		$this->assertArrayHasKey(4, $value);
		$this->assertInternalType('array', $value[4]);
		$this->assertArrayHasKey('user_id', $value[4]);
		$this->assertEquals(4, $value[4]['user_id']);
	
		$this->assertArrayHasKey(5, $value);
		$this->assertInternalType('array', $value[5]);
		$this->assertArrayHasKey('user_id', $value[5]);
		$this->assertEquals(5, $value[5]['user_id']);
	}
	
	public function testGetObject() {
		$obj = (object) ['name' => 'emaphp', 'email' => 'emaphp@github.com'];
	
		$this->provider->delete('pgsql_get_object');
		$this->provider->store('pgsql_get_object', $obj);
		$value = $this->pgsql->cache('pgsql_get_object', 60)->type('obj')->query("SELECT * FROM users WHERE user_id = 1");
		$this->assertEquals($obj, $value);
	}
	
	public function testSetObject() {
		$this->provider->delete('pgsql_set_object');
	
		$this->pgsql->cache('pgsql_set_object', 60)->type('object')->query("SELECT * FROM users WHERE user_id = 1");
		$this->assertTrue($this->provider->exists('pgsql_set_object'));
		$value = $this->provider->fetch('pgsql_set_object');
	
		$this->assertInstanceOf('stdClass', $value);
	
		$this->assertObjectHasAttribute('user_id', $value);
		$this->assertEquals(1, $value->user_id);
	
		$this->assertObjectHasAttribute('user_name', $value);
		$this->assertEquals('jdoe', $value->user_name);
	
		$this->assertObjectHasAttribute('birth_date', $value);
		$this->assertInstanceOf('DateTime', $value->birth_date);
	
		$this->assertObjectHasAttribute('last_login', $value);
		$this->assertInstanceOf('DateTime', $value->last_login);
	
		$this->assertObjectHasAttribute('newsletter_time', $value);
		$this->assertInternalType('string', $value->newsletter_time);
	
		$this->assertObjectHasAttribute('avatar', $value);
		$this->assertEquals(self::$blob, $value->avatar);
	}
	
	public function testSetObjectList() {
		$this->provider->delete('pgsql_set_objectlist');
	
		$this->pgsql->cache('pgsql_set_objectlist', 60)->type('object[user_id]')->query("SELECT * FROM users ORDER BY user_id ASC");
		$this->assertTrue($this->provider->exists('pgsql_set_objectlist'));
		$value = $this->provider->fetch('pgsql_set_objectlist');
	
		$this->assertInternalType('array', $value);
	
		$this->assertArrayHasKey(1, $value);
		$this->assertInstanceOf('stdClass', $value[1]);
		$this->assertObjectHasAttribute('user_id', $value[1]);
		$this->assertEquals(1, $value[1]->user_id);
	
		$this->assertArrayHasKey(2, $value);
		$this->assertInstanceOf('stdClass', $value[2]);
		$this->assertObjectHasAttribute('user_id', $value[2]);
		$this->assertEquals(2, $value[2]->user_id);
	
		$this->assertArrayHasKey(3, $value);
		$this->assertInstanceOf('stdClass', $value[3]);
		$this->assertObjectHasAttribute('user_id', $value[3]);
		$this->assertEquals(3, $value[3]->user_id);
	
		$this->assertArrayHasKey(4, $value);
		$this->assertInstanceOf('stdClass', $value[4]);
		$this->assertObjectHasAttribute('user_id', $value[4]);
		$this->assertEquals(4, $value[4]->user_id);
	
		$this->assertArrayHasKey(5, $value);
		$this->assertInstanceOf('stdClass', $value[5]);
		$this->assertObjectHasAttribute('user_id', $value[5]);
		$this->assertEquals(5, $value[5]->user_id);
	}
}
?>