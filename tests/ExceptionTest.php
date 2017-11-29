<?php

namespace Test;

use Direct808\Vk\Exception;
use Direct808\Vk\Exception\ManyRequestVkException;
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

    /** @test */
    public function noManyRequestPerSecondException()
    {
        $true = true;
        try {
            for ($i = 0; $i < 20; $i++) {
                self::$vk->marketGetById([
                    'item_ids' => -getenv('GROUP_ID') . '_34234'
                ]);
            }
        } catch (ManyRequestVkException $e) {
            $true = false;
        }
        $this->assertTrue(
            $true,
            "Скрипт не должен был выбросить исключение ManyRequestVkException. 
            Итерация $i"
        );
    }

    /** @test */
    public function manyRequestPerSecondException()
    {
        self::$vk->setQueryDuration(10);
        $this->expectException(ManyRequestVkException::class);
        $this->expectExceptionCode(6);
        $this->expectExceptionMessage('Too many requests per second');

        try {
            for ($i = 0; $i < 20; $i++) {
                self::$vk->marketGetById([
                    'item_ids' => -getenv('GROUP_ID') . '_34234'
                ]);
            }
        } finally {
            sleep(2); // спим чтоб следующие тесты не отвалились
        }
        $this->assertTrue(
            false,
            'Скрипт должен был выбросить исключение ManyRequestVkException'
        );
    }
}
