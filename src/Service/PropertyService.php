<?php
namespace App\Service;
use App\Entity\Property;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PropertyService {

    protected $em;
    protected $propertyRepository;
    private $httpRequestService;

    public function __construct(EntityManagerInterface $entityManager, HttpRequestService $httpRequestService)
    {
        $this->em = $entityManager;
        $this->httpRequestService = $httpRequestService;
        $this->propertyRepository = $this->em->getRepository(Property::class);
    }

    private function setPropertyObject(Property $property, array $propertyData) {
        $property->setPropertyName($propertyData['property_name']);
        $property->setPropertyLabel($propertyData['property_label']);
        return $property;
    }

    public function getPropertyByName(string $propertyName) {
        $property = $this->propertyRepository->findOneBy(["property_name" => $propertyName]);
        if ($property === null) {
            throw new BadRequestHttpException(sprintf("Property name:%s not found in database.",
                $propertyName
            ));
        }
        return $property;
    }

    public function getPropertyById(int $propertyId) {
        $property = $this->propertyRepository->findOneBy(["id" => $propertyId]);
        if ($property === null) {
            throw new BadRequestHttpException(sprintf("Property id:%s not found in database.",
                $propertyId
            ));
        }
        return $property;
    }

    public function createProperty(array $propertyData) {
        $property = $this->setPropertyObject(new Property(), $propertyData);
        if ($this->httpRequestService->validateData($property)) {
            return $this->propertyRepository->createProperty($property);
        }
        return false;
    }

    public function updateProperty(array $propertyData) {
        if (!array_key_exists("id", $propertyData)){
            throw new BadRequestHttpException("Property id doesnt exist in request.");
        }
        $getProperty = $this->propertyRepository->findOneBy(['id' => $propertyData["id"]]);

        $property = $this->setPropertyObject($getProperty, $propertyData);
        if($this->httpRequestService->validateData($property)) {
            return $this->propertyRepository->updateProperty($property);
        }
        return false;
    }

    public function deleteProperty(int $propertyId) {
        $property = $this->getPropertyById($propertyId);
        if ($property === null) {
            throw new BadRequestHttpException(sprintf("Property id: %s not found in database.", $propertyId));
        }
        return $this->propertyRepository->deleteProperty($property);
    }
}