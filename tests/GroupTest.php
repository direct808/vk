<?php

namespace Test;

use Direct808\Vk\Exception;
use Test\Helpers\VkTestCase;

class GroupTest extends VkTestCase
{

    public function testGroupGetById()
    {
        $result = self::$vk->groupsGetById([
            'group_id' => getenv('GROUP_ID'),
            'fields' => 'market'
        ]);
        $this->assertArraySubset([['market' => []]], $result);
    }

}
