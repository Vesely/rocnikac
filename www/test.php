<?php

$container = require __DIR__ . '/../app/bootstrap.php';

$container->getByType('Nette\Application\Application')->run();


// header('Location: http://twenty7.cz/www/');
// exit;
