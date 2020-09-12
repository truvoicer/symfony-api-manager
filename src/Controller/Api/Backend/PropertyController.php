<?php

namespace App\Controller\Api\Backend;

use App\Controller\Api\BaseController;
use App\Entity\Property;
use App\Repository\PropertyRepository;
use App\Service\HttpRequestService;
use App\Service\PropertyService;
use App\Service\SerializerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Contains api endpoint functions for properties related tasks
 *
 * Require ROLE_ADMIN for *every* controller method in this class.
 *
 * @IsGranted("ROLE_ADMIN")
 */
class PropertyController extends BaseController
{
    private $propertyRepository;
    private $propertyService;
    private $httpRequestService;
    private $serializerService;

    /**
     * PropertyController constructor.
     * Initialises services to be used in this class
     *
     * @param PropertyRepository $propertyRepository
     * @param HttpRequestService $httpRequestService
     * @param PropertyService $propertyService
     * @param SerializerService $serializerService
     */
    public function __construct(PropertyRepository $propertyRepository, HttpRequestService $httpRequestService,
                                PropertyService $propertyService, SerializerService $serializerService)
    {
        $this->propertyRepository = $propertyRepository;
        $this->propertyService = $propertyService;
        $this->httpRequestService = $httpRequestService;
        $this->serializerService = $serializerService;
    }

    /**
     * Gets a list of properties from the database based on
     * the get request query parameters
     *
     * @Route("/api/properties", name="api_get_properties", methods={"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getPropertyList(Request $request)
    {
        $getProperties = $this->propertyRepository->findByParams(
            $request->get('sort', "property_name"),
            $request->get('order', "asc"),
            (int) $request->get('count', null)
        );
        return $this->jsonResponseSuccess("success",
            $this->serializerService->entityArrayToArray($getProperties));
    }

    /**
     * Gets a single property from the database based on the get request query parameters
     *
     * @Route("/api/property/{id}", name="api_get_property", methods={"GET"})
     * @param Property $property
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getProperty(Property $property)
    {
        return $this->jsonResponseSuccess("success",
            $this->serializerService->entityToArray($property, ["main"]));
    }

    /**
     * Updates a property in the database based on the post request data
     *
     * @param Request $request
     * @Route("/api/property/update", name="api_update_property", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateProperty(Request $request)
    {
        $requestData = $this->httpRequestService->getRequestData($request, true);

        $updateProperty = $this->propertyService->updateProperty($requestData);

        if(!$updateProperty) {
            return $this->jsonResponseFail("Error updating property");
        }
        return $this->jsonResponseSuccess("Property updated", $this->serializerService->entityToArray($updateProperty, ['main']));
    }

    /**
     * Creates a property in the database based on the post request data
     *
     * @param Request $request
     * @Route("/api/property/create", name="api_create_property", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createProperty(Request $request)
    {
        $requestData = $this->httpRequestService->getRequestData($request, true);

        $createProperty = $this->propertyService->createProperty($requestData);
        if(!$createProperty) {
            return $this->jsonResponseFail("Error creating property");
        }
        return $this->jsonResponseSuccess("Property created", $this->serializerService->entityToArray($createProperty, ['main']));
    }


    /**
     * Deletes a property in the database based on the post request data
     *
     * @Route("/api/property/delete", name="api_delete_property", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteProperty(Request $request) {
        $requestData = $this->httpRequestService->getRequestData($request, true);
        $delete = $this->propertyService->deleteProperty($requestData['item_id']);
        if (!$delete) {
            return $this->jsonResponseFail("Error deleting property", $this->serializerService->entityToArray($delete, ['main']));
        }
        return $this->jsonResponseSuccess("Property deleted.", $this->serializerService->entityToArray($delete, ['main']));
    }
}
