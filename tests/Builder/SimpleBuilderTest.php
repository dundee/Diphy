<?php

namespace DiphyTest\Builder;

use DiphyTest\TestCase;
use Diphy\Loader\SimpleLoader;
use Diphy\Builder\SimpleBuilder;

define('FOO_DIR', __DIR__ . '/resources');

/**
 * @author Daniel Milde <daniel@milde.cz>
 * @group unit
 */
class SimpleBuilderTest extends TestCase
{
	private $config = array(

	);

	private $loaderConfig = array(
		'namespaces' => array(
			'DiphyTest' => APP_DIR,
			'Diphy' => LIBS_DIR,
			'Foo'   => FOO_DIR,
		),
	);

	public function setUp()
	{
		$loader = new SimpleLoader($this->loaderConfig);
		$this->object = new SimpleBuilder($this->config);
		$this->object->registerClassLoader($loader);
	}

	public function testLoadConfig()
	{
		$this->assertInstanceOf('Diphy\Builder\SimpleBuilder', $this->object);
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

	public function testGetServiceWithMultipleDependencies()
	{
		$this->assertInstanceOf('Foo\D', $this->object->getService('Foo\D'));
	}
}
