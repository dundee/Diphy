<?php

use Diphy\Loader\SimpleLoader;
use Diphy\Builder\ComplexBuilder;
use Diphy\Parser\ScriptParser;
use Diphy\Cache\NullCache;

define('APP_DIR', __DIR__);
define('LIBS_DIR', __DIR__ . '/../lib');

require_once(LIBS_DIR . '/Loader/ILoader.php');
require_once(LIBS_DIR . '/Loader/SimpleLoader.php');

// autoloading configuration
$loaderConfig = array(
	'namespaces' => array(
		'DiphyExample' => APP_DIR,
		'Diphy' => LIBS_DIR,
	),
);

// setup autoloading
$loader = new SimpleLoader($loaderConfig);
$loader->registerAutoload();

// create DI container
$di = new ComplexBuilder(new ScriptParser(), new NullCache());
$di->registerClassLoader($loader);


$di->getService('DiphyExample\Application')->run();
