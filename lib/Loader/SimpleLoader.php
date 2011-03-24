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

	public function classFileExists($className)
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
			throw new \UnexpectedValueException(sprintf('Class file "%s" of class "%s" does not exist', $path, $className));
		}

		$path = realpath($path);

		$this->loadFile($path);

		if (!class_exists($className) && !interface_exists($className)) {
			throw new \UnexpectedValueException(sprintf('Class file "%s" loaded, but class "%s" not present', $path, $className));
		}
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
