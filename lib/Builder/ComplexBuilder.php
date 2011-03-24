<?php

namespace Diphy\Builder;

use Diphy\Cache\ICache;
use Diphy\Parser\ScriptParser;

class ComplexBuilder extends SimpleBuilder
{
	/** var Diphy\Cache\ICache */
	protected $cache;

	/** var Diphy\Parser\ScriptParser */
	protected $parser;

	/** var string[] */
	protected $implementors = array();

	public function __construct(ScriptParser $parser, ICache $cache, array $config = array())
	{
		parent::__construct($config);
		$this->cache = $cache;
		$this->parser = $parser;
	}

	/**
	 * Tries to find the only one implementation of interface in interface directory and subdirectories
	 * @param string $interfaceName
	 * @return string $implementorName
	 */
	protected function getImplementor($interfaceName)
	{
		$this->implementors = $this->cache['complex-builder-' . $interfaceName];

		if (!$this->implementors) {
			$path = FALSE;
			foreach ($this->loaders as $loader) {
				if ($loader->classFileExists($interfaceName)) {
					$path = $loader->getPathToClass($interfaceName);
					break;
				}
			}
			if (!$path) throw new \UnexpectedValueException(sprintf('Interface "%s" dos not exist', $interfaceName));

			$this->implementors = array();
			$this->scanDirectoryForImplementor(dirname($path), $interfaceName);

			$this->cache['complex-builder-' . $interfaceName] = $this->implementors;
		}

		if (count($this->implementors) > 1) {
			throw new \UnexpectedValueException(sprintf('Interface "%s" has more than one implementor', $interfaceName));
		} elseif (count($this->implementors) == 1) {
			return $this->implementors[0];
		} else {
			throw new \UnexpectedValueException(sprintf('No implementor of interface "%s" found', $interfaceName));
		}
	}

	/**
	 * Scan directory and subdirectories for implementor of interface
	 * @param string $directory
	 * @param string $interfaceName
	 * @return void
	 */
	public function scanDirectoryForImplementor($directory, $interfaceName)
	{
		$handle = dir($directory);
		while (false !== ($entry = $handle->read())) {
			if ($entry == '.' || $entry == '..') continue;

			$path = $directory . '/' . $entry;
			if (is_dir($path)) {
				$this->scanDirectoryForImplementor($path, $interfaceName);
			} else {
				$this->scanFileForImplementor($path, $interfaceName);
			}
		}
		$handle->close();
	}

	/**
	 * Scan file for implementor of interface
	 * @param string $directory
	 * @param string $interfaceName
	 * @return void
	 */
	public function scanFileForImplementor($path, $interfaceName)
	{
		if (!preg_match('/(\.php|\.php5)$/', $path)) return;

		$content = file_get_contents($path);

		$this->parser->run($content);

		$interfaces = $this->parser->getInterfaces();

		if (is_array($interfaces) && in_array($interfaceName, $interfaces)) {
			$this->implementors[] = $this->parser->getClassName();
		}
	}
}
