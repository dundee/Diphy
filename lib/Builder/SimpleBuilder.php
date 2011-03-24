<?php

namespace Diphy\Builder;

use Diphy\ServiceContainer;
use Diphy\Loader\ILoader;

class SimpleBuilder extends ServiceContainer
{
	private $config = array(
		'services' => array(),
	);

	/** var Diphy\Loader\ILoader[] */
	private $loaders = array();

	public function __construct(array $config = array())
	{
		$this->config = $config + $this->config;
	}

	public function setConfig(array $config)
	{
		$this->config = $config + $this->config;
	}

	public function getService($serviceName)
	{
		if (isset($this->config['services'][$serviceName])) {
			return $this->getConfiguredService($serviceName);
		} elseif (isset($this->services[$serviceName])) {
			return $this->services[$serviceName];
		} else {
			return $this->buildService($serviceName);
		}
	}

	/**
	 * Registers new class loader
	 * @param Diphy\Loader\ILoader $loader
	 * @return void
	 */
	public function registerClassLoader(ILoader $loader)
	{
		$this->loaders[] = $loader;
	}

	protected function buildService($serviceName)
	{
		$this->loadClass($serviceName);

		$classRefl = new \ReflectionClass($serviceName);

		if ($classRefl->isInterface()) {
			throw new \InvalidArgumentException(sprintf('SimpleBuilder can not instantiate interface "%s"', $serviceName));
		}

		$constructorRefl = $classRefl->getConstructor();

		if ($constructorRefl) {
			$paramsRefl = $constructorRefl->getParameters();

			$params = array();
			foreach ($paramsRefl as $paramRefl) {
				if (!$paramRefl->isOptional()) {
					$params[] = $this->getService($paramRefl->getClass()->getName());
				}
			}

			$this->services[$serviceName] = $classRefl->newInstanceArgs($params);
		} else {
			$this->services[$serviceName] = new $serviceName();
		}

		return $this->services[$serviceName];
	}

	protected function loadClass($className)
	{
		$loaded = FALSE;
		foreach ($this->loaders as $loader) {
			if ($loader->classFileExists($className)) {
				$loader->loadClass($className);
				$loaded = TRUE;
				break;
			}
		}

		if (!$loaded) {
			throw new \InvalidArgumentException(sprintf('No class loader is able to load class "%s"', $className));
		}
	}
}
