<?php
namespace eMapper\Cache;

use eMapper\Cache\Key\CacheKey;
use eMapper\Type\TypeManager;

/**
 * 
 * @author emaphp
 * @group cache
 */
class ArgumentCacheKeyTest extends \PHPUnit_Framework_TestCase {
	public $cacheKey;
	
	public function __construct() {
		$this->cacheKey = new CacheKey(new TypeManager());
	}
	
	/**
	 * Tests various type handlers applied to distinct types
	 */
	public function testArgumentCacheKey() {
		$result = $this->cacheKey->build('USER_%{0}_%{0:s}_%{0:us}_%{0:i}_%{0:f}_%{0:b}', array(25), array());
		$this->assertEquals('USER_25_25_25_25_25_TRUE', $result);
		
		$result = $this->cacheKey->build('PRICE_%{0}_%{0:s}_%{0:us}_%{0:i}_%{0:f}_%{0:b}', array(39.95), array());
		$this->assertEquals('PRICE_39.95_39.95_39.95_39_39.95_TRUE', $result);
		
		$result = $this->cacheKey->build('PROD_%{0}_%{0:s}_%{0:us}_%{0:i}_%{0:f}_%{0:b}', array('XYZ123'), array());
		$this->assertEquals('PROD_XYZ123_XYZ123_XYZ123_0_0_TRUE', $result);
		
		$result = $this->cacheKey->build('AVAL_%{0}_%{0:s}_%{0:us}_%{0:i}_%{0:f}_%{0:b}', array(true), array());
		$this->assertEquals('AVAL_TRUE_1_1_1_1_TRUE', $result);
		
		$result = $this->cacheKey->build('MIX_%{3}_%{2}_%{1}_%{0}', array(25, 39.95, 'XYZ123', true), array());
		$this->assertEquals('MIX_TRUE_XYZ123_39.95_25', $result);
	}
	
	/**
	 * Tests accesing properties through subindexes
	 */
	public function testArgumentSubindex() {
		$result = $this->cacheKey->build('ID_%{0[id]}_NAME_%{0[0]}', array(array('id' => 1, 'jdoe')), array());
		$this->assertEquals('ID_1_NAME_jdoe', $result);
		
		$result = $this->cacheKey->build('ID_#{data[id]}_NAME_#{data[0]}', array(array('data' => array('id' => 1, 'jdoe'))), array());
		$this->assertEquals('ID_1_NAME_jdoe', $result);
	}
	
	/**
	 * Tests accesing elemements in an array through ranges
	 */
	public function testArgumentArrayRange() {
		$result = $this->cacheKey->build('IDS_%{0[2..2]}', array(array(45, 23, '43', '164', 43)), array());
		$this->assertEquals('IDS_43_164', $result);
		
		$result = $this->cacheKey->build('IDS_%{0[2..]}', array(array(45, 23, '43', '164', 43)), array());
		$this->assertEquals('IDS_43_164_43', $result);
		
		$result = $this->cacheKey->build('IDS_%{0[..3]}', array(array(45, 23, '43', '164', 43)), array());
		$this->assertEquals('IDS_45_23_43', $result);
		
		$result = $this->cacheKey->build('IDS_%{0[..]}', array(array(45, 23, '43', '164', 43)), array());
		$this->assertEquals('IDS_45_23_43_164_43', $result);
		
		$result = $this->cacheKey->build('IDS_#{data[2..2]}', array(array('data' => array(45, 23, '43', '164', 43))), array());
		$this->assertEquals('IDS_43_164', $result);
		
		$result = $this->cacheKey->build('IDS_#{data[2..]}', array(array('data' => array(45, 23, '43', '164', 43))), array());
		$this->assertEquals('IDS_43_164_43', $result);
		
		$result = $this->cacheKey->build('IDS_#{data[..3]}', array(array('data' => array(45, 23, '43', '164', 43))), array());
		$this->assertEquals('IDS_45_23_43', $result);
		
		$result = $this->cacheKey->build('IDS_#{data[..]}', array(array('data' => array(45, 23, '43', '164', 43))), array());
		$this->assertEquals('IDS_45_23_43_164_43', $result);
	}
	
	public function testArgumentStringRange() {
		$result = $this->cacheKey->build('COD_%{0[2..2]}', array("supercheria"), array());
		$this->assertEquals('COD_pe', $result);
	
		$result = $this->cacheKey->build('COD_%{0[2..]}', array("supercheria"), array());
		$this->assertEquals('COD_percheria', $result);
	
		$result = $this->cacheKey->build('COD_%{0[..3]}', array("supercheria"), array());
		$this->assertEquals('COD_sup', $result);
	
		$result = $this->cacheKey->build('COD_%{0[..]}', array("supercheria"), array());
		$this->assertEquals('COD_supercheria', $result);
	
		$result = $this->cacheKey->build('COD_#{data[2..2]}', array(array('data' => "supercheria")), array());
		$this->assertEquals('COD_pe', $result);
	
		$result = $this->cacheKey->build('COD_#{data[2..]}', array(array('data' => "supercheria")), array());
		$this->assertEquals('COD_percheria', $result);
	
		$result = $this->cacheKey->build('COD_#{data[..3]}', array(array('data' => "supercheria")), array());
		$this->assertEquals('COD_sup', $result);
	
		$result = $this->cacheKey->build('COD_#{data[..]}', array(array('data' => "supercheria")), array());
		$this->assertEquals('COD_supercheria', $result);
	}
}
?>