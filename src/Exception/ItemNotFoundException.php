<?php

namespace Direct808\Vk\Exception;

class ItemNotFoundException extends VkException
{
    public function __construct($message = "", $code = 1403, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}