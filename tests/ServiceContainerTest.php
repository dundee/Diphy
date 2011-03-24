<?php

namespace DiphyTest\Builder;

use DiphyTest\TestCase;
use Diphy\ServiceContainer;

/**
 * @author Daniel Milde <daniel@milde.cz>
 * @group unit
 */
class ContainerTest extends TestCase
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

	private $loader;

	public function setUp()
	{
		$this->object = new ServiceContainer();
	}

	public function testGetService()
	{
		//$a = new \Foo\A();
		//$this->object->setService('Foo\A', $a);

		//$this->assertInstanceOf('Foo\A', $this->object->getService('Foo\A'));
	}
}
