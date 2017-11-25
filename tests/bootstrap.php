<?php

include __DIR__ . '/../vendor/autoload.php';

$dotEnv = new \Dotenv\Dotenv(__DIR__ . '/..');
$dotEnv->load();


if (empty(getenv('ACCESS_TOKEN')) && empty(getenv('GROUP_ID'))) {
    throw new \Exception('ACCESS_TOKEN && GROUP_ID env values not set');
}

