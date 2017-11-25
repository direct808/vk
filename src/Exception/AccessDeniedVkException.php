<?php

namespace Direct808\Vk\Exception;


class AccessDeniedVkException extends VkException
{
    public function __construct($message = "", $code = 15, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

