<?php
namespace eMapper\Callback;

use eMapper\MapperTest;

abstract class AbstractIndexCallbackTest extends MapperTest {
	public function testClosureIndex() {
		$list = $this->mapper->indexCallback(function ($user) {
			return intval($user->birth_date->format('Y'));
		})
		->type('obj[]')->query("SELECT * FROM users");
	
		$this->assertInternalType('array', $list);
		$this->assertCount(5, $list);
	
		//assert indexes
		$this->assertArrayHasKey(1987, $list);
		$this->assertArrayHasKey(1976, $list);
		$this->assertArrayHasKey(1967, $list);
		$this->assertArrayHasKey(1980, $list);
		$this->assertArrayHasKey(1977, $list);
	
		//assert values
		$this->assertInstanceOf('stdClass', $list[1987]);
		$this->assertInstanceOf('stdClass', $list[1976]);
		$this->assertInstanceOf('stdClass', $list[1967]);
		$this->assertInstanceOf('stdClass', $list[1980]);
		$this->assertInstanceOf('stdClass', $list[1977]);
	
		$this->assertEquals(1, $list[1987]->user_id);
		$this->assertEquals(2, $list[1976]->user_id);
		$this->assertEquals(3, $list[1967]->user_id);
		$this->assertEquals(4, $list[1980]->user_id);
		$this->assertEquals(5, $list[1977]->user_id);
	}
	
	public function createIndex($product) {
		return strstr($product['description'], ' ', true);
	}
	
	public function testMethodIndex() {
		$list = $this->mapper->indexCallback([$this, 'createIndex'])->type('arr[]')->query("SELECT * FROM products");
	
		$this->assertInternalType('array', $list);
		$this->assertCount(7, $list);
	
		//assert indexes
		$this->assertArrayHasKey('Red', $list);
		$this->assertArrayHasKey('Blue', $list);
		$this->assertArrayHasKey('Green', $list);
		$this->assertArrayHasKey('ATI', $list);
		$this->assertArrayHasKey('Android', $list);
		$this->assertArrayHasKey('Notebook', $list);
		$this->assertArrayHasKey('Apple', $list);
	
		//assert values
		$this->assertInternalType('array', $list['Red']);
		$this->assertInternalType('array', $list['Blue']);
		$this->assertInternalType('array', $list['Green']);
		$this->assertInternalType('array', $list['ATI']);
		$this->assertInternalType('array', $list['Android']);
		$this->assertInternalType('array', $list['Notebook']);
		$this->assertInternalType('array', $list['Apple']);
	
		$this->assertEquals(8, $list['Red']['product_id']);
		$this->assertEquals(2, $list['Blue']['product_id']);
		$this->assertEquals(3, $list['Green']['product_id']);
		$this->assertEquals(4, $list['ATI']['product_id']);
		$this->assertEquals(5, $list['Android']['product_id']);
		$this->assertEquals(6, $list['Notebook']['product_id']);
		$this->assertEquals(7, $list['Apple']['product_id']);
	}
}
?>