<?php

namespace Direct808\Vk\Exception;

class UnknownMethodVkException extends VkException
{
    public function __construct($message = "", $code = 3, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

