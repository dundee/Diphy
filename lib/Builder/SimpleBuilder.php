<?php

namespace Diphy\Builder;

use Diphy\Container;
use Diphy\Loader\ILoader;

class SimpleBuilder extends Container implements IBuilder
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

	public function registerClassLoader(ILoader $loader)
	{
		$this->loaders[] = $loader;
	}

	public function buildService($serviceName)
	{
		$loaded = FALSE;
		foreach ($this->loaders as $loader) {
			if ($loader->classFileExists($serviceName)) {
				$loader->loadClass($serviceName);
				$loaded = TRUE;
				break;
			}
		}

		if (!$loaded) {
			throw new \InvalidArgumentException(sprintf('No class loader is able to load class "%s"', $serviceName));
		}

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
}
