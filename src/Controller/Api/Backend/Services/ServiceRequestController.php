<?php
namespace App\Controller\Api\Backend\Services;

use App\Controller\Api\BaseController;
use App\Entity\ServiceRequest;
use App\Service\Api\Operations\GetOperation;
use App\Service\Api\Operations\RequestOperation;
use App\Service\Api\Operations\SearchOperation;
use App\Service\ApiServicesService;
use App\Service\HttpRequestService;
use App\Service\ProviderService;
use App\Service\RequestService;
use App\Service\SerializerService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Contains Api endpoint functions for api service related request operations
 *
 * Require ROLE_ADMIN for *every* controller method in this class.
 *
 * @IsGranted("ROLE_ADMIN")
 */
class ServiceRequestController extends BaseController
{
    // Initialise services variables for this controller
    private $providerService;
    private $serializerService;
    private $httpRequestService;
    private $apiServicesService;
    private $requestService;

    /**
     * ServiceRequestController constructor.
     * Initialise services for this controller
     * @param ProviderService $providerService
     * @param HttpRequestService $httpRequestService
     * @param SerializerService $serializerService
     * @param ApiServicesService $apiServicesService
     * @param RequestService $requestService
     */
    public function __construct(ProviderService $providerService, HttpRequestService $httpRequestService,
                                SerializerService $serializerService, ApiServicesService $apiServicesService,
                                RequestService $requestService)
    {
        $this->providerService = $providerService;
        $this->serializerService = $serializerService;
        $this->httpRequestService = $httpRequestService;
        $this->apiServicesService = $apiServicesService;
        $this->requestService = $requestService;
    }

    /**
     * Get list of service requests function
     * Returns a list of service requests based on the request query parameters
     *
     * @Route("/api/service/request/list", name="api_get_service_request_list", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getServiceRequestList(Request $request)
    {
        $getServices = $this->requestService->getServiceRequestByProvider(
            $request->get('provider_id'),
            $request->get('sort', "service_request_name"),
            $request->get('order', "asc"),
            (int) $request->get('count', null)
        );
        return $this->jsonResponseSuccess("success",
            $this->serializerService->entityArrayToArray($getServices, ["list"]));
    }

    /**
     * Get a single api service request
     * Returns a single api service request based on the id passed in the request url
     *
     * @Route("/api/service/request/{id}", name="api_get_service_request", methods={"GET"})
     * @param ServiceRequest $serviceRequest
     * @return JsonResponse
     */
    public function getServiceRequest(ServiceRequest $serviceRequest)
    {
        return $this->jsonResponseSuccess("success",
            $this->serializerService->entityToArray($serviceRequest, ["single"]));
    }

    /**
     * Get a provider service request based on the provider and service in the request data
     * Returns a single provider service request
     *
     * @Route("/api/provider/service/request", name="api_get_provider_service_request", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getProviderServiceRequest(Request $request)
    {
        $data = $request->query->all();
        $getProperties = $this->requestService->getProviderServiceRequest($data['service_id'], $data['provider_id']);
        return $this->jsonResponseSuccess("success",
            $this->serializerService->entityToArray($getProperties));
    }

    /**
     * Create an api service request based on request POST data
     * Returns json success message and api service request data on successful creation
     * Returns error response and message on fail
     *
     * @param Request $request
     * @Route("/api/service/request/create", name="api_create_service_request", methods={"POST"})
     * @return JsonResponse
     */
    public function createServiceRequest(Request $request) {
        $create = $this->requestService->createServiceRequest(
            $this->httpRequestService->getRequestData($request, true));

        if(!$create) {
            return $this->jsonResponseFail("Error inserting service request");
        }
        return $this->jsonResponseSuccess("Service request inserted",
            $this->serializerService->entityToArray($create, ['main']));
    }

    /**
     * Update an api service request based on request POST data
     * Returns json success message and api service request data on successful update
     * Returns error response and message on fail
     *
     * @param Request $request
     * @Route("/api/service/request/update", name="api_update_service_request", methods={"POST"})
     * @return JsonResponse
     */
    public function updateServiceRequest(Request $request)
    {
        $update = $this->requestService->updateServiceRequest(
            $this->httpRequestService->getRequestData($request, true));

        if(!$update) {
            return $this->jsonResponseFail("Error updating service request");
        }
        return $this->jsonResponseSuccess("Service request updated",
            $this->serializerService->entityToArray($update, ['main']));
    }

    /**
     * Runs an api request to a provider based on the request query data
     *
     * Required fields in query data:
     * - request_type
     * - provider
     * - (Parameters set for the provider service request)
     *
     * @param RequestOperation $requestOperation
     * @param Request $request
     * @return JsonResponse
     * @Route("/api/service/api/request/run", name="run_service_api_request", methods={"GET"})
     */
    public function runApiRequest(RequestOperation $requestOperation, Request $request) {
        $data = $request->query->all();

        if (!isset($data["request_type"]) || $data["request_type"] === null || $data["request_type"] === "") {
            return $this->jsonResponseFail("Api request type not found in the request.");
        }

        $requestOperation->setProviderName($data['provider']);
        $requestOperation->setApiRequestName($data["request_type"]);
        $runApiRequest = $requestOperation->getOperationRequestContent($data);

        return new JsonResponse(
            $this->serializerService->entityToArray($runApiRequest),
            Response::HTTP_OK);
    }

    /**
     * Duplicate a providers' service request
     *
     * @param Request $request
     * @Route("/api/service/request/duplicate", name="api_duplicate_service_request", methods={"POST"})
     * @return JsonResponse
     */
    public function duplicateServiceRequest(Request $request)
    {
        $update = $this->requestService->duplicateServiceRequest(
            $this->httpRequestService->getRequestData($request, true));

        if(!$update) {
            return $this->jsonResponseFail("Error duplicating service request");
        }
        return $this->jsonResponseSuccess("Service request duplicated",
            $this->serializerService->entityToArray($update, ['main']));
    }

    /**
     * Delete an api service request based on request POST data
     * Returns json success message and api service request data on successful delete
     * Returns error response and message on fail
     *
     * @param Request $request
     * @Route("/api/service/request/delete", name="api_delete_service_request", methods={"POST"})
     * @return JsonResponse
     */
    public function deleteServiceRequest(Request $request)
    {
        $requestData = $this->httpRequestService->getRequestData($request, true);
        $delete = $this->requestService->deleteServiceRequest($requestData['item_id']);
        if (!$delete) {
            return $this->jsonResponseFail("Error deleting service request", $this->serializerService->entityToArray($delete, ['main']));
        }
        return $this->jsonResponseSuccess("Service request deleted.", $this->serializerService->entityToArray($delete, ['main']));
    }
}
