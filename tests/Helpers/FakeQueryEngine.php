<?php

namespace Test\Helpers;

use Direct808\Vk\QueryEngine;

class FakeQueryEngine implements QueryEngine
{

    public $wait = 0;

    public function query($url, array $parameters = [])
    {
        usleep(1000 * $this->wait);
        return null;
    }
}