<?php

namespace Test;


use Direct808\Vk\Exception\ItemNotFoundException;
use Direct808\Vk\Exception\ParametersMissingVkException;
use Direct808\Vk\Exception\VkException;
use Direct808\Vk\Vk;
use PHPUnit\Framework\TestCase;

class MarketTest extends TestCase
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
        $photoData = $this->vk->photosUploadToServer($url, 'https://fakeimg.pl/400x400/');
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
        $this->vk->photosUploadToServer($url, 'http://ru-free-tor.org/download/592095');
    }

    /**
     * @depends testGetMarketUploadServer
     * @param string $url
     */
    function testMarketUploadPhotoToServerExceptionsEmptyUrl($url)
    {
        $this->expectException(VkException::class);
        $this->vk->photosUploadToServer($url, 'bad url');
    }

    /**
     * @param array $photoData
     * @depends testMarketUploadPhotoToServer
     */
    function testMarketSavePhoto($photoData)
    {
        $result = $this->vk->photosSaveMarketPhoto($photoData, getenv('GROUP_ID'));

        $this->assertTrue($result['response'][0]['id'] > 0);

        return $result['response'][0]['id'];
    }

    function testMarketAddException()
    {
        $this->expectException(ParametersMissingVkException::class);
        $this->vk->marketAdd([]);
    }


    /**
     * @param $photoId
     * @depends testMarketSavePhoto
     * @return array
     */
    function testMarketAdd($photoId)
    {
        $id = $this->vk->marketAdd([
            'owner_id' => -getenv('GROUP_ID'),
            'name' => "Market name",
            'description' => "Market description",
            'category_id' => 500,
            'price' => 100,
            'main_photo_id' => $photoId,
            'photo_ids' => $photoId,
        ]);

        $this->assertTrue($id > 0);
        return [$id, $photoId];
    }


    function testMarketEditItemNotFoundException()
    {
        $photo = $this->vk->marketUploadPhotos('https://fakeimg.pl/405x405/', getenv('GROUP_ID'));

        $this->expectException(ItemNotFoundException::class);
        $this->expectExceptionCode(1403);

        $this->vk->marketEdit([
            'owner_id' => -getenv('GROUP_ID'),
            'item_id' => 1111111,
            'name' => "Market name updated",
            'description' => "Market description updated",
            'category_id' => 300,
            'price' => 200,
            'main_photo_id' => $photo,
            'photo_ids' => $photo,
        ]);
    }


    /**
     * @depends testMarketAdd
     * @param array $data
     * @return integer
     */
    function testMarketEdit($data)
    {
        $result = $this->vk->marketEdit([
            'owner_id' => -getenv('GROUP_ID'),
            'item_id' => $data[0],
            'name' => "Market name updated",
            'description' => "Market description updated",
            'category_id' => 300,
            'price' => 200,
            'main_photo_id' => $data[1],
            'photo_ids' => $data[1],
        ]);
        $this->assertTrue($result);
        return $data[0];
    }


    /**
     * @depends testMarketEdit
     * @param integer $itemId
     */
    function testMarketDelete($itemId)
    {
        $result = $this->vk->marketDelete(-getenv('GROUP_ID'), $itemId);
        $this->assertTrue($result);
    }

}