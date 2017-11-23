<?php


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
        $dotEnv = new Dotenv\Dotenv(__DIR__ . '/..');
        $dotEnv->load();

        $this->vk = new Vk();
        $this->vk->setToken(getenv('ACCESS_TOKEN'));
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

    /**
     * @depends testGetMarketUploadServer
     * @param string $url
     * @return
     */
    function testMarketUploadPhotoToServer($url)
    {
        $result = $this->vk->uploadPhotoToServer($url, 'http://lorempixel.com/410/410/');

//        print_r($result);

        $this->assertTrue(true);

        return $result;
    }

    /**
     * @param array $photoData
     * @depends testMarketUploadPhotoToServer
     */
    function testMarketSavePhoto($photoData)
    {
        $result = $this->vk->photosSaveMarketPhoto($photoData, getenv('GROUP_ID'));

        $photoData['photo'] = stripslashes($photoData['photo']);

//        print_r($result);

        $this->assertTrue(true);

        return $result['response'][0]['id'];
    }

    /**
     * @param $photoId
     * @depends testMarketSavePhoto
     */
    function testMarketAdd($photoId)
    {
        $id = $this->vk->marketAdd([
            'owner_id' => 234234234,
            'name' => 234234234,
            'description' => 233242342344234234,
            'category_id' => 500,
            'price' => 500000,
            'main_photo_id' => $photoId,
        ]);


        $this->assertTrue($id > 0);
    }
}