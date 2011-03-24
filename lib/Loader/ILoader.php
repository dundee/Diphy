<?php

namespace Diphy\Loader;

/**
 * Interface for loading classes
 */
interface ILoader
{
	/**
	 * Register __autoload function
	 */
	public function registerAutoload();

	/**
	 * @param string $className
	 * @return bool
	 */
	public function classFileExists($className);

	/**
	 * @param string $className
	 * @return void
	 */
	public function loadClass($className);

	/**
	 * Get path to class
	 * @param string $className
	 * @return string $path
	 */
	public function getPathToClass($className);
}
