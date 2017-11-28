<?php

namespace Direct808\Vk;

class Vk
{
    use
        Features\Market,
        Features\Notes,
        Features\Photos;

    private $token;
    private $apiVersion = "5.69";
    private $batchMode;
    private $batches = [];
    private $queryService;

    private $lastQueryTimestamp = 0;
    private $queryDuration = 334;  //ms

    public function __construct($options = [], $queryEngine = null)
    {
        if (!$queryEngine) {
            $queryEngine = new CurlQueryEngine();
        }
        $this->queryService = $queryEngine;
    }

    public function setAccessToken($token)
    {
        $this->token = $token;
        return $this;
    }


    public function callMethod($method, array $parameters = [])
    {
        if ($this->batchMode) {
            $this->callMethodBatch($method, $parameters);
            return null;
        }

        $this->processWait();

        $url = "https://api.vk.com/method/$method";
        $parameters['access_token'] = $this->token;
        $parameters['v'] = $this->apiVersion;

        $this->lastQueryTimestamp = microtime(true);
        $result = $this->query($url, $parameters);


        return $result['response'];
    }

    private function processWait()
    {
        if ($this->lastQueryTimestamp == 0) {
            return;
        }

        $curTime = microtime(true);
        $lastQueryDuration = ($curTime - $this->lastQueryTimestamp);
        $lastQueryDurationMs = $lastQueryDuration * 1000;

        if ($lastQueryDurationMs < $this->queryDuration) {
            $timeout = ($this->queryDuration - $lastQueryDurationMs);
            usleep($timeout * 1000);
        }
    }

    private function callMethodBatch($method, array $parameters = [])
    {
        $json = json_encode($parameters);
        $this->batches[] = "result.push(API.$method($json));";
    }

    private function query($url, array $parameters = [])
    {
        $result = $this->queryService->query($url, $parameters);
        $result = json_decode($result, true);

        if (isset($result['error'])) {
            $this->handleError($result['error']);
        }
        return $result;
    }

    private function queryAsync(array $data)
    {
        $result = $this->queryService->queryAsync($data);
//        $result = json_decode($result, true);
//
//        if (isset($result['error'])) {
//            $this->handleError($result['error']);
//        }
        return $result;
    }


    private function handleError($error)
    {
        if (is_string($error)) {
            throw new Exception\VkException($error);
        }

        $message = isset($error['error_msg']) ? $error['error_msg'] : 'Unknown Vk error';
        $code = isset($error['error_code']) ? $error['error_code'] : 0;

        switch ($code) {
            case 3:
                throw new Exception\UnknownMethodVkException($message);
            case 5:
                throw new Exception\UserAuthVkException($message);
            case 6:
                throw new Exception\ManyRequestVkException($message);
            case 12:
                throw new Exception\UnableCompileCodeVkException($message);
            case 13:
                throw new Exception\RuntimeErrorVkException($message);
            case 14:
                throw new Exception\CaptchaNeededVkException($error);
            case 15:
                throw new Exception\AccessDeniedVkException($message);
            case 100:
                throw new Exception\ParametersMissingVkException($message);
            case 1403:
                throw new Exception\ItemNotFoundException($message);
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
        $this->batches = [];

        $this->batches[] = 'var result = [];';

        $closure();

        $this->batchMode = false;

        $this->batches[] = 'return result;';

        $str = implode("\n", $this->batches);

        return $this->execute($str);
    }

    /**
     * @return int
     */
    public function getQueryDuration()
    {
        return $this->queryDuration;
    }

    /**
     * @param int $ms
     * @return $this
     */
    public function setQueryDuration($ms)
    {
        $this->queryDuration = $ms;
        return $this;
    }
}
