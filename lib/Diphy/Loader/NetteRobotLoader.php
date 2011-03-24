<?php

namespace Diphy\Loader;

use Nette\Loaders\RobotLoader;

class NetteRobotLoader implements \Diphy\Loader\ILoader
{
	protected $loader;

	public function __construct(RobotLoader $loader)
	{
		$this->loader = $loader;
	}

	public function registerAutoload()
	{
		$this->loader->register();
	}

	public function classFileExists($className)
	{
		$classes = $this->loader->getIndexedClasses();
		if (isset($classes[$className])) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function loadClass($className)
	{
		// done via __autoload
	}

	public function getPathToClass($className)
	{
		$classes = $this->loader->getIndexedClasses();
		return $classes[$className];
	}
}
