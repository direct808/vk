<?php

namespace Test;

use Direct808\Vk\Vk;
use Test\Helpers\FakeQueryEngine;
use Test\Helpers\VkTestCase;

class CommonTest extends VkTestCase
{

    public function testQueryDuration()
    {
        $queryEngine = new FakeQueryEngine();
        $queryEngine->wait = 50;

        $vk = new Vk([], $queryEngine);
        $vk->setQueryDuration(200);

        $startTime = microtime(true);
        $vk->callMethod('market.getById', []);
        $duration = round((microtime(true) - $startTime) * 1000);
        $this->assertEquals(
            $duration,
            $queryEngine->wait,
            'Первый запрос к апи должен продлиться без задержки'
        );


        $startTime = microtime(true);
        $vk->callMethod('market.getById', []);
        $duration = round((microtime(true) - $startTime) * 1000);
        $this->assertTrue(
            abs($duration - $vk->getQueryDuration()) < 7,
            'Остальные запросы 
            должны придерживаться определенной задержки'
        );


        $startTime = microtime(true);
        $vk->callMethod('market.getById', []);
        $duration = round((microtime(true) - $startTime) * 1000);
        $this->assertTrue(
            abs($duration - $vk->getQueryDuration()) < 7,
            'Остальные запросы 
            должны придерживаться определенной задержки'
        );

        usleep(160 * 1000);

        $startTime = microtime(true);
        $vk->callMethod('market.getById', []);
        $duration = round((microtime(true) - $startTime) * 1000);
        $this->assertEquals(
            $duration,
            $queryEngine->wait,
            'Этот запрос должен выполниться без задержки, т.к. 
            выше создана искуственная задержка превышающая интервал'
        );
    }
}
