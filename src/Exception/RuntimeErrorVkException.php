<?php

namespace Direct808\Vk\Exception;

class RuntimeErrorVkException extends VkException
{
    public function __construct($message = "", $code = 13, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

