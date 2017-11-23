<?php

namespace Direct808\Vk;


use Direct808\Vk\Exception\CurlException;
use Direct808\Vk\Exception\ParametersMissingVkExeption;
use Direct808\Vk\Exception\UserAuthVkException;
use Direct808\Vk\Exception\VkException;

class Vk
{
    private $token;

    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }


    private function request($method, array $parameters = [])
    {
        $url = "https://api.vk.com/method/$method";
        $parameters['access_token'] = $this->token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);

        $result = curl_exec($ch);

        if ($result === false) {
            throw new CurlException(curl_error($ch), curl_errno($ch));
        }

        $result = json_decode($result, true);

        if (isset($result['error'])) {
            $this->handleError($result['error']);
        }

        print_r($result);
        return $result;
    }

    private function handleError($error)
    {
        $message = isset($error['error_msg']) ? $error['error_msg'] : 'Unknown error';
        $code = isset($error['error_code']) ? $error['error_code'] : 0;

        switch ($code) {
            case 5:
                throw new UserAuthVkException($message, $code);
            case 100:
                throw new ParametersMissingVkExeption($message, $code);

        }


        throw new VkException($message, $code);
    }

    public function marketAdd(array $parameters)
    {
        $response = $this->request('market.add', $parameters);

        return $response['response']['market_item_id'];
    }
}