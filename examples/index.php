<?php

use Nette\Loaders\RobotLoader;
use Nette\Caching\FileStorage;
use Nette\Caching\Cache;

use Diphy\Builder\ComplexBuilder;

define('APP_DIR', __DIR__);
define('LIBS_DIR', __DIR__ . '/../lib');
define('TMP_DIR', __DIR__ . '/../tmp');

require_once(LIBS_DIR . '/nette/Nette/loader.php');

// setup autoloading
$loader = new RobotLoader();
$loader->setCacheStorage(new FileStorage(TMP_DIR));
$loader->addDirectory(APP_DIR);
$loader->addDirectory(LIBS_DIR);
$loader->ignoreDirs .= ', nette\tests';
$loader->register();

// create DI container
$di = new ComplexBuilder(new Cache(new FileStorage(TMP_DIR)));
$di->registerClassLoader($loader);

$di->getService('DiphyExample\Application')->run();
