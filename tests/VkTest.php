<?php

namespace Test;


use Direct808\Vk\Exception\ParametersMissingVkException;
use Direct808\Vk\Exception\VkException;
use Direct808\Vk\Vk;
use PHPUnit\Framework\TestCase;

class VkTest extends TestCase
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

    function testGetMarketUploadServer()
    {
        $url = $this->vk->photosGetMarketUploadServer([
            'main_photo' => 1,
            'group_id' => getenv('GROUP_ID')
        ]);
        $this->assertStringStartsWith('http', $url);
        return $url;
    }

    function testGetMarketUploadServerExceptions()
    {
        $this->expectException(ParametersMissingVkException::class);
        $this->vk->photosGetMarketUploadServer([]);
    }


    /**
     * @depends testGetMarketUploadServer
     * @param string $url
     * @return
     */
    function testMarketUploadPhotoToServer($url)
    {
        $photoData = $this->vk->uploadPhotoToServer($url, 'https://fakeimg.pl/400x400/');
        $this->assertJson($photoData['photo']);
        return $photoData;
    }

    /**
     * @depends testGetMarketUploadServer
     * @param string $url
     */
    function testMarketUploadPhotoToServerExceptions($url)
    {
        $this->expectException(VkException::class);
        $this->vk->uploadPhotoToServer($url, 'http://ru-free-tor.org/download/592095');
    }

    /**
     * @param array $photoData
     * @depends testMarketUploadPhotoToServer
     */
    function testMarketSavePhoto($photoData)
    {
        $result = $this->vk->photosSaveMarketPhoto($photoData, getenv('GROUP_ID'));

        $this->assertTrue($result['response'][0]['pid'] > 0);

        return $result['response'][0]['pid'];
    }

    function testMarketAddException()
    {
        $this->expectException(ParametersMissingVkException::class);
        $this->vk->marketAdd([]);
    }


//    /**
//     * @param $photoId
//     * @depends testMarketSavePhoto
//     */
//    function testMarketAdd($photoId)
//    {
//        $id = $this->vk->marketAdd([
//            'owner_id' => -getenv('GROUP_ID'),
//            'name' => 234234234,
//            'description' => 233242342344234234,
//            'category_id' => 500,
//            'price' => 500000,
//            'main_photo_id' => $photoId,
//        ]);
//
//        $this->assertTrue($id > 0);
//    }
}