<?php

namespace Diphy\Builder;

use Diphy\ServiceContainer;
use Diphy\Loader\ILoader;
use Nette\Loaders\RobotLoader;

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
		$this->services[get_class($this)] = $this;
	}

	public function setConfig(array $config)
	{
		$this->config = $config + $this->config;
	}

	public function getService($serviceName, array $options = NULL)
	{
		if (isset($options)) {
			return $this->buildService($serviceName, $options);
		} elseif (isset($this->config['services'][$serviceName])) {
			return $this->getConfiguredService($serviceName);
		} elseif (isset($this->services[$serviceName])) {
			return $this->services[$serviceName];
		} else {
			return $this->buildService($serviceName);
		}
	}

	/**
	 * Registers new class loader
	 * @param Nette\Loaders\RobotLoader $loader
	 * @return void
	 */
	public function registerClassLoader(RobotLoader $loader)
	{
		$this->loaders[] = $loader;
	}

	/**
	 * Build instance of service
	 * @param string $serviceName
	 * @return mixed
	 */
	protected function buildService($serviceName, array $options = NULL)
	{
		$classRefl = new \ReflectionClass($serviceName);

		if ($classRefl->isInterface()) {
			$implementorName = $this->getImplementor($serviceName);
			$classRefl = new \ReflectionClass($implementorName);
		}

		if (isset($options)) {
			return $classRefl->newInstanceArgs($options);
		} else {
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
		}

		return $this->services[$serviceName];
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

		if (isset($config['factory'])) {
			$factory = $this->getService($config['factory'][0]);
			return $this->services[$serviceName] = $factory->{$config['factory'][1]}();
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
