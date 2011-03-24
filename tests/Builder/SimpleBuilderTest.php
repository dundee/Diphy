<?php

namespace DiphyTest\Builder;

use DiphyTest\TestCase;
use Nette\Caching\FileStorage;
use Nette\Loaders\RobotLoader;
use Diphy\Loader\NetteRobotLoader;
use Diphy\Builder\SimpleBuilder;

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
		$robot = new RobotLoader();
		$robot->setCacheStorage(new FileStorage(TMP_DIR));
		$robot->addDirectory(APP_DIR);
		$robot->addDirectory(LIBS_DIR);
		$robot->register();
		$loader = new NetteRobotLoader($robot);

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

	public function testGetServiceWithMultipleDependenciesAndOptionalParameter()
	{
		$this->assertInstanceOf('Foo\E', $this->object->getService('Foo\E'));
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testGetInterfaceImplementor()
	{
		$this->object->getService('Foo\IG');
	}

	public function testGetConfiguredService()
	{
		$config = array(
			'services' => array(
				'Foo\IG' => array(
					'class' => 'Foo\G1',
				),
			),
		);
		$this->object->setConfig($config);
		$x = $this->object->getService('Foo\IG');
		$y = $this->object->getService('Foo\IG');
		$this->assertInstanceOf('Foo\G1', $x);
		$this->assertTrue($x === $y);

	}

	public function testGetNewConfiguredService()
	{
		$config = array(
			'services' => array(
				'Foo\IG' => array(
					'class' => 'Foo\G1',
					'alwaysnew' => TRUE,
				),
			),
		);
		$this->object->setConfig($config);
		$x = $this->object->getService('Foo\IG');
		$y = $this->object->getService('Foo\IG');
		$this->assertInstanceOf('Foo\G1', $x);
		$this->assertFalse($x === $y);
	}

	public function testGetConfiguredServiceWithParam()
	{
		$config = array(
			'services' => array(
				'Foo\IH' => array(
					'class' => 'Foo\H1',
					'params' => array(
						'@Foo\G1',
					)
				),
			),
		);
		$this->object->setConfig($config);
		$this->assertInstanceOf('Foo\H1', $this->object->getService('Foo\IH'));

	}

	public function testGetConfiguredServiceWithMultipleParams()
	{
		$config = array(
			'services' => array(
				'Foo\E' => array(
					'class' => 'Foo\E',
					'params' => array(
						'@Foo\A',
						'@Foo\B',
						array('xx'),
					)
				),
			),
		);
		$this->object->setConfig($config);
		$e = $this->object->getService('Foo\E');
		$this->assertInstanceOf('Foo\E', $e);
		$this->assertEquals(array('xx'), $e->config);
	}

	public function testGetConfiguredServiceWithMultipleParams2()
	{
		$config = array(
			'services' => array(
				'Foo\J' => array(
					'class' => 'Foo\J',
					'params' => array(
						'@Foo\IG',
						'aa',
					)
				),
				'Foo\IG' => array(
					'class' => 'Foo\G1',
				)
			),
		);
		$this->object->setConfig($config);
		$j = $this->object->getService('Foo\J');
		$this->assertInstanceOf('Foo\J', $j);
		$this->assertEquals('aa', $j->x);
	}
}
