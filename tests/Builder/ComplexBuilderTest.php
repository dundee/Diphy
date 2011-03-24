<?php

namespace DiphyTest\Builder;

use DiphyTest\TestCase;
use Diphy\Loader\SimpleLoader;
use Diphy\Cache\NullCache;
use Diphy\Parser\ScriptParser;
use Diphy\Builder\ComplexBuilder;

/**
 * @author Daniel Milde <daniel@milde.cz>
 * @group unit
 */
class ComplexBuilderTest extends TestCase
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
		$cache = new NullCache();
		$parser = new ScriptParser();
		$this->object = new ComplexBuilder($parser, $cache, $this->config);
		$this->object->registerClassLoader($loader);
	}

	public function testLoadConfig()
	{
		$this->assertInstanceOf('Diphy\Builder\ComplexBuilder', $this->object);
	}

	public function testGetInterfaceImplementor()
	{
		$this->assertInstanceOf('Foo\G1', $this->object->getService('Foo\IG'));
	}

	/**
	 * @expectedException UnexpectedValueException
	 */
	public function testGetInterfaceImplementor2()
	{
		$this->object->getService('Foo\IH');
	}

	public function testGetNestedInterfaceImplementor()
	{
		$k1 = $this->object->getService('Foo\IK');
		$k2 = $this->object->getService('Foo\IK');
		$k3 = $this->object->getService('Foo\Bar\K1');
		$this->assertInstanceOf('Foo\Bar\K1', $k1);
		$this->assertTrue($k1 === $k2);
		$this->assertFalse($k1 === $k3);
	}
}
