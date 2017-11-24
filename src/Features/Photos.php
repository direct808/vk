<?php

namespace Direct808\Vk\Features;

use Direct808\Vk\Exception\VkException;

trait Photos
{

    public function photosGetMarketUploadServer($params)
    {
        $result = $this->callMethod('photos.getMarketUploadServer', $params);
        return $result['response']['upload_url'];
    }

    public function uploadPhotoToServer($uploadUrl, $file)
    {
        $filePath = tempnam(sys_get_temp_dir(), 'vk');

        $f = @file_get_contents($file);

        if ($f === false) {
            $err = error_get_last();
            throw new VkException($err['message']);
        }

        file_put_contents($filePath, $f);

        $curlFile = new \CURLFile($filePath);
        $curlFile->setPostFilename('file.png');

        $parameters = [
            'file' => $curlFile
        ];
        return $this->query($uploadUrl, $parameters);
    }

    public function photosSaveMarketPhoto(array $photoData, $groupId)
    {
        $photoData['group_id'] = $groupId;
        return $this->callMethod('photos.saveMarketPhoto', $photoData);
    }
}