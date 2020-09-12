<?php
namespace App\Service\Api\Client\Amazon\Requests;

use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsRequest;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsResource;
use App\Service\Api\Client\Amazon\AmazonApiManager;

class ItemSearch extends AmazonApiManager
{
    private $resources;

    function searchItems(string $searchQuery = null, int $count, string $searchIndex = "All")
    {
        $this->setResources();
        $searchItemsRequest = new SearchItemsRequest();
        $searchItemsRequest->setSearchIndex($searchIndex);
        $searchItemsRequest->setKeywords($searchQuery);
        $searchItemsRequest->setItemCount($count);
        $searchItemsRequest->setPartnerTag($this->partnerTag);
        $searchItemsRequest->setPartnerType($this->partnerType);
        $searchItemsRequest->setResources($this->resources);

        # Validating request
        $invalidPropertyList = $searchItemsRequest->listInvalidProperties();
        $length = count($invalidPropertyList);
        if ($length > 0) {
            dd($invalidPropertyList);
        }

        # Sending the request
        try {
            return $this->newApiInstance()->searchItems($searchItemsRequest);

        }
        catch (\Exception $exception) {
            dd($exception);
        }
    }

    /**
     * @param mixed $resources
     */
    public function setResources(): void
    {
        $this->resources = [
            SearchItemsResource::ITEM_INFOTITLE,
            SearchItemsResource::OFFERSLISTINGSPRICE
        ];
    }
}