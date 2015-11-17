<?php

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

// $configurator->setDebugMode('194.228.11.31'); // enable for your remote IP
$configurator->setDebugMode(true);
// $configurator->enableDebugger(__DIR__ . '/../log');
$configurator->enableDebugger(__DIR__ . '/../log', 'david.vesely@gmail.com');

$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

Kdyby\Replicator\Container::register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
$localhostArray = array('127.0.0.1', '::1');
if(in_array($_SERVER['REMOTE_ADDR'], $localhostArray)){
	$configurator->addConfig(__DIR__ . '/config/config.local.neon');
}else{
	$configurator->addConfig(__DIR__ . '/config/config.ftp.neon');
}

$container = $configurator->createContainer();

return $container;
