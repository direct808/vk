<?php


use Direct808\Vk\Vk;
use PHPUnit\Framework\TestCase;

class VkTest extends TestCase
{
    function testVk()
    {
        $dotenv = new Dotenv\Dotenv(__DIR__ . '/..');
        $dotenv->load();

        $vk = new Vk();

        $vk->setToken(getenv('ACCESS_TOKEN'));

        try {
            $vk->marketAdd([
                'owner_id'=>234234234,
                'name'=>234234234,
                'description'=>233242342344234234,
                'category_id'=>500,
                'price'=>500000,
                'main_photo_id'=>500,
            ]);
        } catch (\Exception $e) {
            echo get_class($e) .' '. $e->getCode() . PHP_EOL;
            throw $e;
        }


        $this->assertTrue(true);
    }
}