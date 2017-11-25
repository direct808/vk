<?php

namespace Direct808\Vk\Exception;

class ParametersMissingVkException extends VkException
{
    public function __construct($message = "", $code = 100, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

