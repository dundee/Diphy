<?php

namespace Diphy\Builder;

use Diphy\Loader\ILoader;

interface IBuilder
{
	public function __construct(array $config = array());

	/**
	 * Sets new configuration
	 */
	public function setConfig(array $config);

	/**
	 * Registers new class loader
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
