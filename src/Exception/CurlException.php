<?php

namespace Direct808\Vk\Exception;

class CurlException extends VkException
{

    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        if (empty($message)) {
            $message = 'Unknown curl error #' . $code;
        }
        parent::__construct($message, $code, $previous);
    }
}
