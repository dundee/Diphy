<?php

namespace Diphy;

/**
 * Basic Dependency Injection container
 */
class ServiceContainer implements IServiceContainer
{
	/** var mixed[] */
	private $services;

	public function getService($serviceName, array $options = NULL)
	{
		if (isset($options)) {
			throw new \UnexpectedValueException('Service container can not accept options');
		}

		if (isset($this->services[$serviceName])) {
			return $this->services[$serviceName];
		} else {
			throw new \InvalidArgumentException(sprintf('Service "%s" is not set', $serviceName));
		}
	}

	public function setService($serviceName, $service)
	{
		$this->services[$serviceName] = $service;
		return $this;
	}

	public function addService($serviceName, $service, $singleton = TRUE, array $options = NULL)
	{
		if ($singleton != TRUE) {
			throw new \UnexpectedValueException('Service container does not support non singleton services');
		}

		if (isset($options)) {
			throw new \UnexpectedValueException('Service container can not accept options');
		}

		$this->setService($serviceName, $service);
	}

	public function removeService($serviceName)
	{
		unset($this->services[$serviceName]);
	}

	public function hasService($serviceName)
	{
		if (isset($this->services[$serviceName])) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
