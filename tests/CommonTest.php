<?php

namespace Test;

use Direct808\Vk\Exception\ManyRequestVkException;
use Direct808\Vk\Vk;
use PHPUnit\Framework\TestCase;
use Test\Helpers\TestQueryEngine;

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


        $result = $this->vk->batch(function () {
            for ($i = 0; $i < 25; $i++) {
                $photo = $this->vk->marketUploadPhotos($this->image, getenv('GROUP_ID'), true);
                $this->vk->marketAdd([
                    'owner_id' => -getenv('GROUP_ID'),
                    'name' => "Market name",
                    'description' => "Market description",
                    'category_id' => 500,
                    'price' => 100,
                    'main_photo_id' => $photo,
//                'photo_ids' => $photo,
                ]);
            }
        });


        file_put_contents(__DIR__ . '/asd.php', "<?php\n" . var_export($result, true) . ';');

        $this->assertTrue(true);
//        $this->expectException(UserAuthVkException::class);
//        $this->vk->setAccessToken('bad_token');
//        $this->vk->execute('asdasd');
    }

    /** @test */
    function delete_all_tovars()
    {
        $groupId = getenv('GROUP_ID');
//        $result = $this->vk->marketGet([
//            'owner_id' => -$groupId,
//            'count' => 200
//        ]);

        do {


            $result = $this->vk->execute("var tovars = API.market.get({\"owner_id\": -$groupId});

if (tovars.items.length == 0)
    return 0;

var i = 0;

while (i < tovars.items.length && i<24) {
    API.market.delete({\"owner_id\": -$groupId, \"item_id\": tovars.items[i].id});
    i = i + 1;
}

if (tovars.count> i)
    return 1;

return 0;");
        } while ($result == 1);

        print_r($result);

        $this->assertTrue(true);
//        $this->expectException(UserAuthVkException::class);
//        $this->vk->setAccessToken('bad_token');
//        $this->vk->execute('asdasd');
    }

    function testManyRequestPerSecond()
    {
        $this->expectException(ManyRequestVkException::class);
        $this->expectExceptionCode(6);
        $this->expectExceptionMessage('Too many requests per second');
        for ($i = 0; $i < 50; $i++) {
            $this->vk->marketGetById([
                'item_ids' => -getenv('GROUP_ID') . '_34234'
            ]);
        }
        $this->assertTrue(false, 'Скрипт должен был выбросить исключени "Too many requests per second"');
    }

    function testManyRequestPerSecond2()
    {
        $queryEngine = new TestQueryEngine();

        $vk = new Vk([], $queryEngine);

        $startTime = microtime(true);
        for ($i = 0; $i < 4; $i++) {
            $vk->callMethod('market.getById', []);
        }
        $curTime = microtime(true) - $startTime;
        echo   PHP_EOL.'total ' . round($curTime * 1000) . PHP_EOL;
        echo PHP_EOL;
//        $this->assertTrue(false, 'Скрипт должен был выбросить исключени "Too many requests per second"');
    }

}