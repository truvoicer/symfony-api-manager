<?php
namespace App\EventListener;

use App\Events\ApiSendRequestEvent;
use App\Service\Api\Client\ApiClientHandler;

class ApiSendRequestListener
{
    private $apiClientHandler;
    private $responseManager;

    public function __construct(ApiClientHandler $apiClientHandler)
    {
        $this->apiClientHandler = $apiClientHandler;
    }

    public function onApiRequestSent(ApiSendRequestEvent $event) {
        //
    }
}