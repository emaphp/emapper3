<?php
namespace eMapper\MySQL;

use eMapper\AbstractManagerTest;
use eMapper\Mapper;
use eMapper\Engine\MySQL\MySQLDriver;
use Acme\Type\RGBColorTypeHandler;

/**
 * MySQL manager test
 * @author emaphp
 * @group mysql
 * @group manager
 */
class ManagerTest extends AbstractManagerTest {
	public function build() {
		$config = MySQLTest::$config;
		$this->driver = new MySQLDriver($config['database'], $config['host'], $config['user'], $config['password']);
		$this->mapper = new Mapper($this->driver);
		$this->mapper->addType('Acme\RGBColor', new RGBColorTypeHandler());
		$this->productsManager = $this->mapper->buildManager('Acme\Entity\Product');
	}
}	
?>