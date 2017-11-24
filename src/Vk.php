<?php

namespace Direct808\Vk;

class Vk
{
    use
        Features\Market,
        Features\Photos;

    private $token;
    private $batchMode;
    private $batches = [];

    public function setAccessToken($token)
    {
        $this->token = $token;
        return $this;
    }


    public function callMethod($method, array $parameters = [])
    {
        if ($this->batchMode)
            return $this->callMethodBatch($method, $parameters);

        $url = "https://api.vk.com/method/$method";
        $parameters['access_token'] = $this->token;
        $result = $this->query($url, $parameters);
        return $result;
    }

    private function callMethodBatch($method, array $parameters = [])
    {
        $json = json_encode($parameters);
        $this->batches[] = "API.$method($json);";
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
            throw new Exception\CurlException(curl_error($ch), curl_errno($ch));
        }

        $result = json_decode($result, true);

        if (isset($result['error'])) {
            $this->handleError($result['error']);
        }

        return $result;
    }

    private function handleError($error)
    {
        if (is_string($error))
            throw new Exception\VkException($error);

        $message = isset($error['error_msg']) ? $error['error_msg'] : 'Unknown Vk error';
        $code = isset($error['error_code']) ? $error['error_code'] : 0;

        switch ($code) {
            case 5:
                throw new Exception\UserAuthVkException($message, $code);
            case 3:
                throw new Exception\UnknownMethodVkException($message, $code);
            case 12:
                throw new Exception\UnableCompileCodeVkException($message, $code);
            case 13:
                throw new Exception\RuntimeErrorVkException($message, $code);
            case 15:
                throw new Exception\AccessDeniedVkException($message, $code);
            case 100:
                throw new Exception\ParametersMissingVkException($message, $code);
        }
        throw new Exception\VkException($message, $code);
    }

    public function execute($code)
    {
        return $this->callMethod('execute', ['code' => $code]);
    }

    public function batch(\Closure $closure)
    {
        $this->batchMode = true;
        $closure();
//        print_r($this->batches);
        $this->batchMode = false;

        $this->batches[] = 'return "111";';

        $str = implode("\n", $this->batches);

        return $this->execute($str);
    }

}