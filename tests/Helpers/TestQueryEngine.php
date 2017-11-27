<?php

namespace Test\Helpers;

use Direct808\Vk\QueryEngine;

class TestQueryEngine implements QueryEngine
{
    public function query($url, array $parameters = [])
    {
        $ms = rand(100, 500);
//        $ms = 200;
//        echo 'query wait  ' . ($ms) . PHP_EOL;
//        usleep(1000 * $ms);
        return null;
    }
}