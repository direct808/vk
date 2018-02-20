<?php

namespace Test;

use Direct808\Vk\Exception;
use Test\Helpers\VkTestCase;

class MarketTest extends VkTestCase
{

    public function testGetMarketUploadServer()
    {
        $url = self::$vk->photosGetMarketUploadServer([
            'main_photo' => 1,
            'group_id' => getenv('GROUP_ID')
        ]);
        $this->assertStringStartsWith('http', $url);
        return $url;
    }

    public function testGetMarketUploadServerExceptions()
    {
        $this->expectException(Exception\ParametersMissingVkException::class);
        self::$vk->photosGetMarketUploadServer([]);
    }


    /**
     * @depends testGetMarketUploadServer
     * @param string $url
     * @return
     */
    public function testMarketUploadPhotoToServer($url)
    {
        $photoData = self::$vk->photosUploadToServer($url, self::$image);
        $this->assertJson($photoData['photo']);
        return $photoData;
    }

    /**
     * @depends testGetMarketUploadServer
     * @param string $url
     */
    public function testMarketUploadPhotoToServerExceptions($url)
    {
        $this->expectException(Exception\VkException::class);
        self::$vk->photosUploadToServer($url, __FILE__);
    }

    /**
     * @depends testGetMarketUploadServer
     * @param string $url
     */
    public function testMarketUploadPhotoToServerExceptionsEmptyUrl($url)
    {
        $this->expectException(Exception\VkException::class);
        self::$vk->photosUploadToServer($url, 'bad url');
    }

    /**
     * @param array $photoData
     * @depends testMarketUploadPhotoToServer
     */
    public function testMarketSavePhoto($photoData)
    {
        $result = self::$vk->photosSaveMarketPhoto($photoData, getenv('GROUP_ID'));

        $this->assertTrue($result[0]['id'] > 0);

        return $result[0]['id'];
    }

    public function testMarketAddException()
    {
        $this->expectException(Exception\ParametersMissingVkException::class);
        self::$vk->marketAdd([]);
    }


    /**
     * @param $photoId
     * @depends testMarketSavePhoto
     * @return array
     */
    public function testMarketAdd($photoId)
    {
        $id = self::$vk->marketAdd([
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


    public function testMarketEditItemNotFoundException()
    {
        $photo = self::$vk->marketUploadPhotos(self::$image, getenv('GROUP_ID'));

        $this->expectException(Exception\ItemNotFoundException::class);
        $this->expectExceptionCode(1403);

        self::$vk->marketEdit([
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
    public function testMarketEdit($data)
    {
        $result = self::$vk->marketEdit([
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
    public function testMarketDelete($itemId)
    {
        $result = self::$vk->marketDelete(-getenv('GROUP_ID'), $itemId);
        $this->assertTrue($result);
    }


    public function testUploadManyAdvertsAndImages()
    {
        $images = array_fill(0, 2, self::$image);
        self::$vk->setQueryDuration(100);

        for ($i = 0; $i < 2; $i++) {
            $photos = self::$vk->marketUploadPhotos($images, getenv('GROUP_ID'), true);
            $mainPhoto = reset($photos);

            self::$vk->marketAdd([
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


    public function testMarketDeleteAll()
    {
        $groupId = getenv('GROUP_ID');
        self::$vk->marketDeleteAll(-$groupId);
        $this->assertTrue(true);
    }


    public function testMarketEnabled()
    {
        $groupId = getenv('GROUP_ID');
        $this->assertTrue(self::$vk->marketEnabled($groupId));
    }
}
