<?php

namespace Direct808\Vk;


use Direct808\Vk\Exception\CurlException;
use Direct808\Vk\Exception\ParametersMissingVkExeption;
use Direct808\Vk\Exception\UserAuthVkException;
use Direct808\Vk\Exception\VkException;
use Direct808\Vk\Features\Market;
use Direct808\Vk\Features\Photos;

class Vk
{
    use Market, Photos;

    private $token;

    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }


    private function callMethod($method, array $parameters = [])
    {
        $url = "https://api.vk.com/method/$method";

//        if (!isset($parameters['access_token']))
        $parameters['access_token'] = $this->token;

        $result = $this->query($url, $parameters);


//        print_r($result);
        return $result;
    }

    private function query($url, array $parameters = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);

        $result = curl_exec($ch);

        if ($result === false) {
            $err = curl_error($ch);
            $errNo = curl_errno($ch);
            throw new CurlException($err, $errNo);
        }

        $result = json_decode($result, true);

        if (isset($result['error'])) {
            $this->handleError($result['error']);
        }

//        print_r($result);
        return $result;
    }

    private function handleError($error)
    {
        if (is_string($error))
            throw new VkException($error);

        $message = isset($error['error_msg']) ? $error['error_msg'] : 'Unknown Vk error';
        $code = isset($error['error_code']) ? $error['error_code'] : 0;

        switch ($code) {
            case 5:
                throw new UserAuthVkException($message, $code);
            case 100:
                throw new ParametersMissingVkExeption($message, $code);

        }


        throw new VkException($message, $code);
    }

}