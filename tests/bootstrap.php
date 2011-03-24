<?php

use Diphy\Loader\SimpleLoader;

define('WWW_DIR', __DIR__);
define('APP_DIR', __DIR__);
define('LIBS_DIR', __DIR__ . '/../lib');

require_once(__DIR__ . '/TestCase.php');

require_once(LIBS_DIR . '/Loader/ILoader.php');
require_once(LIBS_DIR . '/Loader/SimpleLoader.php');


$loaderConfig = array(
	'namespaces' => array(
		'Diphy' => LIBS_DIR,
	),
);

$loader = new SimpleLoader($loaderConfig);
$loader->registerAutoload();


