<?php

namespace Diphy;

use Diphy\Diphy;
use Diphy\Loader\SimpleLoader;

define('FOO_DIR', __DIR__ . '/resources');

/**
 * @author Daniel Milde <daniel@milde.cz>
 * @group unit
 */
class DiphyTest extends TestCase
{
	private $config = array(

	);

	private $loaderConfig = array(
		'namespaces' => array(
			'Diphy' => LIBS_DIR,
			'Foo'   => FOO_DIR,
		),
	);

	public function setUp()
	{
		$loader = new SimpleLoader($this->loaderConfig);
		$this->object = new Diphy($this->config);
		$this->object->registerClassLoader($loader);
	}

	public function testLoadConfig()
	{
		$this->assertInstanceOf('Diphy\Diphy', $this->object);
	}

	public function testGetServiceWithoutDependencies()
	{
		$this->assertInstanceOf('Foo\A', $this->object->getService('Foo\A'));
	}

	public function testGetServiceWithOneDependency()
	{
		$this->assertInstanceOf('Foo\B', $this->object->getService('Foo\B'));
	}

	public function testGetServiceWithoutDependenciesAndEmptyConstructor()
	{
		$this->assertInstanceOf('Foo\C', $this->object->getService('Foo\C'));
	}
}
