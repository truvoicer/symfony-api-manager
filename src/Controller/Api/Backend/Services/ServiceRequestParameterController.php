<?php
namespace App\Controller\Api\Backend\Services;

use App\Controller\Api\BaseController;
use App\Entity\ServiceRequestParameter;
use App\Service\ApiServicesService;
use App\Service\HttpRequestService;
use App\Service\ProviderService;
use App\Service\RequestParametersService;
use App\Service\SerializerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Contains Api endpoint functions for api service request parameter related operations
 *
 * Require ROLE_ADMIN for *every* controller method in this class.
 *
 * @IsGranted("ROLE_ADMIN")
 */
class ServiceRequestParameterController extends BaseController
{
    private $providerService;
    private $serializerService;
    private $httpRequestService;
    private $apiServicesService;
    private $requestParametersService;

    /**
     * ServiceRequestParameterController constructor.
     * Initialises services used in this controller
     *
     * @param ProviderService $providerService
     * @param HttpRequestService $httpRequestService
     * @param SerializerService $serializerService
     * @param ApiServicesService $apiServicesService
     * @param RequestParametersService $requestParametersService
     */
    public function __construct(ProviderService $providerService, HttpRequestService $httpRequestService,
                                SerializerService $serializerService, ApiServicesService $apiServicesService,
                                RequestParametersService $requestParametersService)
    {
        $this->providerService = $providerService;
        $this->serializerService = $serializerService;
        $this->httpRequestService = $httpRequestService;
        $this->apiServicesService = $apiServicesService;
        $this->requestParametersService = $requestParametersService;
    }

    /**
     * Get a list of service request parameters.
     * Returns a list of service request parameters based on the request query parameters
     *
     * @Route("/api/service/request/parameter/list", name="api_get_service_request_parameter_list", methods={"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getServiceRequestParameterList(Request $request)
    {
        $responseKeys = $this->requestParametersService->findByParams(
            $request->get('service_request_id'),
            $request->get('sort', "parameter_name"),
            $request->get('order', "asc"),
            (int) $request->get('count', null)
        );
        return $this->jsonResponseSuccess("success",
            $this->serializerService->entityArrayToArray($responseKeys, ["list"]));
    }

    /**
     * Get a single service request parameter
     * Returns a single service request parameter based on the id passed in the request url
     *
     * @Route("/api/service/request/parameter/{id}", name="api_get_service_request_parameter", methods={"GET"})
     * @param ServiceRequestParameter $serviceRequestParameter
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getServiceRequestParameter(ServiceRequestParameter $serviceRequestParameter)
    {
        return $this->jsonResponseSuccess("success",
            $this->serializerService->entityToArray($serviceRequestParameter, ["single"]));
    }

    /**
     * Create an api service request parameter based on request POST data
     * Returns json success message and api service request data on successful creation
     * Returns error response and message on fail
     *
     * @param Request $request
     * @Route("/api/service/request/parameter/create", name="api_create_service_request_parameter", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createServiceRequestParameter(Request $request) {
        $create = $this->requestParametersService->createRequestParameter(
            $this->httpRequestService->getRequestData($request, true));

        if(!$create) {
            return $this->jsonResponseFail("Error inserting parameter");
        }
        return $this->jsonResponseSuccess("Parameter inserted",
            $this->serializerService->entityToArray($create, ['main']));
    }

    /**
     * Update an api service request parameter based on request POST data
     * Returns json success message and api service request parameter data on successful update
     * Returns error response and message on fail
     *
     * @param Request $request
     * @Route("/api/service/request/parameter/update", name="api_update_service_request_parameter", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateServiceRequestParameter(Request $request)
    {
        $update = $this->requestParametersService->updateRequestParameter(
            $this->httpRequestService->getRequestData($request, true));

        if(!$update) {
            return $this->jsonResponseFail("Error updating parameter");
        }
        return $this->jsonResponseSuccess("Parameter updated",
            $this->serializerService->entityToArray($update, ['main']));
    }

    /**
     * Delete an api service request parameter based on request POST data
     * Returns json success message and api service request parameter data on successful delete
     * Returns error response and message on fail
     *
     * @param Request $request
     * @Route("/api/service/request/parameter/delete", name="api_delete_service_request_parameter", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteServiceRequestParameter(Request $request)
    {
        $requestData = $this->httpRequestService->getRequestData($request, true);
        $delete = $this->requestParametersService->deleteRequestParameter($requestData['item_id']);
        if (!$delete) {
            return $this->jsonResponseFail("Error deleting parameter", $this->serializerService->entityToArray($delete, ['main']));
        }
        return $this->jsonResponseSuccess("Parameter deleted.", $this->serializerService->entityToArray($delete, ['main']));
    }
}
