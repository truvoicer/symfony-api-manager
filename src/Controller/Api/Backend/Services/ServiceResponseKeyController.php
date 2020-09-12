<?php
namespace App\Controller\Api\Backend\Services;

use App\Controller\Api\BaseController;
use App\Entity\ServiceResponseKey;
use App\Service\ApiServicesService;
use App\Service\HttpRequestService;
use App\Service\ProviderService;
use App\Service\ResponseKeysService;
use App\Service\SerializerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Contains Api endpoint functions for api service response keys related operations
 *
 * Require ROLE_ADMIN for *every* controller method in this class.
 *
 * @IsGranted("ROLE_ADMIN")
 */
class ServiceResponseKeyController extends BaseController
{
    private $providerService;
    private $serializerService;
    private $httpRequestService;
    private $apiServicesService;
    private $responseKeysService;

    /**
     * ServiceResponseKeyController constructor.
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
     * Get a list of response keys.
     * Returns a list of response keys based on the request query parameters
     *
     * @Route("/api/service/response/key/list", name="api_get_service_response_key_list", methods={"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getServiceResponseKeyList(Request $request)
    {
        $data = $request->query->all();
        $responseKeys = $this->responseKeysService->getResponseKeysByServiceId($data['service_id']);
        return $this->jsonResponseSuccess("success",
            $this->serializerService->entityArrayToArray($responseKeys));
    }

    /**
     * Get a single service response key
     * Returns a single service response key based on the id passed in the request url
     *
     * @Route("/api/service/response/key/{id}", name="api_get_service_response_key", methods={"GET"})
     * @param ServiceResponseKey $serviceResponseKey
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getServiceResponseKey(ServiceResponseKey $serviceResponseKey)
    {
        return $this->jsonResponseSuccess("success",
            $this->serializerService->entityToArray($serviceResponseKey));
    }

    /**
     * Create an api service response key based on request POST data
     * Returns json success message and api service response key data on successful creation
     * Returns error response and message on fail
     *
     * @param Request $request
     * @Route("/api/service/response/key/create", name="api_create_service_response_key", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createServiceResponseKey(Request $request) {
        $create = $this->responseKeysService->createServiceResponseKeys(
            $this->httpRequestService->getRequestData($request, true));

        if(!$create) {
            return $this->jsonResponseFail("Error inserting service response key");
        }
        return $this->jsonResponseSuccess("Service response key inserted",
            $this->serializerService->entityToArray($create, ['main']));
    }

    /**
     * Update an api service response key based on request POST data
     * Returns json success message and api service response key data on successful update
     * Returns error response and message on fail
     *
     * @param Request $request
     * @Route("/api/service/response/key/update", name="api_update_service_response_key", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateServiceResponseKey(Request $request)
    {
        $update = $this->responseKeysService->updateServiceResponseKeys(
            $this->httpRequestService->getRequestData($request, true));

        if(!$update) {
            return $this->jsonResponseFail("Error updating service response key");
        }
        return $this->jsonResponseSuccess("Service response key updated",
            $this->serializerService->entityToArray($update, ['main']));
    }

    /**
     * Delete an api service response key based on request POST data
     * Returns json success message and api service response key data on successful delete
     * Returns error response and message on fail
     *
     * @param Request $request
     * @Route("/api/service/response/key/delete", name="api_delete_service_response_key", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteServiceResponseKey(Request $request)
    {
        $requestData = $this->httpRequestService->getRequestData($request, true);
        $delete = $this->responseKeysService->deleteServiceResponseKey($requestData['item_id']);
        if (!$delete) {
            return $this->jsonResponseFail("Error deleting service response key", $this->serializerService->entityToArray($delete, ['main']));
        }
        return $this->jsonResponseSuccess("Response key service deleted.", $this->serializerService->entityToArray($delete, ['main']));
    }
}
