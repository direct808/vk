<?php

namespace Direct808\Vk\Features;

trait Market
{

    public function marketAdd(array $parameters)
    {
        $response = $this->callMethod('market.add', $parameters);

        return $response['market_item_id'];
    }

    public function marketEdit(array $parameters)
    {
        $response = $this->callMethod('market.edit', $parameters);

        return $response == 1;
    }

    public function marketGet(array $parameters)
    {
        $response = $this->callMethod('market.get', $parameters);

        return $response;
    }

    public function marketDelete($ownerId, $itemId)
    {
        // возвращает 1 даже если удалить несуществующий товар
        $parameters = [
            'owner_id' => $ownerId,
            'item_id' => $itemId,
        ];
        $response = $this->callMethod('market.delete', $parameters);

        return $response == 1;
    }

    public function marketGetById(array $parameters)
    {
        $response = $this->callMethod('market.getById', $parameters);

        return $response;
    }

    public function marketUploadPhotos($filePath, $groupId, $firstIsMain = false)
    {
        $oldBatchMode = $this->batchMode;
        $this->batchMode = false;
        $files = is_array($filePath) ? $filePath : [$filePath];

        $result = [];
        for ($i = 0; $i < count($files); $i++) {
            $uploadUrl = $this->photosGetMarketUploadServer([
                'group_id' => $groupId,
                'main_photo' => $firstIsMain && $i == 0 ? 1 : 0,
            ]);
            $uploadData = $this->photosUploadToServer($uploadUrl, $files[$i]);
            $result[] = $this->photosSaveMarketPhoto($uploadData, $groupId)[0]['id'];
        }
        $this->batchMode = $oldBatchMode;

        if (!is_array($filePath)) {
            return reset($result);
        }
        return $result;
    }

    public function marketDeleteAll($ownerId)
    {
        $code = " 
            var goods = API.market.get({\"owner_id\": $ownerId});
            if (goods.count == 0)
                return 0;
            
            var i = 0;
            
            while (i < goods.items.length && i<24) {
                API.market.delete({\"owner_id\": $ownerId, \"item_id\": goods.items[i].id});
                i = i + 1;
            }
            
            if (goods.count > i)
                return 1;
            
            return 0;
        ";

        do {
            $result = $this->execute($code);
        } while ($result == 1);
    }


}