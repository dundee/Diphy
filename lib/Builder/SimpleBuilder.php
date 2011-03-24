<?php

namespace Diphy\Builder;

use Diphy\Container;
use Diphy\Loader\ILoader;

class SimpleBuilder extends Container implements IBuilder
{
	private $config = array(
		'services' => array(),
		'autobuilding' => TRUE,
		'autowiring' => TRUE,
	);

	/** var Diphy\Loader\ILoader[] */
	private $loaders = array();

	public function getService($serviceName)
	{
		if (isset($this->config['services'][$serviceName])) {
			return $this->getConfiguredService($serviceName);
		} elseif (isset($this->services[$serviceName])) {
			return $this->services[$serviceName];
		} elseif ($this->config['autobuilding']) {
			return $this->buildService($serviceName);
		} else {
			throw new \InvalidArgumentException(sprintf('Service "%s" does not exist', $serviceName));
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
		$constructorRefl = $classRefl->getConstructor();

		if ($constructorRefl) {
			$paramsRefl = $constructorRefl->getParameters();

			$params = array();
			foreach ($paramsRefl as $paramRefl) {
				$params[] = $this->getService($paramRefl->getClass()->getName());
			}

			$this->services[$serviceName] = $classRefl->newInstanceArgs($params);
		} else {
			$this->services[$serviceName] = new $serviceName();
		}

		return $this->services[$serviceName];
	}
}
