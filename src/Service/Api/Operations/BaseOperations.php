<?php

namespace App\Service\Api\Operations;

use App\Entity\Provider;
use App\Entity\ServiceRequestParameter;
use App\Service\Api\ApiBase;
use App\Service\Api\Client\Amazon\AmazonApiManager;
use App\Service\Api\Client\ApiClientHandler;
use App\Service\Api\Client\Entity\ApiRequest;
use App\Service\Api\Client\Oauth\Oauth;
use App\Service\Api\Response\ResponseManager;
use App\Service\CategoryService;
use App\Service\EventsService;
use App\Service\ProviderService;
use App\Service\RequestService;
use App\Service\SerializerService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class BaseOperations extends ApiBase
{
    private $oath;
    private $providerService;
    private $serializerService;
    private $eventsService;
    private $requestService;
    protected $provider;
    protected $apiRequestName;
    protected $apiService;
    protected $categoryService;
    protected $responseManager;
    protected $apiClientHandler;
    protected $amazonApiManager;
    protected $apiRequest;
    protected $query;
    protected $queryArray;
    protected $category;
    protected $timestamp;

    public function __construct(ProviderService $providerService, SerializerService $serializerService, Oauth $oauth,
                                ResponseManager $responseManager, AmazonApiManager $amazonApiManager,
                                CategoryService $categoryService, ApiClientHandler $apiClientHandler,
                                ApiRequest $apiRequest, EventsService $eventsService, RequestService $requestService)
    {
        $this->providerService = $providerService;
        $this->serializerService = $serializerService;
        $this->oath = $oauth;
        $this->responseManager = $responseManager;
        $this->amazonApiManager = $amazonApiManager;
        $this->apiRequest = $apiRequest;
        $this->categoryService = $categoryService;
        $this->eventsService = $eventsService;
        $this->apiClientHandler = $apiClientHandler;
        $this->requestService = $requestService;
    }

    public function getRequestContent(string $providerName = null)
    {
        $response = $this->runRequest($providerName);
        return $this->responseManager->getRequestContent($this->apiService, $this->provider, $response);
    }

    public function getOperationResponse(string $providerName = null)
    {
        $response = $this->runRequest($providerName);
        return $this->responseManager->processResponse($this->apiService, $this->provider, $response);
    }

    public function runRequest(string $providerName = null)
    {
        $this->setProvider($providerName);
        $this->setApiService();

        $providerServiceParams = $this->requestService->getRequestParametersByRequestName(
            $this->provider,
            $this->apiRequestName);
        $requestQueryArray = $this->buildRequestQuery($providerServiceParams);
        return $this->getRequest($requestQueryArray);
    }

    private function getRequest(array $requestQueryArray)
    {
        $url = "";
        switch ($this->providerService->getProviderPropertyValue($this->provider, self::API_AUTH_TYPE)) {
            case "oauth":
                $this->oath->setProvider($this->provider);
                $accessToken = $this->oath->getAccessToken();
                $endpoint = $this->getEndpoint();
                $this->apiRequest->setHeaders(["Authorization" => "Bearer " . $accessToken->getAccessToken()]);
                $this->apiRequest->setMethod($this->getMethod());
                $this->apiRequest->setUrl($this->provider->getProviderApiBaseUrl() . $endpoint);
                $this->apiRequest->setQuery($requestQueryArray);
                break;
            case "amazon":
                $service = $this->amazonApiManager->getApiRequest($this->apiService);
                $service->setAccessKey($this->provider->getProviderAccessKey());
                $service->setSecretKey($this->provider->getProviderSecretKey());
                $service->setRegion("eu-west-1");
                $service->setHost("webservices.amazon.co.uk");
                $service->setPartnerTag($this->provider->getProviderUserId());
                return $service->searchItems($this->query, $requestQueryArray['limit']);
                break;

            case "access_token":
                $endpoint = $this->getEndpoint();
                $this->apiRequest->setHeaders($this->getHeaders());
                $this->apiRequest->setMethod($this->getMethod());
                $this->apiRequest->setUrl($this->provider->getProviderApiBaseUrl() . $endpoint);
                $this->apiRequest->setQuery($requestQueryArray);
                break;
        }
        $this->eventsService->apiSendRequestEvent($this->apiRequest);
        try {
            return $this->apiClientHandler->sendRequest($this->apiRequest);
        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }

    private function getHeaders()
    {
        $headers = ["Content-Type" => "application/json;charset=utf-8"];
        $getHeaders = $this->getRequestConfig("headers");
        if ($getHeaders === null) {
            return $headers;
        }
        $headerArray = $getHeaders->getItemArrayValue();
        foreach ($headerArray as $item) {
            $headers[$item["name"]] = $this->filterParameterValue($item["value"]);
        }
        return $headers;
    }

    private function getEndpoint()
    {
        $endpoint = $this->getRequestConfig("endpoint");
        if ($endpoint === null) {
            throw new BadRequestHttpException("Endpoint is not specified in request config");
        }
        return $this->getQueryFilterValue($endpoint->getItemValue());
    }

    private function getMethod()
    {
        $method = $this->getRequestConfig("request_method");
        if ($method === null) {
            throw new BadRequestHttpException("Request method is not specified in request config");
        }
        return $this->getQueryFilterValue($method->getItemValue());
    }

    private function getQueryFilterValue($string)
    {
        if (preg_match_all('~\[(.*?)\]~', $string, $output)) {
            foreach ($output[1] as $key => $value) {
                if (array_key_exists($value, $this->queryArray)) {
                    $string = str_replace($output[0][$key], $this->queryArray[$value], $string, $count);
                } else {
                    return false;
                }
            }
        }
        return $string;
    }

    private function getRequestConfig(string $parameterName)
    {
        return $this->requestService->getRequestConfigByName($this->provider, $this->apiService, $parameterName);
    }

    public function buildRequestQuery(array $apiParamsArray)
    {
        $queryArray = [];
        foreach ($apiParamsArray as $requestParameter) {
            $paramValue = $this->filterParameterValue($requestParameter->getParameterValue());
            if ($paramValue || $paramValue === "") {
                $queryArray[$requestParameter->getParameterName()] = $paramValue;
            }
        }
        return $queryArray;
    }

    private function filterParameterValue($paramValue)
    {
        $replaceString = "";
        $replaceValue = "";
        if (preg_match_all('~\[(.*?)\]~', $paramValue, $output)) {
            foreach ($output[1] as $key => $value) {
                $filterReservedParam = $this->getReservedParamsValues($output[0][$key]);
                $paramValue = str_replace($output[0][$key], $filterReservedParam, $paramValue, $count);
            }
        }
        return $paramValue;
    }

    private function getReservedParamsValues($paramValue)
    {
        switch ($paramValue) {
            case self::PARAM_FILTER_KEYS["PROVIDER_USER_ID"]:
                return $this->provider->getProviderUserId();

            case self::PARAM_FILTER_KEYS["SECRET_KEY"]:
                return $this->provider->getProviderSecretKey();

            case self::PARAM_FILTER_KEYS["ACCESS_KEY"]:
                return $this->provider->getProviderAccessKey();

            case self::PARAM_FILTER_KEYS["QUERY"]:
                return $this->query;

            case self::PARAM_FILTER_KEYS["TIMESTAMP"]:
                $date = new \DateTime();
                return $date->format("Y-m-d h:i:s");

            default:
                $value = $this->getQueryFilterValue($paramValue);
                if (is_numeric($value)) {
                    return (int)$value;
                }
                return $value;
        }
    }

    public function setApiRequestName(string $apiRequestName)
    {
        $this->apiRequestName = $apiRequestName;
    }

    public function setApiService()
    {
        $apiService = $this->requestService->getRequestByName($this->provider, $this->apiRequestName);
        if ($apiService === null) {
            throw new BadRequestHttpException("Service request doesn't exist, check config.");
        }
        $this->apiService = $apiService;
    }

    /**
     * @param $providerName
     */
    public function setProvider($providerName): void
    {
        $this->provider = $this->providerService->getProviderByName($providerName);
        if ($this->provider === null) {
            throw new BadRequestHttpException("Provider in request not found...");
        }
    }

    public function setQuery(string $query)
    {
        $this->query = $query;
    }

    public function setTimestamp(string $timestamp)
    {
        $this->timestamp = $timestamp;
    }

    public function setCategory(string $category)
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getQueryArray()
    {
        return $this->queryArray;
    }

    /**
     * @param mixed $queryArray
     */
    public function setQueryArray($queryArray): void
    {
        $this->queryArray = $queryArray;
    }


}