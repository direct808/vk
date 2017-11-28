<?php

namespace Test;

use Direct808\Vk\Exception;
use Test\Helpers\VkTestCase;

class ExceptionTest extends VkTestCase
{

    /** @test */
    public function badAccessToken()
    {
        $this->expectException(Exception\UserAuthVkException::class);
        $this->expectExceptionCode(5);

        self::$vk->setAccessToken('bad_token');
        self::$vk->callMethod('account.getInfo');
    }

    /** @test */
    public function accessDenied()
    {
        $this->expectException(Exception\AccessDeniedVkException::class);
        $this->expectExceptionCode(15);

        self::$vk->callMethod('market.delete', [
            'owner_id' => -31456119,
            'item_id' => 123123,
        ]);
    }

    /** @test */
    public function parametersMissing()
    {
        $this->expectException(Exception\ParametersMissingVkException::class);
        $this->expectExceptionCode(100);

        self::$vk->callMethod('market.delete', []);
    }

    /** @test */
    public function unknownMethod()
    {
        $this->expectException(Exception\UnknownMethodVkException::class);
        $this->expectExceptionCode(3);

        self::$vk->callMethod('bad_method', []);
    }


    /** @test */
    public function unableToCompileCode()
    {
        $this->expectException(Exception\UnableCompileCodeVkException::class);
        $this->expectExceptionCode(12);

        self::$vk->execute('bad code (((');
    }

    /** @test */

    public function runtimeError()
    {
        $this->expectException(Exception\RuntimeErrorVkException::class);
        $this->expectExceptionCode(13);

        self::$vk->execute('var a=1/0;');
    }
}
