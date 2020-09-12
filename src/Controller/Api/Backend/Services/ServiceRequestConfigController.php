<?php
namespace App\Controller\Api\Backend\Services;

use App\Controller\Api\BaseController;
use App\Entity\ServiceRequestConfig;
use App\Service\ApiServicesService;
use App\Service\HttpRequestService;
use App\Service\ProviderService;
use App\Service\RequestConfigService;
use App\Service\SerializerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Contains Api endpoint functions for service request config related operations
 *
 * Require ROLE_ADMIN for *every* controller method in this class.
 *
 * @IsGranted("ROLE_ADMIN")
 */
class ServiceRequestConfigController extends BaseController
{
    // Initialise services for this controller
    private $providerService;
    private $serializerService;
    private $httpRequestService;
    private $apiServicesService;
    private $requestConfigService;

    /**
     * ServiceRequestConfigController constructor.
     * Initialise services for this controller
     *
     * @param ProviderService $providerService
     * @param HttpRequestService $httpRequestService
     * @param SerializerService $serializerService
     * @param ApiServicesService $apiServicesService
     * @param RequestConfigService $requestConfigService
     */
    public function __construct(ProviderService $providerService, HttpRequestService $httpRequestService,
                                SerializerService $serializerService, ApiServicesService $apiServicesService,
                                RequestConfigService $requestConfigService)
    {
        // Initialise services for this controller
        $this->providerService = $providerService;
        $this->serializerService = $serializerService;
        $this->httpRequestService = $httpRequestService;
        $this->apiServicesService = $apiServicesService;
        $this->requestConfigService = $requestConfigService;
    }

    /**
     * Get list of service request configs function
     * Returns a list of service request configs based on the request query parameters
     *
     * @Route("/api/service/request/config/list", name="api_get_service_request_config_list", methods={"GET"})
     */
    public function getRequestConfigList(Request $request)
    {
        $responseKeys = $this->requestConfigService->findByParams(
            $request->get('service_request_id'),
            $request->get('sort', "item_name"),
            $request->get('order', "asc"),
            (int) $request->get('count', null)
        );
        return $this->jsonResponseSuccess("success",
            $this->serializerService->entityArrayToArray($responseKeys, ["list"]));
    }

    /**
     * Get a single service request config
     * Returns a single service request config based on the id passed in the request url
     *
     * @Route("/api/service/request/config/{id}", name="api_get_service_request_config", methods={"GET"})
     * @param ServiceRequestConfig $serviceRequestConfig
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getServiceRequestConfig(ServiceRequestConfig $serviceRequestConfig)
    {
        return $this->jsonResponseSuccess("success",
            $this->serializerService->entityToArray($serviceRequestConfig, ["single"]));
    }

    /**
     * Create an service request config based on request POST data
     * Returns json success message and service request config data on successful creation
     * Returns error response and message on fail
     *
     * @param Request $request
     * @Route("/api/service/request/config/create", name="api_create_service_request_config", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createRequestConfig(Request $request) {
        $create = $this->requestConfigService->createRequestConfig(
            $this->httpRequestService->getRequestData($request, true));

        if(!$create) {
            return $this->jsonResponseFail("Error inserting config item");
        }
        return $this->jsonResponseSuccess("Config item inserted",
            $this->serializerService->entityToArray($create, ['main']));
    }

    /**
     * Update a service request config based on request POST data
     * Returns json success message and service request config data on successful update
     *
     * Returns error response and message on fail
     * @param Request $request
     * @Route("/api/service/request/config/update", name="api_update_service_request_config", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateRequestConfig(Request $request)
    {
        $update = $this->requestConfigService->updateRequestConfig(
            $this->httpRequestService->getRequestData($request, true));

        if(!$update) {
            return $this->jsonResponseFail("Error updating config item");
        }
        return $this->jsonResponseSuccess("Config item updated",
            $this->serializerService->entityToArray($update, ['main']));
    }

    /**
     * Delete a service request config based on request POST data
     * Returns json success message and service request config data on successful deletion
     *
     * Returns error response and message on fail
     * @param Request $request
     * @Route("/api/service/request/config/delete", name="api_delete_service_request_config", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteRequestConfig(Request $request)
    {
        $requestData = $this->httpRequestService->getRequestData($request, true);
        $delete = $this->requestConfigService->deleteRequestConfig($requestData['item_id']);
        if (!$delete) {
            return $this->jsonResponseFail("Error deleting config item", $this->serializerService->entityToArray($delete, ['main']));
        }
        return $this->jsonResponseSuccess("Config item deleted.", $this->serializerService->entityToArray($delete, ['main']));
    }
}
