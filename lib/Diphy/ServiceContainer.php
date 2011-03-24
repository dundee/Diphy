<?php

namespace Diphy;

/**
 * Basic Dependency Injection container
 */
class ServiceContainer implements IServiceContainer
{
	/** var mixed[] */
	private $services;

	public function getService($serviceName)
	{
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
}
