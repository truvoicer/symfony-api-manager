<?php
namespace App\Controller\Api\Backend\Services;

use App\Controller\Api\BaseController;
use App\Entity\ServiceRequest;
use App\Service\ApiServicesService;
use App\Service\HttpRequestService;
use App\Service\ProviderService;
use App\Service\ResponseKeysService;
use App\Service\SerializerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Contains Api endpoint functions for api service request response keys related operations
 *
 * Require ROLE_ADMIN for *every* controller method in this class.
 *
 * @IsGranted("ROLE_ADMIN")
 */
class ServiceRequestResponseKeyController extends BaseController
{
    private $providerService;
    private $serializerService;
    private $httpRequestService;
    private $apiServicesService;
    private $responseKeysService;

    /**
     * ServiceRequestResponseKeyController constructor.
     * Initialises services used in this controller
     *
     * @param ProviderService $providerService
     * @param HttpRequestService $httpRequestService
     * @param SerializerService $serializerService
     * @param ApiServicesService $apiServicesService
     * @param ResponseKeysService $responseKeysService
     */
    public function __construct(ProviderService $providerService, HttpRequestService $httpRequestService,
                                SerializerService $serializerService, ApiServicesService $apiServicesService,
                                ResponseKeysService $responseKeysService)
    {
        $this->providerService = $providerService;
        $this->serializerService = $serializerService;
        $this->httpRequestService = $httpRequestService;
        $this->apiServicesService = $apiServicesService;
        $this->responseKeysService = $responseKeysService;
    }

    /**
     * Get a list of service request response keys.
     * Returns a list of service request response keys based on the request query parameters
     *
     * @Route("/api/service/request/{id}/response/key/list", name="api_get_request_response_key_list", methods={"GET"})
     * @param ServiceRequest $serviceRequest
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getRequestResponseKeyList(ServiceRequest $serviceRequest, Request $request)
    {
        $responseKeys = $this->responseKeysService->getRequestResponseKeys(
            $serviceRequest->getId(),
            $request->get('sort', "key_name"),
            $request->get('order', "asc"),
            (int) $request->get('count', null)
        );
        return $this->jsonResponseSuccess("success", $responseKeys);
    }

    /**
     * Get a single service request response key
     * Returns a single service request response key based on the id passed in the request url
     *
     * @Route("/api/service/request/{id}/response/key/{response_key_id}", name="api_get_request_response_key", methods={"GET"})
     * @param ServiceRequest $serviceRequest
     * @param int $response_key_id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getRequestResponseKey(ServiceRequest $serviceRequest, int $response_key_id)
    {
        $getRequestResponseKey = $this->responseKeysService->getRequestResponseKeyObjectById($serviceRequest, $response_key_id);
        return $this->jsonResponseSuccess("success", $getRequestResponseKey);
    }

    /**
     * Create an api service request response key based on request POST data
     * Returns json success message and api service request response key data on successful creation
     * Returns error response and message on fail
     *
     * @param Request $request
     * @Route("/api/service/request/response/key/create", name="api_create_request_response_key", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createRequestResponseKey(Request $request) {
          $requestData = $this->httpRequestService->getRequestData($request);
        $create = $this->responseKeysService->createRequestResponseKey($requestData->data);
        if(!$create) {
            return $this->jsonResponseFail("Error adding response key.");
        }
        return $this->jsonResponseSuccess("Successfully added response key.",
            $this->serializerService->entityToArray($create));
    }

    /**
     * Update an api service request response key based on request POST data
     * Returns json success message and api service request response key data on successful update
     * Returns error response and message on fail
     *
     * @param Request $request
     * @Route("/api/service/request/response/key/update", name="api_update_request_response_key", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateRequestResponseKey(Request $request)
    {
        $update = $this->responseKeysService->updateRequestResponseKey(
            $this->httpRequestService->getRequestData($request, true));

        if(!$update) {
            return $this->jsonResponseFail("Error updating service response key");
        }
        return $this->jsonResponseSuccess("Service response key updated",
            $this->serializerService->entityToArray($update, ['main']));
    }

    /**
     * Delete  an api service request response key based on request POST data
     * Returns json success message and api service request response key data on successful delete
     * Returns error response and message on fail
     *
     * @param Request $request
     * @Route("/api/service/request/response/key/delete", name="api_delete_request_response_key", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteRequestResponseKey(Request $request)
    {
        $requestData = $this->httpRequestService->getRequestData($request, true);
        $delete = $this->responseKeysService->deleteRequestResponseKey($requestData['extra']['service_request_id'], $requestData['item_id']);
        if (!$delete) {
            return $this->jsonResponseFail("Error deleting service response key", $this->serializerService->entityToArray($delete, ['main']));
        }
        return $this->jsonResponseSuccess("Response key service deleted.", $this->serializerService->entityToArray($delete, ['main']));
    }
}
