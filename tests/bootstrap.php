<?php

include __DIR__.'/../vendor/autoload.php';

$dotEnv = new \Dotenv\Dotenv(__DIR__ . '/..');
$dotEnv->load();


global $faker;


$faker = Faker\Factory::create('ru_RU');