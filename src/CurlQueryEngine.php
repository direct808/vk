<?php
namespace Direct808\Vk;


class CurlQueryEngine implements QueryEngine
{
    public function query($url, array $parameters = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);

        if ($result === false) {
            throw new Exception\CurlException(curl_error($ch), curl_errno($ch));
        }
        return $result;
    }
}