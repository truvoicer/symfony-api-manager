<?php
namespace App\Controller\Api\Frontend;

use App\Controller\Api\BaseController;
use App\Service\Api\Operations\RequestOperation;
use App\Service\HttpRequestService;
use App\Service\SerializerService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Require ROLE_ADMIN for *every* controller method in this class.
 *
 * @IsGranted("ROLE_USER")
 */
class OperationsController extends BaseController
{

    private $httpRequestService;
    private $serializerService;

    public function __construct(HttpRequestService $httpRequestService, SerializerService $serializerService)
    {
        $this->httpRequestService = $httpRequestService;
        $this->serializerService = $serializerService;
    }

    /**
     * @Route("/api/operation/{service_request_name}", name="api_request_operation", methods={"GET"})
     * @param string $service_request_name
     * @param RequestOperation $requestOperation
     * @param Request $request
     * @return JsonResponse
     */
    public function searchOperation(string $service_request_name, RequestOperation $requestOperation, Request $request) {
        $data = $request->query->all();
        $requestOperation->setProviderName($data['provider']);
        $requestOperation->setApiRequestName($service_request_name);
        return new JsonResponse(
            $this->serializerService->entityToArray($requestOperation->runOperation($data)),
            Response::HTTP_OK);
    }
}
