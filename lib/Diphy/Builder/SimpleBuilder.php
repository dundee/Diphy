<?php

namespace Diphy\Builder;

use Diphy\ServiceContainer;
use Diphy\Loader\ILoader;

/**
 * Builds services according to configuration or via reflection
 */
class SimpleBuilder extends ServiceContainer
{
	protected $config = array(
		'services' => array(),
	);

	/** var Diphy\Loader\ILoader[] */
	protected $loaders = array();

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

	/**
	 * Build instance of service
	 * @param string $serviceName
	 * @return mixed
	 */
	protected function buildService($serviceName)
	{
		$this->loadClass($serviceName);

		$classRefl = new \ReflectionClass($serviceName);

		if ($classRefl->isInterface()) {
			$implementorName = $this->getImplementor($serviceName);
			$classRefl = new \ReflectionClass($implementorName);
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
			$this->services[$serviceName] = $classRefl->newInstance();
		}

		return $this->services[$serviceName];
	}

	/**
	 * Load class
	 * @param string $className
	 */
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

	/**
	 * Get service configured via given config
	 * @param string $serviceName
	 * @return mixed
	 */
	protected function getConfiguredService($serviceName)
	{
		$config = $this->config['services'][$serviceName];

		if (isset($this->services[$serviceName])
		    && (!isset($config['alwaysnew']) || !$config['alwaysnew'])) {
			return $this->services[$serviceName];
		}

		if (isset($config['params'])) {
			$params = array();
			foreach ($config['params'] as $param) {
				if (is_string($param) && strpos($param, '@') === 0) {
					$params[] = $this->getService(str_replace('@', '', $param));
				} else {
					$params[] = $param;
				}
			}

			$classRefl = new \ReflectionClass($config['class']);
			$instance = $classRefl->newInstanceArgs($params);
		} else {
			$instance = new $config['class'];
		}

		return $this->services[$serviceName] = $instance;
	}

	/**
	 * Get implementor of given interface
	 * @param string $interfaceName
	 * @return string $implementorName
	 */
	protected function getImplementor($interfaceName)
	{
		throw new \InvalidArgumentException(sprintf('SimpleBuilder is not able to look for interface implementors "%s"', $interfaceName));
	}
}
