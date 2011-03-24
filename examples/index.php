<?php

use Nette\Loaders\RobotLoader;
use Nette\Caching\FileStorage;
use Diphy\Builder\ComplexBuilder;
use Diphy\Parser\ScriptParser;
use Diphy\Cache\NetteCache;
use Diphy\Loader\NetteRobotLoader;

define('APP_DIR', __DIR__);
define('LIBS_DIR', __DIR__ . '/../lib');
define('TMP_DIR', __DIR__ . '/../tmp');

require_once(LIBS_DIR . '/nette/Nette/loader.php');

// setup autoloading
$loader = new RobotLoader();
$loader->setCacheStorage(new FileStorage(TMP_DIR));
$loader->addDirectory(APP_DIR);
$loader->addDirectory(LIBS_DIR);
$loader->register();

// create DI container
$di = new ComplexBuilder(new ScriptParser(), new NetteCache(new FileStorage(TMP_DIR)));
$di->registerClassLoader(new NetteRobotLoader($loader));

$di->getService('DiphyExample\Application')->run();
