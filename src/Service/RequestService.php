<?php
namespace App\Service;


use App\Entity\Category;
use App\Entity\Service;
use App\Entity\Provider;
use App\Entity\ServiceRequest;
use App\Entity\ServiceRequestConfig;
use App\Entity\ServiceRequestParameter;
use App\Entity\ServiceRequestResponseKey;
use App\Entity\ServiceResponseKey;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RequestService
{
    private $entityManager;
    private $httpRequestService;
    private $providerService;
    private $serviceRepository;
    private $serviceRequestRepository;
    private $requestParametersRepo;
    private $requestConfigRepo;
    private $responseKeysService;
    private $requestConfigService;
    private $requestParametersService;
    private $apiService;

    public function __construct(EntityManagerInterface $entityManager, HttpRequestService $httpRequestService,
                                ProviderService $providerService, ResponseKeysService $responseKeysService,
                                RequestConfigService $requestConfigService, RequestParametersService $requestParametersService,
                                ApiServicesService $apiService)
    {
        $this->entityManager = $entityManager;
        $this->httpRequestService = $httpRequestService;
        $this->providerService = $providerService;
        $this->apiService = $apiService;
        $this->responseKeysService = $responseKeysService;
        $this->requestConfigService = $requestConfigService;
        $this->requestParametersService = $requestParametersService;
        $this->serviceRepository = $this->entityManager->getRepository(Service::class);
        $this->serviceRequestRepository = $this->entityManager->getRepository(ServiceRequest::class);
        $this->requestParametersRepo = $this->entityManager->getRepository(ServiceRequestParameter::class);
        $this->responseKeysRepo = $this->entityManager->getRepository(ServiceResponseKey::class);
        $this->requestConfigRepo = $this->entityManager->getRepository(ServiceRequestConfig::class);
    }

    public function findByQuery(string $query)
    {
        return $this->serviceRequestRepository->findByQuery($query);
    }

    public function findByParams(string $sort, string $order, int $count) {
        return $this->serviceRequestRepository->findByParams($sort, $order, $count);
    }

    public function getRequestByName(Provider $provider, string $serviceRequestName = null) {
        return $this->serviceRequestRepository->getRequestByName($provider, $serviceRequestName);
    }

    public function getRequestByRequestName(Provider $provider, string $serviceName = null) {
        return $this->serviceRequestRepository->getRequestByName($provider, $serviceName);
    }

    public function getServiceByRequestName(Provider $provider, string $serviceName = null) {
        return $this->serviceRepository->getServiceByRequestName($provider, $serviceName);
    }

    public function getServiceRequestById($id) {
        $getServiceRequest = $this->serviceRequestRepository->findOneBy(["id" => $id]);
        if ($getServiceRequest === null) {
            throw new BadRequestHttpException("Service request does not exist in database.");
        }
        return $getServiceRequest;
    }

    public function getServiceRequestByProvider(int $providerId, string $sort, string $order, int $count) {
        return $this->serviceRequestRepository->getServiceRequestByProvider(
            $this->providerService->getProviderById($providerId),
            $sort, $order, $count);
    }

    public function getProviderServiceRequest(int $serviceId, int $providerId) {
        return $this->serviceRequestRepository->findOneBy([
            "service_id" => $serviceId,
            "provider_id" => $providerId
        ]);
    }

    public function getRequestConfigByName(Provider $provider, ServiceRequest $serviceRequest, string $configItemName)
    {
        return $this->requestConfigRepo->getRequestConfigByName($provider, $serviceRequest, $configItemName);
    }

    public function getRequestParametersByRequestName(Provider $provider, string $serviceRequestName = null)
    {
        return $this->requestParametersRepo->getRequestParametersByRequestName($provider, $serviceRequestName);
    }

    public function getResponseKeysByRequest(Provider $provider, ServiceRequest $serviceRequest)
    {
        return $this->responseKeysRepo->getResponseKeys($provider, $serviceRequest);
    }

    public function getSingleResponseKeyByRequest(Provider $provider, ServiceRequest $serviceRequest)
    {
        return $this->responseKeysRepo->getResponseKey($provider, $serviceRequest);
    }


    private function getServiceRequestObject(ServiceRequest $serviceRequest, Provider $provider,
                                             Service $service, array $data)
    {
        $serviceRequest->setProvider($provider);
        $serviceRequest->setService($service);
        $serviceRequest->setServiceRequestLabel($data['service_request_label']);
        $serviceRequest->setServiceRequestName($data['service_request_name']);
        return $serviceRequest;
    }

    public function createServiceRequest(array $data)
    {
        $providerRepo = $this->entityManager->getRepository(Provider::class);
        $provider = $providerRepo->findOneBy(["id" => $data["provider_id"]]);
        $checkRequestName = $this->serviceRequestRepository->findOneBy([
            "service_request_name" => $data['service_request_name'],
            "provider" => $provider
        ]);
        if ($checkRequestName !== null) {
            throw new BadRequestHttpException(sprintf("Service request (%s) already exists.", $data['service_request_name']));
        }
        $service = $this->serviceRepository->findOneBy(["id" => $data["services"]['id']]);
        $serviceRequest = $this->getServiceRequestObject(new ServiceRequest(), $provider, $service, $data);
        if ($this->httpRequestService->validateData($service)) {
            return $this->serviceRequestRepository->save($serviceRequest);
        }
        return false;
    }

    public function updateServiceRequest(array $data)
    {
        $providerRepo = $this->entityManager->getRepository(Provider::class);
        $getServiceRequest = $this->serviceRequestRepository->findOneBy(["id" => $data["id"]]);
        $provider = $providerRepo->findOneBy(["id" => $data["provider_id"]]);
        if (isset($data["service_id"]))  {
            $service = $this->serviceRepository->findOneBy(["id" => $data["service_id"]]);
        } elseif (isset($data["services"])) {
            $service = $this->serviceRepository->findOneBy(["id" => $data["services"]["id"]]);
        }
        $serviceRequest = $this->getServiceRequestObject($getServiceRequest, $provider, $service, $data);
        if ($this->httpRequestService->validateData($service)) {
            return $this->serviceRequestRepository->save($serviceRequest);
        }
        return false;
    }

    public function duplicateServiceRequest(array $data)
    {
        return $this->serviceRequestRepository->duplicateServiceRequest(
            $this->getServiceRequestById($data["id"]), $data);
    }

    public function deleteServiceRequest(int $id) {
        $serviceRequest = $this->serviceRequestRepository->findOneBy(["id" => $id]);
        if ($serviceRequest === null) {
            throw new BadRequestHttpException(sprintf("Service request id: %s not found in database.", $id));
        }
        return $this->serviceRequestRepository->delete($serviceRequest);
    }



}