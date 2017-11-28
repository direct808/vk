<?php

namespace Direct808\Vk\Exception;

class ManyRequestVkException extends VkException
{
    public function __construct($message = "", $code = 6, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
