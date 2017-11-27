<?php

namespace Test;

use Direct808\Vk\Exception\AccessDeniedVkException;
use Direct808\Vk\Exception\ParametersMissingVkException;
use Direct808\Vk\Exception\RuntimeErrorVkException;
use Direct808\Vk\Exception\UnableCompileCodeVkException;
use Direct808\Vk\Exception\UnknownMethodVkException;
use Direct808\Vk\Exception\UserAuthVkException;
use Direct808\Vk\Vk;
use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase
{
    /**
     * @var Vk;
     */
    static $vk;

    public static function setUpBeforeClass()
    {
        self::$vk = new Vk();
    }

    protected function setUp()
    {
        self::$vk->setAccessToken(getenv('ACCESS_TOKEN'));
    }

    /** @test */
    function bad_access_token()
    {
        $this->expectException(UserAuthVkException::class);
        $this->expectExceptionCode(5);

        self::$vk->setAccessToken('bad_token');
        self::$vk->callMethod('account.getInfo');
    }

    /** @test */
    function access_denied()
    {
        $this->expectException(AccessDeniedVkException::class);
        $this->expectExceptionCode(15);

        self::$vk->callMethod('market.delete', [
            'owner_id' => -31456119,
            'item_id' => 123123,
        ]);
    }

    /** @test */
    function parameters_missing()
    {
        $this->expectException(ParametersMissingVkException::class);
        $this->expectExceptionCode(100);

        self::$vk->callMethod('market.delete', []);
    }

    /** @test */
    function unknown_method()
    {
        $this->expectException(UnknownMethodVkException::class);
        $this->expectExceptionCode(3);

        self::$vk->callMethod('bad_method', []);
    }


    /** @test */
    function unable_to_compile_code()
    {
        $this->expectException(UnableCompileCodeVkException::class);
        $this->expectExceptionCode(12);

        self::$vk->execute('bad code (((');
    }

    /** @test */

    function runtime_error()
    {
        $this->expectException(RuntimeErrorVkException::class);
        $this->expectExceptionCode(13);

        self::$vk->execute('var a=1/0;');
    }

}