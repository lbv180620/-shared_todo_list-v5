<?php

require dirname(__FILE__, 4) . '/vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\WebProcessor;

// create a log channel
$log = new Logger('name');

$log->pushHandler(new StreamHandler(__DIR__ . '/your.log', Logger::WARNING));
$log->pushHandler(new RotatingFileHandler(dirname(__FILE__) . '/your.log'));

$log->pushProcessor(function ($record) {
	$record['extra']['dummy'] = 'Hello World!';
	return $record;
});
$log->pushProcessor(new MemoryUsageProcessor());
$log->pushProcessor(new WebProcessor());

// add records to the log
$log->warning('Foo');
$log->error('Adding a new user', array('username' => 'Seldaek'));
