<?php

namespace Test\Helpers;

use Direct808\Vk\Vk;
use PHPUnit\Framework\TestCase;

class VkTestCase extends TestCase
{
    /**
     * @var Vk;
     */
    protected static $vk;
    protected static $image;
    protected static $accessToken;

    public static function setUpBeforeClass()
    {
        if (!self::$vk) {
            self::$vk = new Vk();
        }
        self::$image = __DIR__ . '/../400.gif';
        self::$accessToken = getenv('ACCESS_TOKEN');
    }

    protected function setUp()
    {
        self::$vk->setAccessToken(self::$accessToken);
        self::$vk->setQueryDuration(334);
    }
}
