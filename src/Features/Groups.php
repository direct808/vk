<?php

namespace Direct808\Vk\Features;

trait Groups
{
    public function groupsGetById(array $parameters)
    {
        $response = $this->callMethod('groups.getById', $parameters);

        return $response;
    }

}
