<?php

namespace App\Service\Api\Response\Handlers\Xml;

use App\Service\Api\Response\Handlers\ResponseHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class XmlResponseHandler extends ResponseHandler
{

    public function getListItems()
    {
        return $this->buildListItems($this->getItemList());
    }

    public function getListData()
    {
        return $this->buildParentListItems($this->getParentItemList());
    }

    protected function buildListItems(array $itemList)
    {
        return array_map(function ($item) {
            $itemList = [];
            foreach ($this->responseKeysArray as $keys) {
                $getKey = $this->getRequestResponseKeyByName($keys);
                if ($getKey !== null && $getKey->getListItem()) {
                    $keyArray = explode(".", $getKey->getResponseKeyValue());
                    $getAttribute = $this->getAttribute($getKey->getResponseKeyValue(), $item);
                    if ($getAttribute && $getKey->getShowInResponse()) {
                        $itemList[$keys] = $getAttribute;
                    } elseif ($getKey->getShowInResponse()) {
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


    private function getAttribute(string $keyValue, array $itemArray)
    {
        if (strpos($keyValue, "attribute." === false)) {
            return false;
        }
        $keyArray = explode(".", $keyValue);

        if (isset($keyArray[2])) {
            return $itemArray["attributes"][$keyArray[2]];
        }
        return false;
    }

    /**
     * @param string $responseContent
     */
    public function setResponseArray(string $responseContent): void
    {
        if ($this->xmlService->checkXmlErrors($responseContent)) {
            throw new BadRequestHttpException("item_request_error");
        }
        $itemsArrayString = $this->getRequestResponseKeyByName($this->responseKeysArray['ITEMS_ARRAY'])->getResponseKeyValue();
        $this->responseArray = $this->xmlService->convertXmlToArray($responseContent,
            $this->filterItemsArrayValue($itemsArrayString)["value"],
            $this->filterItemsArrayValue($itemsArrayString)["brackets"]
        );
    }
}