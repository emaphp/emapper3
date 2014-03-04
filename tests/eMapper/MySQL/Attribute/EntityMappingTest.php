<?php
namespace eMapper\MySQL\Attribute;

use eMapper\MySQL\MySQLTest;

/**
 * Test dynamic attributes on entities
 * 
 * @author emaphp
 * @group attribute
 * @group mysql
 */
class EntityMappingTest extends MySQLTest {
	public function testMacroAttribute() {
		$user = self::$mapper->type('obj:Acme\Result\Attribute\User')->query("SELECT * FROM users WHERE user_id = 1");
		$this->assertInstanceOf('Acme\Result\Attribute\User', $user);
		
		//id
		$this->assertInternalType('integer', $user->id);
		$this->assertEquals(1, $user->id);
		
		//name
		$this->assertInternalType('string', $user->user_name);
		$this->assertEquals('jdoe', $user->user_name);
		
		//birthDate
		$this->assertInstanceOf('DateTime', $user->getBirthDate());
		$this->assertEquals('1987-08-10', $user->getBirthDate()->format('Y-m-d'));
		
		/**
		 * DYNAMIC ATTRIBUTES
		 */
		//uppercase_name
		$this->assertInternalType('string', $user->uppercase_name);
		$this->assertEquals('JDOE', $user->uppercase_name);
		
		//fakeId
		$this->assertInternalType('integer', $user->fakeId);
		$this->assertEquals(6, $user->fakeId);
		
		//age
		$this->assertInternalType('string', $user->age);
		$this->assertEquals('26', $user->age);
	}
	
	public function testQueryAttribute() {
		$sale = self::$mapper->type('obj:Acme\Result\Attribute\Sale')->query("SELECT * FROM sales WHERE sale_id = 2");
		$this->assertInstanceOf('Acme\Result\Attribute\Sale', $sale);
		
		//productId
		$this->assertInternalType('integer', $sale->productId);
		$this->assertEquals(2, $sale->productId);
		
		//userId
		$this->assertInternalType('integer', $sale->userId);
		$this->assertEquals(5, $sale->userId);
		
		/**
		 * DYNAMIC ATTRIBUTES
		 */
		//product
		$this->assertInstanceOf('stdClass', $sale->product);
		$this->assertObjectHasAttribute('product_id', $sale->product);
		$this->assertEquals(2, $sale->product->product_id);
		$this->assertObjectHasAttribute('product_code', $sale->product);
		$this->assertEquals('IND00043', $sale->product->product_code);
		$this->assertObjectHasAttribute('description', $sale->product);
		$this->assertEquals('Blue jeans', $sale->product->description);
		$this->assertObjectHasAttribute('color', $sale->product);
		$this->assertEquals('0c1bd9', $sale->product->color);
		$this->assertObjectHasAttribute('price', $sale->product);
		$this->assertEquals(235.7, $sale->product->price);
		$this->assertObjectHasAttribute('category', $sale->product);
		$this->assertEquals('Clothes', $sale->product->category);
		$this->assertObjectHasAttribute('rating', $sale->product);
		$this->assertEquals(3.9, $sale->product->rating);
		$this->assertObjectHasAttribute('refurbished', $sale->product);
		$this->assertEquals(0, $sale->product->refurbished);
		$this->assertObjectHasAttribute('manufacture_year', $sale->product);
		$this->assertEquals('2012', $sale->product->manufacture_year);
		
		//user
		$this->assertInstanceOf('Acme\Result\Attribute\User', $sale->user);
		$this->assertEquals('ishmael', $sale->user->user_name);
		$this->assertEquals(5, $sale->user->id);
		$this->assertInstanceOf('DateTime', $sale->user->getBirthDate());
		$this->assertEquals('1977-03-16', $sale->user->getBirthDate()->format('Y-m-d'));
		$this->assertEquals('ISHMAEL', $sale->user->uppercase_name);
		$this->assertEquals(10, $sale->user->fakeId);
		$this->assertEquals('36', $sale->user->age);
	}
	
	public function testStatementAttribute() {
		$sale = self::$mapper->type('obj:Acme\Result\Attribute\ExtraSale')->query("SELECT * FROM sales WHERE sale_id = 3");
		$this->assertInstanceOf('Acme\Result\Attribute\ExtraSale', $sale);
		
		//productId
		$this->assertInternalType('integer', $sale->productId);
		$this->assertEquals(4, $sale->productId);
		
		//userId
		$this->assertInternalType('integer', $sale->userId);
		$this->assertEquals(2, $sale->userId);
		
		//product
		$this->assertInternalType('array', $sale->product);
		$this->assertEquals(4, $sale->product['product_id']);
		$this->assertEquals('GFX00067', $sale->product['product_code']);
		$this->assertEquals('ATI HD 9999', $sale->product['description']);
		$this->assertEquals(null, $sale->product['color']);
		$this->assertEquals(120.75, $sale->product['price']);
		$this->assertEquals('Hardware', $sale->product['category']);
		$this->assertEquals(3.8, $sale->product['rating']);
		$this->assertEquals(0, $sale->product['refurbished']);
		$this->assertEquals(2013, $sale->product['manufacture_year']);
		
		//user
		$this->assertInternalType('object', $sale->user);
		$this->assertInstanceOf('stdClass', $sale->user);
		$this->assertEquals(2, $sale->user->user_id);
		$this->assertEquals('okenobi', $sale->user->user_name);
		$this->assertInstanceOf('DateTime', $sale->user->birth_date);
		$this->assertEquals('1976-03-03', $sale->user->birth_date->format('Y-m-d'));
		$this->assertInstanceOf('DateTime', $sale->user->last_login);
		$this->assertEquals('2013-01-06 12:34:10', $sale->user->last_login->format('Y-m-d H:i:s'));
		$this->assertEquals('00:00:00', $sale->user->newsletter_time);
		$this->assertEquals(self::$blob, $sale->user->avatar);
	}
	
	public function testProcedureAttribute() {
		$product = self::$mapper->type('obj:Acme\Result\Attribute\Product')->query("SELECT * FROM products WHERE product_id = 3");
		$this->assertInstanceOf('Acme\Result\Attribute\Product', $product);
		
		//id
		$this->assertEquals(3, $product->id);
		
		//category
		$this->assertEquals('Clothes', $product->category);
		
		//color
		$this->assertInstanceOf('Acme\RGBColor', $product->color);
		
		//lastSale
		$sale = $product->lastSale;
		$this->assertInstanceOf('Acme\Result\Attribute\Sale', $sale);
		$this->assertEquals(3, $sale->productId);
		$this->assertEquals(3, $sale->userId);
		$this->assertNull($sale->product);
		$this->assertNull($sale->user);
		
		//bestInCategory
		$best = $product->bestInCategory;
		$this->assertInstanceOf('Acme\Result\Attribute\Product', $best);
		$this->assertEquals(1, $best->id);
		$this->assertEquals('Clothes', $best->category);
		$this->assertInstanceOf('Acme\RGBColor', $best->color);
		$this->assertNull($best->lastSale);
		$this->assertNull($best->bestInCategory);
		$this->assertNull($best->avgPrice);
		
		//avgPrice
		$avg = $product->avgPrice;
		$this->assertEquals(152, intval($avg));
	}
}
?>