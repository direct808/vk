<?php

namespace Direct808\Vk\Exception;

class UnableCompileCodeVkException extends VkException
{
    public function __construct($message = "", $code = 12, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
