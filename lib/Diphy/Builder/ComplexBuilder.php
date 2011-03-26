<?php

namespace Diphy\Builder;

use Diphy\Parser\ScriptParser;
use Nette\Loaders\RobotLoader;
use Nette\Caching\Cache;
use ReflectionClass;

/**
 * Extends the posibilities of SimpleBuilder by looking up implementors of interface
 */
class ComplexBuilder extends SimpleBuilder
{
	/** var Diphy\Cache\ICache */
	protected $cache;

	/** var string[] */
	protected $implementors = array();

	public function __construct(Cache $cache, array $config = array())
	{
		parent::__construct($config);
		$this->cache = $cache;
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
			foreach ($this->loaders as $loader) {
				$classes = $loader->getIndexedClasses();
				foreach ($classes as $class => $file) {
					$classRefl = new ReflectionClass($class);
					$interfaces = $classRefl->getInterfaces();
					foreach ($interfaces as $interface) {
						if ($interface->getName() == $interfaceName) {
							$this->implementors[] = $classRefl->getName();
						}
					}
				}
			}


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
}
