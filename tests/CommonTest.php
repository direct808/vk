<?php

namespace Test;

use Direct808\Vk\Exception\ManyRequestVkException;
use Direct808\Vk\Vk;
use Test\Helpers\FakeQueryEngine;
use Test\Helpers\VkTestCase;

class CommonTest extends VkTestCase
{

    /** @test */
    public function uploadManyAdvertsAndImages()
    {
        $images = array_fill(0, 4, self::$image);
        self::$vk->setQueryDuration(100);

        for ($i = 0; $i < 5; $i++) {
            $photos = self::$vk->marketUploadPhotos($images, getenv('GROUP_ID'), true);
            $mainPhoto = reset($photos);

            self::$vk->marketAdd([
                'owner_id' => -getenv('GROUP_ID'),
                'name' => "Market name $i",
                'description' => "Market description $i",
                'category_id' => 500,
                'price' => 100,
                'main_photo_id' => $mainPhoto,
                'photo_ids' => implode(',', $photos),
            ]);
        }

        $this->assertTrue(true);
    }


    /** @test */
    public function marketDeleteAll()
    {
        $groupId = getenv('GROUP_ID');
        self::$vk->marketDeleteAll(-$groupId);
        $this->assertTrue(true);
    }

    /** @test */

    public function manyRequestPerSecond()
    {
        self::$vk->setQueryDuration(10);
        $this->expectException(ManyRequestVkException::class);
        $this->expectExceptionCode(6);
        $this->expectExceptionMessage('Too many requests per second');
        for ($i = 0; $i < 20; $i++) {
            self::$vk->marketGetById([
                'item_ids' => -getenv('GROUP_ID') . '_34234'
            ]);
        }
        $this->assertTrue(
            false,
            'Скрипт должен был выбросить исключение "Too many requests per second"'
        );
    }


    public function queryDuration()
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
            abs($duration - $vk->getQueryDuration()) < 5,
            'Остальные запросы 
            должны придерживаться определенной задержки'
        );


        $startTime = microtime(true);
        $vk->callMethod('market.getById', []);
        $duration = round((microtime(true) - $startTime) * 1000);
        $this->assertTrue(
            abs($duration - $vk->getQueryDuration()) < 5,
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
