<?php
namespace App\Service\Api\Operations;

use App\Service\Api\Response\Entity\ApiResponse;
use App\Service\Api\Response\Entity\SearchResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RequestOperation extends BaseOperations
{
    private $providerName;

    private function initialize(array $query = []) {
        if (count($query) === 0) {
            throw new BadRequestHttpException("Query empty in itemquery");
        }

        $this->setQueryArray($query);
        if (isset($query['query'])) {
            $this->setQuery($query['query']);
        }
    }

    public function runOperation(array $query = []) {
        $this->initialize($query);
        return $this->buildResponseObject($this->getOperationResponse($this->providerName));
    }

    public function getOperationRequestContent(array $query = []) {
        $this->initialize($query);
        return $this->getRequestContent($this->providerName);
    }

    private function buildResponseObject(ApiResponse $apiResponse)
    {
        $getResponse = new SearchResponse();
        $getResponse->setStatus($apiResponse->getStatus());
        $getResponse->setContentType($apiResponse->getContentType());
        $getResponse->setRequestService($apiResponse->getRequestService());
        $getResponse->setCategory($apiResponse->getCategory());
        $getResponse->setProvider($apiResponse->getProvider());
        $getResponse->setRequestData($apiResponse->getRequestData());
        $getResponse->setExtraData($apiResponse->getExtraData());
        return $getResponse;
    }
    public function setProviderName(string $providerName = null)
    {
        $this->providerName = $providerName;
    }

}