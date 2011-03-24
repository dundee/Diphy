<?php

namespace Diphy;

use Diphy\Loader\ILoader;
use ClassReflection;

class Diphy
{
	private $config = array(
		'services' => array(),
		'autobuilding' => TRUE,
		'autowiring' => TRUE,
	);

	/** var mixed[] */
	private $services;

	/** var Diphy\Loader\ILoader[] */
	private $loaders = array();

	public function __construct(array $config = array())
	{
		$this->config = $config + $this->config;
	}

	/**
	 * register new class loader
	 * @param Diphy\Loader\ILoader $loader
	 * @return void
	 */
	public function registerClassLoader(ILoader $loader)
	{
		$this->loaders[] = $loader;
	}

	/**
	 * Returns service
	 * @param string $serviceName
	 * @return mixed
	 */
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

	/**
	 * Constructs service object
	 * @param string $serviceName
	 * @return mixed
	 */
	private function buildService($serviceName)
	{
		foreach ($this->loaders as $loader) {
			if ($loader->classExists($serviceName)) {
				$loader->loadClass($serviceName);
				break;
			}
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
