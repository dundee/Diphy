<?php

namespace Diphy;

interface IServiceContainer
{
	/**
	 * Returns service
	 * @param string $serviceName
	 * @return mixed
	 */
	public function getService($serviceName);

	/**
	 * Sets service
	 * @param string $serviceName
	 * @param mixed $service
	 * @return Diphy\Container
	 */
	public function setService($serviceName, $service);
}
