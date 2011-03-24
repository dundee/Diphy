<?php

namespace DiphyTest\Loader;

use DiphyTest\TestCase;
use Diphy\Loader\SimpleLoader;

/**
 * @author Daniel Milde <daniel@milde.cz>
 * @group unit
 */
class SimpleLoaderTest extends TestCase
{
	private $loaderConfig = array(
		'namespaces' => array(
			'DiphyTest' => APP_DIR,
			'Diphy' => LIBS_DIR,
			'Boo'   => BOO_DIR,
		),
	);

	public function setUp()
	{
		$this->object = new SimpleLoader($this->loaderConfig);
	}

	public function tearDown()
	{

	}

	public function testRegisterAutoload()
	{
		$this->object->registerAutoload();

		$functions = spl_autoload_functions();
		$this->assertEquals(array($this->object, 'loadClass'), $functions[count($functions) - 1]);

		spl_autoload_unregister(array($this->object, 'loadClass'));
	}

	public function testLoadClass()
	{
		//$this->assertFalse(class_exists('Boo\Foo'));
		$this->object->loadClass('Boo\Foo');
		$this->assertTrue(class_exists('Boo\Foo'));
	}

	public function testClassExists()
	{
		$this->assertTrue($this->object->classFileExists('Boo\Boo'));
	}

	public function testClassNotExists()
	{
		$this->assertFalse($this->object->classFileExists('Foooo'));
	}
}
