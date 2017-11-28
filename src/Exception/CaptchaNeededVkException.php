<?php

namespace Direct808\Vk\Exception;

class CaptchaNeededVkException extends VkException
{
    private $data;

    public function __construct(array $message)
    {
        $this->data = $message;
        parent::__construct($message['error_msg'], 14);
    }

    public function getCaptchaImg()
    {
        return $this->data['captcha_img'];
    }


    public function getCaptchaSid()
    {
        return $this->data['captcha_sid'];
    }
}
