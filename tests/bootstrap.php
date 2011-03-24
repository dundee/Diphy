<?php

use Nette\Loaders\RobotLoader;
use Nette\Caching\FileStorage;

define('WWW_DIR', __DIR__);
define('APP_DIR', __DIR__);
define('LIBS_DIR', __DIR__ . '/../lib');
define('TMP_DIR', __DIR__ . '/../tmp');

define('FOO_DIR', __DIR__ . '/resources');
define('BOO_DIR', __DIR__ . '/Loader/resources');

require_once(__DIR__ . '/TestCase.php');

require_once(LIBS_DIR . '/nette/Nette/loader.php');

$loader = new RobotLoader();
$loader->setCacheStorage(new FileStorage(TMP_DIR));
$loader->addDirectory(APP_DIR);
$loader->addDirectory(LIBS_DIR);
$loader->register();
