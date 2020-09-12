<?php

namespace App\Service\Api\Client\Amazon;

use Amazon\ProductAdvertisingAPI\v1\ApiException;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\api\DefaultApi;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\PartnerType;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\ProductAdvertisingAPIClientException;
use Amazon\ProductAdvertisingAPI\v1\Configuration;
use App\Service\Api\ApiBase;
use App\Service\Api\Client\Amazon\Requests\ItemSearch;


class AmazonApiManager extends ApiBase
{
    private $config;
    private $apiInstance;
    protected $partnerTag;
    protected $partnerType;

    public function __construct()
    {
        $this->config = new Configuration();
        $this->setPartnerType(PartnerType::ASSOCIATES);
    }

    public function newApiInstance()
    {
        return $this->apiInstance = new DefaultApi(
            new \GuzzleHttp\Client(),
            $this->config
        );
    }

    public function getApiRequest(string $apiRequest)
    {
        switch ($apiRequest) {
            case self::API_REQUESTS["ITEM_SEARCH"]:
                return new ItemSearch();
        }
    }

    public function setPartnerTag($partnerTag)
    {
        $this->partnerTag = $partnerTag;
    }

    public function setPartnerType($partnerType)
    {
        $this->partnerType = $partnerType;
    }

    public function setAccessKey($accessKey)
    {
        $this->config->setAccessKey($accessKey);
    }

    public function setSecretKey($secretKey)
    {
        $this->config->setSecretKey($secretKey);
    }

    public function setHost($host)
    {
        $this->config->setHost($host);
    }

    public function setRegion($region)
    {
        $this->config->setRegion($region);
    }

}