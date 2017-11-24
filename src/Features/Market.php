<?php

namespace Direct808\Vk\Features;

trait Market
{

    public function marketAdd(array $parameters)
    {
        $response = $this->callMethod('market.add', $parameters);

        return $response['response']['market_item_id'];
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

        return $response;
    }

    public function marketGetById(array $parameters)
    {
        $response = $this->callMethod('market.getById', $parameters);

        return $response;
    }

}