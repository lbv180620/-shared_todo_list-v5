<?php

require dirname(__FILE__, 4) . '/vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

$log = new Logger('name');

$date_format = "Y年n月d日 H:i:s";

$format = "%datetime% > %level_name% > %message% %context% %extra%\n";

$formatter = new LineFormatter($format, $date_format);

$stream = new StreamHandler(__DIR__ . '/your.log', Logger::WARNING);

$stream->setFormatter($formatter);

$log->pushHandler($stream);

$log->warning('Foo');
$log->error('Bar');
