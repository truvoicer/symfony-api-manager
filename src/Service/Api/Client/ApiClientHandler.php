<?php
namespace App\Service\Api\Client;

use App\Service\Api\ApiBase;
use App\Service\Api\Client\Entity\ApiRequest;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ApiClientHandler extends ApiBase
{

    private $requestConfig;

    public function __construct()
    {
        $this->requestConfig = [];
    }

    public function sendRequest(ApiRequest $apiRequest)
    {
        try {
            $this->setQueryParams($apiRequest->getQuery());
            $this->setHeaders($apiRequest->getHeaders());
            $this->setPostData($apiRequest->getBody());
            $this->setRequestAuth($apiRequest->getAuthentication());
            $client = HttpClient::create();
            return $client->request($apiRequest->getMethod(), $apiRequest->getUrl(), $this->requestConfig);

        } catch (TransportExceptionInterface $e) {
            throw new TransportException($e->getMessage());
        }
    }

    public function setRequestAuth(array $auth = [])
    {
        if (count($auth) > 0) {
            foreach ($auth as $key => $value) {
                $this->requestConfig[$key] = $value;
            }
        }
        return $this->requestConfig;
    }

    public function setQueryParams(array $params = [])
    {
        if (count($params) > 0) {
            $this->requestConfig["query"] = $params;
        }
        return $this->requestConfig;
    }

    public function setHeaders(array $headers = [])
    {
        if (count($headers) > 0) {
            $this->requestConfig['headers'] = $headers;
        }
        return $this->requestConfig;
    }

    public function setPostData(array $data = []) {
        if (count($data) > 0) {
            $this->requestConfig['body'] = $data;
        }
        return $this->requestConfig;
    }
}