<?php

namespace App\Service\Api\Response;

use App\Entity\Provider;
use App\Entity\ServiceRequest;
use App\Service\Api\Response\Entity\ApiResponse;
use App\Service\Api\Response\Handlers\Json\JsonResponseHandler;
use App\Service\Api\Response\Handlers\Xml\XmlResponseHandler;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ResponseManager
{

    const CONTENT_TYPES = [
        "JSON" => "application/json",
        "XML" => "text/xml"
    ];

    private $jsonResponseHandler;
    private $xmlResponseHandler;

    public function __construct(JsonResponseHandler $jsonResponseHandler, XmlResponseHandler $xmlResponseHandler)
    {
        $this->jsonResponseHandler = $jsonResponseHandler;
        $this->xmlResponseHandler = $xmlResponseHandler;
    }

    public function getRequestContent(ServiceRequest $serviceRequest, Provider $provider, ResponseInterface $response)
    {
        $contentType = null;
        switch ($this->getContentType($response->getHeaders()['content-type'])) {
            case self::CONTENT_TYPES['JSON']:
                $contentType = "json";
                $content = $response->toArray();
                break;
            case self::CONTENT_TYPES['XML']:
                $contentType = "xml";
                $content = $response->getContent();
                break;
        }

        $apiResponse = new ApiResponse();
        $apiResponse->setContentType($contentType);
        $apiResponse->setRequestService($serviceRequest->getServiceRequestName());
        $apiResponse->setStatus("success");
        $apiResponse->setProvider($provider->getProviderName());
        $apiResponse->setRequestData($content);
        $apiResponse->setExtraData([]);
        return $apiResponse;
    }

    public function processResponse(ServiceRequest $serviceRequest, Provider $provider, ResponseInterface $response)
    {
        $contentType = null;
        switch ($this->getContentType($response->getHeaders()['content-type'])) {
            case self::CONTENT_TYPES['JSON']:
                $contentType = "json";
                $this->jsonResponseHandler->setApiService($serviceRequest);
                $this->jsonResponseHandler->setResponseArray($response->toArray());
                $this->jsonResponseHandler->setProvider($provider);
                $listItems = $this->jsonResponseHandler->getListItems();
                $listData = $this->jsonResponseHandler->getListData();
                break;
            case self::CONTENT_TYPES['XML']:
                $contentType = "xml";
                $this->xmlResponseHandler->setApiService($serviceRequest);
                $this->xmlResponseHandler->setProvider($provider);
                $this->xmlResponseHandler->setResponseKeysArray();
                $this->xmlResponseHandler->setResponseArray($response->getContent());
                $listItems = $this->xmlResponseHandler->getListItems();
                $listData = $this->xmlResponseHandler->getListData();
                break;
        }
        $apiResponse = new ApiResponse();
        $apiResponse->setContentType($contentType);
        $apiResponse->setRequestService($serviceRequest->getServiceRequestName());
        $apiResponse->setCategory($serviceRequest->getService()->getCategory()->getCategoryName());
        $apiResponse->setStatus("success");
        $apiResponse->setProvider($provider->getProviderName());
        $apiResponse->setRequestData($this->buildArray($listItems));
        $apiResponse->setExtraData($listData);
        return $apiResponse;
    }

    private function buildArray(array $array)
    {
        $buildArray = [];
        foreach ($array as $item) {
            array_push($buildArray, $item);
        }
        return $buildArray;
    }

    private function getContentType(array $contentTypeArray = [])
    {
        foreach (self::CONTENT_TYPES as $key => $item) {
            if (strpos($contentTypeArray[0], $item) !== false) {
                return $item;
            }
        }
        return false;
    }
}