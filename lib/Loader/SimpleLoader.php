<?php

namespace Diphy\Loader;

class SimpleLoader implements \Diphy\Loader\ILoader
{
	private $config = array(
		'namespaces' => array(
			'Diphy' => '../../lib',
		),
	);

	public function __construct($config = array())
	{
		$this->config = $config + $this->config;
	}

	public function registerAutoload()
	{
		ini_set('unserialize_callback_func', 'spl_autoload_call');
		spl_autoload_register(array($this, 'loadClass'));
	}

	public function classExists($className)
	{
		if (!file_exists($this->getPathToClass($className))) {
			return FALSE;
		}
		return TRUE;
	}

	public function loadClass($className)
	{
		$path = $this->getPathToClass($className);
		if (!file_exists($path)) {
			throw new \UnexpectedValueException(sprintf('Class "%s" not found in file "%s"', $className, $path));
		}

		$this->loadFile($path);
	}

	public function getPathToClass($className)
	{
		foreach ($this->config['namespaces'] as $namespace => $prefix) {
			$className = preg_replace('/^' . $namespace . '/', $prefix, $className);
		}

		return str_replace('\\', '/', $className) . '.php';
	}

	private function loadFile($file)
	{
		$result = require_once($file);

		if (!$result) throw new \UnexpectedValueException(sprintf('File "%s" not found', $file));
	}
}
