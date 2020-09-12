<?php
namespace App\Events;

use App\Service\Api\Client\Entity\ApiRequest;
use Symfony\Contracts\EventDispatcher\Event;

class ApiSendRequestEvent extends Event
{
    public const NAME = "api.request.sent";

    protected $apiRequest;

    public function __construct(ApiRequest $apiRequest)
    {
        $this->apiRequest = $apiRequest;
    }

    /**
     * @return ApiRequest
     */
    public function getApiRequest(): ApiRequest
    {
        return $this->apiRequest;
    }
}