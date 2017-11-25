<?php

namespace Direct808\Vk\Exception;

class UserAuthVkException extends VkException
{
    public function __construct($message = "", $code = 5, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}