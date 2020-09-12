<?php
namespace App\Service\Api\Response\Handlers\Json;

use App\Service\Api\Response\Handlers\ResponseHandler;

class JsonResponseHandler extends ResponseHandler
{
    public function getListItems()
    {
        $this->setResponseKeysArray(
            $this->requestService->getResponseKeysByRequest($this->provider, $this->apiService)
        );
        return $this->buildListItems($this->getItemList());
    }

    public function getListData()
    {
        $this->setResponseKeysArray(
            $this->requestService->getResponseKeysByRequest($this->provider, $this->apiService)
        );
        return $this->buildParentListItems($this->getParentItemList());
    }

    /**
     * @param mixed $responseArray
     */
    public function setResponseArray($responseArray): void
    {
        $this->responseArray = $responseArray;
    }
}