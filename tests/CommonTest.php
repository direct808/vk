<?php

namespace Test;

use Direct808\Vk\Exception\ManyRequestVkException;
use Direct808\Vk\Vk;
use PHPUnit\Framework\TestCase;
use Test\Helpers\FakeQueryEngine;

class CommonTest extends TestCase
{
    /**
     * @var Vk;
     */
    private $vk;
    private $image;


    protected function setUp()
    {
        $this->vk = new Vk();
        $this->vk->setAccessToken(getenv('ACCESS_TOKEN'));
        $this->image = __DIR__ . '/400.gif';
    }

    /** @test */
    function sdfsdf()
    {
        $images = array_fill(0, 4, $this->image);

        for ($i = 0; $i < 3; $i++) {

            $photos = $this->vk->marketUploadPhotos($images, getenv('GROUP_ID'), true);
            $mainPhoto = reset($photos);

            $this->vk->marketAdd([
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
    function market_delete_all()
    {
        $groupId = getenv('GROUP_ID');
        $this->vk->marketDeleteAll(-$groupId);
        $this->assertTrue(true);
    }


    function testManyRequestPerSecond()
    {
//        $this->vk->setQueryDuration(10);
        $this->expectException(ManyRequestVkException::class);
        $this->expectExceptionCode(6);
        $this->expectExceptionMessage('Too many requests per second');
        for ($i = 0; $i < 20; $i++) {
            $this->vk->marketGetById([
                'item_ids' => -getenv('GROUP_ID') . '_34234'
            ]);
        }
        $this->assertTrue(false, 'Скрипт должен был выбросить исключени "Too many requests per second"');
    }


    function testQueryDuration()
    {
        $queryEngine = new FakeQueryEngine();
        $queryEngine->wait = 50;

        $vk = new Vk([], $queryEngine);
        $vk->setQueryDuration(200);

        $startTime = microtime(true);
        $vk->callMethod('market.getById', []);
        $duration = round((microtime(true) - $startTime) * 1000);
        $this->assertEquals($duration, $queryEngine->wait, 'Первый запрос к апи должен продлиться без задержки');


        $startTime = microtime(true);
        $vk->callMethod('market.getById', []);
        $duration = round((microtime(true) - $startTime) * 1000);
        $this->assertTrue(abs($duration - $vk->getQueryDuration()) < 5, 'Остальные запросы должны придерживаться определенной задержки');


        $startTime = microtime(true);
        $vk->callMethod('market.getById', []);
        $duration = round((microtime(true) - $startTime) * 1000);
        $this->assertTrue(abs($duration - $vk->getQueryDuration()) < 5, 'Остальные запросы должны придерживаться определенной задержки');

        usleep(160 * 1000);

        $startTime = microtime(true);
        $vk->callMethod('market.getById', []);
        $duration = round((microtime(true) - $startTime) * 1000);
        $this->assertEquals($duration, $queryEngine->wait, 'Этот запрос должен выполниться без задержки, т.к. выше создана искуственная задержка превышающая интервал');


    }

}