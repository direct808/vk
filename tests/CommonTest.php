<?php

namespace Test;

use Direct808\Vk\Vk;
use PHPUnit\Framework\TestCase;

class CommonTest extends TestCase
{
    /**
     * @var Vk;
     */
    private $vk;

    protected function setUp()
    {
        $this->vk = new Vk();
        $this->vk->setAccessToken(getenv('ACCESS_TOKEN'));
    }

//    /** @test */
    function sdfsdf()
    {

        $result = $this->vk->batch(function () {

            for ($i = 0; $i < 1; $i++)
                $this->vk->marketGet([
                    'owner_id' => -getenv('GROUP_ID')
                ]);
        });

        print_r($result);

        $this->assertTrue(true);
//        $this->expectException(UserAuthVkException::class);
//        $this->vk->setAccessToken('bad_token');
//        $this->vk->execute('asdasd');
    }

    /** @test */
    function sdfsdfasd()
    {
        $groupId = getenv('GROUP_ID');
        $result = $this->vk->marketGetById([
            'item_ids' => "-{$groupId}_1169004234,-153285493_11689744234"
        ]);

        print_r($result);

        $this->assertTrue(true);
//        $this->expectException(UserAuthVkException::class);
//        $this->vk->setAccessToken('bad_token');
//        $this->vk->execute('asdasd');
    }

}