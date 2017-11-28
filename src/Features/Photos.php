<?php

namespace Direct808\Vk\Features;

use Direct808\Vk\Exception\VkException;

trait Photos
{

    public function photosGetMarketUploadServer($params)
    {
        $result = $this->callMethod('photos.getMarketUploadServer', $params);
        return $result['upload_url'];
    }

    public function photosUploadToServer($uploadUrl, $file)
    {
        $filePath = tempnam(sys_get_temp_dir(), 'vk');
        $f = @file_get_contents($file);
        if ($f === false) {
            $err = error_get_last();
            throw new VkException($err['message']);
        }

        file_put_contents($filePath, $f);

        $curlFile = new \CURLFile($filePath);
        $curlFile->setPostFilename('file.jpg');

        $parameters = [
            'file' => $curlFile
        ];
        return $this->query($uploadUrl, $parameters);
    }

    public function photosUploadToServerAsync(array $data)
    {
        $queryArr = [];
        foreach ($data as $datum) {
            $file = $datum['file'];

            $filePath = tempnam(sys_get_temp_dir(), 'vk');
            $f = @file_get_contents($file);
            if ($f === false) {
                $err = error_get_last();
                throw new VkException($err['message']);
            }

            file_put_contents($filePath, $f);

            $curlFile = new \CURLFile($filePath);
            $curlFile->setPostFilename('file.jpg');

            $parameters = [
                'file' => $curlFile
            ];

            $queryArr[]=[
                'url'=>$datum['url'],
                'parameters'=>$parameters,
            ];
        }

        return $this->queryAsync($queryArr);
    }


    public function photosSaveMarketPhoto(array $photoData, $groupId)
    {
        $photoData['group_id'] = $groupId;
        return $this->callMethod('photos.saveMarketPhoto', $photoData);
    }
}
