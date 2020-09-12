<?php
namespace App\Service;
use App\Entity\ApiRequest;
use App\Entity\Category;
use App\Entity\Property;
use App\Entity\Provider;
use App\Entity\ProviderCategory;
use App\Entity\ProviderProperty;
use App\Entity\Service;
use App\Repository\ProviderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProviderService {

    protected $em;
    protected $providerRepository;
    protected $providerPropertyRepository;
    protected $propertyService;
    protected $serviceRepository;
    private $httpRequestService;
    private $categoryService;

    public function __construct(EntityManagerInterface $entityManager, HttpRequestService $httpRequestService,
                                PropertyService $propertyService, CategoryService $categoryService)
    {
        $this->em = $entityManager;
        $this->httpRequestService = $httpRequestService;
        $this->providerRepository = $this->em->getRepository(Provider::class);
        $this->providerPropertyRepository = $this->em->getRepository(ProviderProperty::class);
        $this->propertyService = $propertyService;
        $this->categoryService = $categoryService;
        $this->serviceRepository = $this->em->getRepository(Service::class);
    }

    public function getProviderByName(string $providerName = null) {
        return $this->providerRepository->findByName($providerName);
    }
    public function getProviderById(int $providerId) {
        $provider = $this->providerRepository->findOneBy(["id" => $providerId]);
        if ($provider === null) {
            throw new BadRequestHttpException(sprintf("Provider id:%s not found in database.",
                $providerId
            ));
        }
        return $provider;
    }

    public function findByQuery(string $query) {
        return $this->providerRepository->findByQuery($query);
    }

    public function getProviderPropertyRelationById(int $id) {
        $providerProperty = $this->providerPropertyRepository->findOneBy(["id" => $id]);
        if ($providerProperty === null) {
            throw new BadRequestHttpException(sprintf("ProviderProperty relation id:%s not found in database.",
                $id
            ));
        }
        return $providerProperty;
    }

    public function getProviderList(string $sort = "provider_name", string $order = "asc", int $count = null)
    {
        return $this->providerRepository->findByParams(
            $sort,
            $order,
            $count
        );
    }

    public function getProviderPropertyRelation(int $id) {
        return $this->getProviderPropertyRelationById($id);
    }

    public function getProviderPropertyObjectByName(Provider $provider, string $propertyName) {
        $property = $this->propertyService->getPropertyByName($propertyName);
        return $this->getProviderProperty($provider, $property);
    }

    public function getProviderPropertyObjectById(Provider $provider, int $property_id) {
        $property = $this->propertyService->getPropertyById($property_id);
        return $this->getProviderProperty($provider, $property);
    }

    public function getProviderProperty(Provider $provider, Property $property) {
        $getProviderProperty = $this->providerRepository->getProviderProperty($provider, $property);
        $object = new \stdClass();
        $object->property_id = $property->getId();
        $object->property_name = $property->getPropertyName();
        $object->property_label = $property->getPropertyLabel();
        $object->property_value = "";
        if ($getProviderProperty !== null) {
            $object->property_value = $getProviderProperty->getPropertyValue();
        }
        return $object;
    }

    public function getProviderProperties(int $providerId, string $sort = "property_name", string $order = "asc", int $count = null) {
        $provider = $this->getProviderById($providerId);

        $propertyRepo = $this->em->getRepository(Property::class);
        return array_map(function ($property) use($provider) {
            $repo = $this->em->getRepository(ProviderProperty::class);
            $providerProperty = $repo->findOneBy(["provider" => $provider, "property" => $property]);
            $providerPropertyObject = new \stdClass();
            $providerPropertyObject->id = $property->getId();
            $providerPropertyObject->property_name = $property->getPropertyName();
            $providerPropertyObject->property_value = ($providerProperty !== null)? $providerProperty->getPropertyValue() : "";
            return $providerPropertyObject;
        }, $propertyRepo->findByParams($sort, $order, $count));
    }
    public function getProviderPropertyValue(Provider $provider, string $propertyName) {
        return $this->getProviderPropertyObjectByName($provider,
            $propertyName)->property_value;
    }

    public function getServiceParameterByName(Provider $provider, string $serviceName = null, string $parameterName = null)
    {
        return $this->providerRepository->getServiceParameterByName($provider, $serviceName, $parameterName);
    }

    public function getProviderServiceParametersByName(Provider $provider, string $serviceName = null, array $reservedParams = [])
    {
        return $this->providerRepository->getProviderServiceParameters($provider, $serviceName, $reservedParams);
    }

    public function getProviderServiceParametersById(int $providerId, int $serviceId)
    {
        $service = $this->serviceRepository->findOneBy(["id" => $serviceId]);
        $provider = $this->providerRepository->findOneBy(["id" => $providerId]);
        return $this->providerRepository->getProviderServiceParameters($provider, $service);
    }

    private function setProviderObject(Provider $provider, array $providerData) {
        try {
            $provider->setProviderName($providerData['provider_name']);
            $provider->setProviderLabel($providerData['provider_label']);
            $provider->setProviderApiBaseUrl($providerData['provider_api_base_url']);
            $provider->setProviderAccessKey($providerData['provider_access_key']);
            $provider->setProviderSecretKey($providerData['provider_secret_key']);
            $provider->setProviderUserId($providerData['provider_user_id']);
            foreach ($provider->getCategory() as $category) {
                $provider->removeCategory($category);
            }
            if (isset($providerData['category']) && count($providerData['category']) > 0) {
                foreach($providerData['category'] as $category) {
                    $category = $this->categoryService->getCategoryById($category['id']);
                    $provider->addCategory($category);
                }
            }
            return $provider;
        }
        catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }

    public function createProvider(array $providerData) {
        $checkProvider = $this->providerRepository->findOneBy(["provider_name" => $providerData['provider_name']]);
        if ($checkProvider !== null) {
            throw new BadRequestHttpException(sprintf("Provider (%s) already exists.", $providerData['provider_name']));
        }
        $provider = $this->setProviderObject(new Provider(), $providerData);
        if ($this->httpRequestService->validateData($provider)) {
            return $this->providerRepository->createProvider($provider);
        }
        return false;
    }


    public function updateProvider(array $providerData) {
        if (!array_key_exists("id", $providerData)){
            throw new BadRequestHttpException("Provider id doesnt exist in request.");
        }
        $getProvider = $this->providerRepository->findOneBy(['id' => $providerData["id"]]);
        $provider = $this->setProviderObject($getProvider, $providerData);
        if($this->httpRequestService->validateData($provider)) {
            return $this->providerRepository->updateProvider($provider);
        }
        return false;
    }

    public function deleteProviderCategories(Provider $provider) {
        return  $this->providerRepository->deleteProviderCategories($provider);
    }

    public function createProviderProperty(array $providerPropData) {
        $provider = $this->getProviderById($providerPropData['provider_id']);
        $property = $this->propertyService->getPropertyById($providerPropData['property_id']);
        return  $this->providerRepository->createProviderProperty($provider, $property, $providerPropData['property_value']);
    }
    public function updateProviderProperty (array $data) {
        $providerPropertyRepo = $this->em->getRepository(ProviderProperty::class);

        $provider = $this->getProviderById($data['provider_id']);

        $property = $this->propertyService->getPropertyById($data['property_id']);

        $providerProperty = $providerPropertyRepo->findOneBy(["provider" => $provider, "property" => $property]);
        if ($providerProperty !== null) {
            $providerProperty->setPropertyValue($data['property_value']);
            return $providerPropertyRepo->saveProviderProperty($providerProperty);
        }
        return $providerPropertyRepo->createProviderProperty($provider, $property, $data['property_value']);
    }

    public function deleteProvider(int $providerId) {
        return $this->providerRepository->deleteProvider($this->getProviderById($providerId));
    }

    public function deleteProviderProperty(int $providerId, int $propertyId) {
        $provider = $this->getProviderById($providerId);
        $property = $this->propertyService->getPropertyById($propertyId);
        $providerProperty = $this->providerPropertyRepository->findOneBy([
            "provider" => $provider,
            "property" => $property]);

        if ($providerProperty !== null) {
            return $this->providerPropertyRepository->deleteProviderProperty($providerProperty);
        }
        throw new BadRequestHttpException(
            sprintf("Error deleting property value. (Provider id:%s, Property id:%s)",
            $providerId, $propertyId
        ));
    }
}