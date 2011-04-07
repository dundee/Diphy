<?php

namespace Diphy;

interface IServiceContainer extends \Nette\IContext
{
	/**
	 * Sets service
	 * @param string $serviceName
	 * @param mixed $service
	 * @return Diphy\Container
	 */
	public function setService($serviceName, $service);
}
