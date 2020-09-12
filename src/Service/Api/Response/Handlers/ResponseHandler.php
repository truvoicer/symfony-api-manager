<?php


namespace App\Service\Api\Response\Handlers;


use App\Entity\Provider;
use App\Entity\ServiceRequest;
use App\Entity\ServiceRequestResponseKey;
use App\Service\Api\ApiBase;
use App\Service\ProviderService;
use App\Service\RequestService;
use App\Service\ResponseKeysService;
use App\Service\XmlService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ResponseHandler extends ApiBase
{
    protected $providerService;
    protected $requestService;
    protected $responseKeysService;
    protected $provider;
    protected $apiService;
    protected $xmlService;
    protected $responseArray;
    protected $response;
    protected $responseKeysArray;

    private $needleMatchArrayValue = "=";
    private $needleMatchArrayKey = ".";

    public function __construct(ProviderService $providerService, RequestService $requestService,
                                XmlService $xmlService, ResponseKeysService $responseKeysService)
    {
        $this->providerService = $providerService;
        $this->requestService = $requestService;
        $this->xmlService = $xmlService;
        $this->responseKeysService = $responseKeysService;
    }

    protected function getItemList()
    {
        $itemsArrayString = $this->getRequestResponseKeyByName($this->responseKeysArray['ITEMS_ARRAY']);
        if ($itemsArrayString === null) {
            return [$this->responseArray];
        } elseif ($itemsArrayString->getResponseKeyValue() === "") {
            return [$this->responseArray];
        }

        $itemsArray = array_map(function ($item) {
            return $this->filterItemsArrayValue($item)["value"];
        }, explode(".", $itemsArrayString->getResponseKeyValue()));
        $getArrayItems = $this->getArrayItems($this->responseArray, $itemsArray);
        if ($getArrayItems === "") {
            throw new BadRequestHttpException("Items list is empty");
        }

        return $getArrayItems;
    }

    protected function getParentItemList()
    {
        $array = [];
        $itemsArrayString = $this->getRequestResponseKeyByName($this->responseKeysArray['ITEMS_ARRAY']);
        if ($itemsArrayString !== null) {
            foreach ($this->responseArray as $key => $value) {
                if ($key !== $itemsArrayString->getResponseKeyValue()) {
                    $itemsArray = explode(".", $key);
                    $array[$key] = $this->getArrayItems($this->responseArray, $itemsArray);
                }
            }
        }
        return $array;
    }

    protected function buildListItems(array $itemList)
    {

        return array_map(function ($item) {
            $itemList = [];
            foreach ($this->responseKeysArray as $keys) {
                $getKey = $this->getRequestResponseKeyByName($keys);
                if ($getKey !== null && $getKey->getListItem()) {
                    if ($getKey->getShowInResponse()) {
                        $keyArray = explode(".", $getKey->getResponseKeyValue());
                        $getItemValue = $this->getArrayItems($item, $keyArray);
                        if ($getKey->getHasArrayValue()) {
                            $itemList[$keys] = $this->getRequestKeyArrayValue($getItemValue, $getKey);
                        } else {
                            $itemList[$keys] = $this->buildResponseKeyValue(
                                $getItemValue,
                                $getKey
                            );
                        }
                    }
                }
            }
            $itemList["provider"] = $this->provider->getProviderName();
            return $itemList;
        }, $itemList);
    }

    protected function buildResponseKeyValue(string $keyValue, ServiceRequestResponseKey $requestResponseKey)
    {
        $responseKeyValue = $keyValue;
        if ($requestResponseKey->getPrependExtraData()) {
            $prependValue = $requestResponseKey->getPrependExtraDataValue();
            if ($prependValue !== null && $prependValue !== "") {
                $responseKeyValue = $requestResponseKey->getPrependExtraDataValue() . $responseKeyValue;
            }
        }
        if ($requestResponseKey->getAppendExtraData()) {
            $appendValue = $requestResponseKey->getAppendExtraDataValue();
            if ($appendValue !== null && $appendValue !== "") {
                $responseKeyValue = $responseKeyValue . $requestResponseKey->getAppendExtraDataValue();
            }
        }
        return $this->replaceReservedResponseKeyValue($responseKeyValue);
    }

    protected function buildParentListItems(array $itemList)
    {
        $buildList = [];
        foreach ($this->responseKeysArray as $keys) {
            $getKey = $this->getRequestResponseKeyByName($keys);
            if ($getKey !== null) {
                if (!$getKey->getListItem() && $getKey->getShowInResponse()) {
                    $keyArray = explode(".", $getKey->getResponseKeyValue());
                    $buildList[$keys] = $this->getArrayItems($itemList, $keyArray);
                }
            }
        }
        return $buildList;
    }

    protected function getArrayItems($arrayItems, $queryArray)
    {
        foreach ($queryArray as $value) {
            $item = $value;
            unset($queryArray[array_search($value, $queryArray)]);
            if ($arrayItems === "" || !array_key_exists($value, $arrayItems)) {
                return "";
            }
            return $this->getArrayItems($arrayItems[$item], $queryArray);
        }
        return $arrayItems;
    }

    protected function getRequestKeyArrayValue($itemArrayValue, ServiceRequestResponseKey $requestResponseKey = null)
    {
        if ($requestResponseKey === null || !is_array($itemArrayValue)) {
            return null;
        }
        return $this->getReturnDataType($requestResponseKey, $itemArrayValue);
    }

    private function getReturnDataType(ServiceRequestResponseKey $requestResponseKey, $itemArrayValue)
    {
        switch ($requestResponseKey->getReturnDataType()) {
            case "object":
                return $this->buildRequestKeyObjectValue($requestResponseKey, $itemArrayValue);
            case "array":
                return $this->buildRequestKeyArrayValue($requestResponseKey, $itemArrayValue);
            case "text":
            default:
                return $this->buildResponseKeyValue(
                    $this->buildRequestKeyTextValue($requestResponseKey, $itemArrayValue),
                    $requestResponseKey
                );
        }
    }

    private function buildRequestKeyArrayValue(ServiceRequestResponseKey $requestResponseKey, $itemArrayValue)
    {
        $buildArray = [];
        $i = 0;
        foreach ($itemArrayValue as $item) {
            $array = [];
            foreach ($requestResponseKey->getArrayKeys() as $arrayKey) {
                if (is_array($item)) {
                    $array[$arrayKey["name"]] = $this->getRequestKeyArrayItemValue($arrayKey["name"], $arrayKey["value"], $item);
                }
            }
            if (count($array) > 0) {
                array_push($buildArray, $array);
            }
            $i++;
        }
        return $buildArray;
    }

    private function buildRequestKeyObjectValue(ServiceRequestResponseKey $requestResponseKey, $itemArrayValue)
    {
        $buildArray = [];
        foreach ($itemArrayValue as $item) {
            foreach ($requestResponseKey->getArrayKeys() as $arrayKey) {
                if (is_array($item)) {
                    $buildArray[$arrayKey["name"]] = $this->getRequestKeyArrayItemValue($arrayKey["name"], $arrayKey["value"], $item);
                }
            }
        }
        return $buildArray;
    }

    private function buildRequestKeyTextValue(ServiceRequestResponseKey $requestResponseKey, $itemArrayValue)
    {
        foreach ($itemArrayValue as $item) {
            foreach ($requestResponseKey->getArrayKeys() as $arrayKey) {
                if (is_array($item)) {
                    return $this->getRequestKeyArrayItemValue($arrayKey["name"], $arrayKey["value"], $item);
                }
            }
        }
    }

    private function getRequestKeyArrayItemValue($objectKeyToReturn, $conditions, $item)
    {
        if (strpos($conditions, $this->needleMatchArrayValue) !== false) {
            $needle = $this->needleMatchArrayValue;
        } else if (strpos($conditions, $this->needleMatchArrayKey) !== false) {
            $needle = $this->needleMatchArrayKey;
        } else {
            if (array_key_exists($conditions, $item)) {
                return $item[$conditions];
            }
            return null;
        }
        $itemsArray = explode($needle, $conditions);
        switch ($needle) {
            case $this->needleMatchArrayValue:
                $compareValue = $itemsArray[1];
                $key = explode($this->needleMatchArrayKey, $itemsArray[0]);
                if (count($key) > 1) {
                    $compareKey = $key[count($key) - 1];
                    unset($key[count($key) - 1]);
                } else {
                    $compareKey = $key[0];
                }
                $arrayItem = $this->getArrayItems($item, $key);
                if (array_key_exists($compareKey, $arrayItem) &&
                    $arrayItem[$compareKey] == $compareValue) {
                    return $arrayItem[$objectKeyToReturn];
                }
                break;
            case $this->needleMatchArrayKey:
                return $this->getArrayItems($item, $itemsArray);
        }
        return false;
    }

    protected function filterItemsArrayValue($string)
    {
        if (preg_match_all('~\[(.*?)\]~', $string, $output)) {
            return [
                "value" => $output[1][0],
                "brackets" => true
            ];
        }
        return [
            "value" => $string,
            "brackets" => false
        ];
    }

    private function replaceReservedResponseKeyValue($string)
    {
        if (preg_match_all('~\[(.*?)\]~', $string, $output)) {
            foreach ($output[0] as $key => $value) {
                $string = str_replace($output[0][$key], $this->getReservedParamsValues($value), $string);
            }
        }
        return $string;
    }

    private function getReservedParamsValues($value)
    {
        switch ($value) {
            case self::PARAM_FILTER_KEYS["API_BASE_URL"]:
                return $this->provider->getProviderApiBaseUrl();
            default:
                return $value;
        }
    }

    protected function getRequestResponseKeyByName(string $keyName)
    {
        return $this->responseKeysService->getRequestResponseKeyByName($this->provider, $this->apiService, $keyName);
    }

    /**
     * @return mixed
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param mixed $provider
     */
    public function setProvider($provider): void
    {
        $this->provider = $provider;
    }

    /**
     * @return mixed
     */
    public function getApiService()
    {
        return $this->apiService;
    }

    /**
     * @param mixed $apiService
     */
    public function setApiService(ServiceRequest $apiService): void
    {
        $this->apiService = $apiService;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param mixed $response
     */
    public function setResponse($response): void
    {
        $this->response = $response;
    }


    /**
     * @return mixed
     */
    public function getResponseArray()
    {
        return $this->responseArray;
    }

    /**
     * @param Provider $provider
     * @param ServiceRequest $serviceRequest
     */
    public function setResponseKeysArray(): void
    {
        $buildArray = [];
        foreach ($this->requestService->getResponseKeysByRequest($this->provider, $this->apiService) as $key => $value) {
            $buildArray[$value->getKeyName()] = $value->getKeyValue();
        }
        $this->responseKeysArray = $buildArray;
    }
}