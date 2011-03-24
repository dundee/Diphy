<?php

namespace Diphy\Builder;

use Diphy\Loader\ILoader;

interface IBuilder
{
	/**
	 * register new class loader
	 * @param Diphy\Loader\ILoader $loader
	 * @return void
	 */
	public function registerClassLoader(ILoader $loader);

	/**
	 * Constructs service object
	 * @param string $serviceName
	 * @return mixed
	 */
	public function buildService($serviceName);
}
