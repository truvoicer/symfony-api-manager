<?php

namespace App\Controller\Api\Backend\Services;

use App\Controller\Api\BaseController;
use App\Entity\Service;
use App\Service\ApiServicesService;
use App\Service\HttpRequestService;
use App\Service\ProviderService;
use App\Service\SerializerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Contains Api endpoint functions for api service related operations
 *
 * Require ROLE_ADMIN for *every* controller method in this class.
 *
 * @IsGranted("ROLE_ADMIN")
 */
class ServiceController extends BaseController
{
    private $providerService;   // Initialise provider service
    private $serializerService; // Initialise serializer service
    private $httpRequestService;     // Initialise http request service
    private $apiServicesService;     // Initialise api services service

    /**
     * ServiceController constructor.
     * Initialises services used in this controller
     *
     * @param ProviderService $providerService
     * @param HttpRequestService $httpRequestService
     * @param SerializerService $serializerService
     * @param ApiServicesService $apiServicesService
     */
    public function __construct(ProviderService $providerService, HttpRequestService $httpRequestService,
                                SerializerService $serializerService, ApiServicesService $apiServicesService)
    {
        $this->providerService = $providerService;   //Initialise provider service
        $this->serializerService = $serializerService; // Initialise serializer service
        $this->httpRequestService = $httpRequestService;   //Initialise http request service
        $this->apiServicesService = $apiServicesService;   //Initialise api services service
    }

    /**
     * Get service list function
     * returns a list of api services based on the request query parameters
     *
     * @Route("/api/services", name="api_get_services", methods={"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getServices(Request $request)
    {
        $getServices = $this->apiServicesService->findByParams(
            $request->get('sort', "service_name"),
            $request->get('order', "asc"),
            (int)$request->get('count', null)
        );
        return $this->jsonResponseSuccess("success",
            $this->serializerService->entityArrayToArray($getServices, ["list"]));
    }

    /**
     * Get a single api service
     * Returns a single api service based on the id passed in the request url
     *
     * @Route("/api/service/{id}", name="api_get_service", methods={"GET"})
     * @param Service $service
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getService(Service $service)
    {
        return $this->jsonResponseSuccess("success",
            $this->serializerService->entityToArray($service, ["single"]));
    }

    /**
     * Create an api service based on request POST data
     * Returns json success message and api service data on successful creation
     * Returns error response and message on fail
     *
     * @param Request $request
     * @Route("/api/service/create", name="api_create_service", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createService(Request $request)
    {
        $create = $this->apiServicesService->createService(
            $this->httpRequestService->getRequestData($request, true));

        if (!$create) {
            return $this->jsonResponseFail("Error inserting service");
        }
        return $this->jsonResponseSuccess("Service inserted",
            $this->serializerService->entityToArray($create, ['main']));
    }

    /**
     * Updates an api service based on request POST data
     * Returns json success message and api service data on successful update
     * Returns error response and message on fail
     *
     * @param Request $request
     * @Route("/api/service/update", name="api_update_service", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateService(Request $request)
    {
        $update = $this->apiServicesService->updateService(
            $this->httpRequestService->getRequestData($request, true));

        if (!$update) {
            return $this->jsonResponseFail("Error updating service");
        }
        return $this->jsonResponseSuccess("Service updated",
            $this->serializerService->entityToArray($update, ['main']));
    }

    /**
     * Delete an api service based on request POST data
     * Returns json success message and api service data on successful delete
     * Returns error response and message on fail
     *
     * @param Request $request
     * @Route("/api/service/delete", name="api_delete_service", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteService(Request $request)
    {
        $requestData = $this->httpRequestService->getRequestData($request, true);
        $delete = $this->apiServicesService->deleteService($requestData['item_id']);
        if (!$delete) {
            return $this->jsonResponseFail("Error deleting service", $this->serializerService->entityToArray($delete, ['main']));
        }
        return $this->jsonResponseSuccess("Service deleted.", $this->serializerService->entityToArray($delete, ['main']));
    }
}
