<?php

use Diphy\Loader\SimpleLoader;

define('WWW_DIR', __DIR__);
define('APP_DIR', __DIR__);
define('LIBS_DIR', __DIR__ . '/../lib');

define('FOO_DIR', __DIR__ . '/resources');
define('BOO_DIR', __DIR__ . '/Loader/resources');

require_once(__DIR__ . '/TestCase.php');

require_once(LIBS_DIR . '/Loader/ILoader.php');
require_once(LIBS_DIR . '/Loader/SimpleLoader.php');


$loaderConfig = array(
	'namespaces' => array(
		'DiphyTest' => APP_DIR,
		'Diphy' => LIBS_DIR,
		'Foo'   => FOO_DIR,
		'Boo'   => BOO_DIR,
	),
);

$loader = new SimpleLoader($loaderConfig);
$loader->registerAutoload();


