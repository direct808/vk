<?php

namespace Direct808\Vk\Features;

trait Market
{

    public function marketAdd(array $parameters)
    {
        $response = $this->callMethod('market.add', $parameters);

        return $response['response']['market_item_id'];
    }

}