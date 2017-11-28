<?php

namespace Direct808\Vk;

class CurlQueryEngine implements QueryEngine
{
    private $sslVerifyPeer = true;

    public function __construct($options = [])
    {
        if (isset($options['ssl_verify_peer'])) {
            $this->sslVerifyPeer = (bool)$options['ssl_verify_peer'];
        }
    }

    public function query($url, array $parameters = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->sslVerifyPeer);

        $result = curl_exec($ch);

        if ($result === false) {
            throw new Exception\CurlException(curl_error($ch), curl_errno($ch));
        }
        return $result;
    }

    public function queryAsync(array $data)
    {
        $mh = curl_multi_init();
        $ch = [];

        foreach ($data as $i => $item) {
            $ch[$i] = curl_init();
            curl_setopt($ch[$i], CURLOPT_URL, $item['url']);
            curl_setopt($ch[$i], CURLOPT_POST, true);
            curl_setopt($ch[$i], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch[$i], CURLOPT_POSTFIELDS, $item['parameters']);
            curl_multi_add_handle($mh, $ch[$i]);
        }

        $active = null;
        do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
    }
}
