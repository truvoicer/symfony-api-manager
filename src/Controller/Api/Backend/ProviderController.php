<?php

namespace App\Controller\Api\Backend;

use App\Controller\Api\BaseController;
use App\Entity\Provider;
use App\Repository\ProviderRepository;
use App\Service\HttpRequestService;
use App\Service\ProviderService;
use App\Service\SerializerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Contains api endpoint functions for provider related tasks
 *
 * Require ROLE_ADMIN for *every* controller method in this class.
 *
 * @IsGranted("ROLE_ADMIN")
 */
class ProviderController extends BaseController
{
    private $providerRepo;
    private $providerService;
    private $serializerService;
    private $httpRequestService;

    /**
     * ProviderController constructor.
     * Initialises services to be used in this class
     *
     * @param ProviderRepository $providerRepository
     * @param ProviderService $providerService
     * @param SerializerService $serializerService
     * @param HttpRequestService $httpRequestService
     */
    public function __construct(ProviderRepository $providerRepository, ProviderService $providerService,
                                SerializerService $serializerService,
                                HttpRequestService $httpRequestService)
    {
        $this->providerRepo = $providerRepository;
        $this->providerService = $providerService;
        $this->serializerService = $serializerService;
        $this->httpRequestService = $httpRequestService;
    }

    /**
     * Gets a list of providers from the database based on the get request query parameters
     *
     * @Route("/api/providers", name="api_get_providers", methods={"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getProviderList(Request $request)
    {
        $getProviders = $this->providerService->getProviderList(
            $request->get('sort', "provider_name"),
            $request->get('order', "asc"),
            (int) $request->get('count', null)
        );
        return $this->jsonResponseSuccess("success", $this->serializerService->entityArrayToArray($getProviders, ["list"]));
    }

    /**
     * Gets a single provider from the database based on the id in the get request url
     *
     * @Route("/api/provider/{id}", name="api_get_provider", methods={"GET"})
     * @param Provider $provider
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getProvider(Provider $provider)
    {
        return $this->jsonResponseSuccess("success",
            $this->serializerService->entityToArray($provider, ["single"]));
    }

    /**
     * Gets a single related provider property based on the
     * provider property id in the url
     *
     * @Route("/api/provider/property/relation/{id}", name="api_get_provider_property_relation", methods={"GET"})
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getProviderPropertyRelation(int $id)
    {
        $getProviderProperty = $this->providerService->getProviderPropertyRelation($id);
        return $this->jsonResponseSuccess("success",
            $this->serializerService->entityToArray($getProviderProperty));
    }

    /**
     * Gets a single related provider property based on
     * the provider id and property id in the url
     *
     * @Route("/api/provider/{id}/property/{property_id}", name="api_get_provider_property", methods={"GET"})
     * @param Provider $provider
     * @param int $property_id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getProviderProperty(Provider $provider, int $property_id)
    {
        $getProviderProperty = $this->providerService->getProviderPropertyObjectById($provider, $property_id);
        return $this->jsonResponseSuccess("success", $getProviderProperty);
    }

    /**
     * Gets a list of related provider property objects based on the get request
     * query parameters
     *
     * @Route("/api/provider/{id}/properties", name="api_get_provider_property_list", methods={"GET"})
     * @param Provider $provider
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getProviderPropertyList(Provider $provider, Request $request)
    {
        $getProviderProps = $this->providerService->getProviderProperties(
            $provider->getId(),
            $request->get('sort', "property_name"),
            $request->get('order', "asc"),
            (int) $request->get('count', null)
        );
        return $this->jsonResponseSuccess("success", $getProviderProps);
    }

    /**
     * Creates a provider in the database based on the post request data
     *
     * @param Request $request
     * @Route("/api/provider/create", name="api_create_provider", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createProvider(Request $request)
    {
        $createProvider = $this->providerService->createProvider(
            $this->httpRequestService->getRequestData($request, true));

        if(!$createProvider) {
            return $this->jsonResponseFail("Error inserting provider");
        }
        return $this->jsonResponseSuccess("Provider added",
            $this->serializerService->entityToArray($createProvider, ['main']));
    }


    /**
     * Updates a provider in the database based on the post request data
     *
     * @param Request $request
     * @Route("/api/provider/update", name="api_update_provider", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateProvider(Request $request)
    {
        $updateProvider = $this->providerService->updateProvider(
            $this->httpRequestService->getRequestData($request, true));

        if(!$updateProvider) {
            return $this->jsonResponseFail("Error updating provider");
        }
        return $this->jsonResponseSuccess("Provider updated",
            $this->serializerService->entityToArray($updateProvider, ['main']));
    }


    /**
     * Deletes a provider in the database based on the post request data
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/provider/delete", name="api_delete_provider", methods={"POST"})
     */
    public function deleteProvider(Request $request) {
        $requestData = $this->httpRequestService->getRequestData($request, true);
        $delete = $this->providerService->deleteProvider($requestData['item_id']);
        if (!$delete) {
            return $this->jsonResponseFail("Error deleting provider", $this->serializerService->entityToArray($delete, ['main']));
        }
        return $this->jsonResponseSuccess("Provider deleted.", $this->serializerService->entityToArray($delete, ['main']));
    }

    /**
     * Creates a related provider property in the database based on the post request data
     * Required data request data fields:
     * - provider_id
     * - property_id
     *
     * @param Request $request
     * @Route("/api/provider/property/create", name="api_create_provider_property", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createProviderProperty(Request $request) {
        $requestData = $this->httpRequestService->getRequestData($request);
        $create = $this->providerService->createProviderProperty($requestData->data);
        if(!$create) {
            return $this->jsonResponseFail("Error adding provider property.");
        }
        return $this->jsonResponseSuccess("Successfully added provider property.",
            $this->serializerService->entityToArray($create));
    }

    /**
     * Updates a related provider property in the database based on the post request data
     * Required data request data fields:
     * - provider_id
     * - property_id
     *
     * @param Request $request
     * @Route("/api/provider/property/update", name="api_update_provider_property_relation", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateProviderProperty(Request $request)
    {
        $update = $this->providerService->updateProviderProperty(
            $this->httpRequestService->getRequestData($request, true));

        if(!$update) {
            return $this->jsonResponseFail("Error updating provider property");
        }
        return $this->jsonResponseSuccess("Provider property updated",
            $this->serializerService->entityToArray($update));
    }

    /**
     * Deletes a related provider property in the database based on the post request data
     * Required data request data fields:
     * - item_id (property_id)
     * - extra->provider_id
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/provider/property/delete", name="api_delete_provider_property", methods={"POST"})
     */
    public function deleteProviderProperty(Request $request) {
        $requestData = $this->httpRequestService->getRequestData($request, true);
        $delete = $this->providerService->deleteProviderProperty($requestData['extra']['provider_id'], $requestData['item_id']);
        if (!$delete) {
            return $this->jsonResponseFail("Error deleting provider property value", $this->serializerService->entityToArray($delete, ['main']));
        }
        return $this->jsonResponseSuccess("Provider property value deleted.", $this->serializerService->entityToArray($delete, ['main']));
    }
}
