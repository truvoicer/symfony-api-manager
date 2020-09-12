<?php
namespace App\Service;


use App\Entity\Category;
use App\Entity\Service;
use App\Entity\Provider;
use App\Entity\ServiceRequest;
use App\Entity\ServiceRequestConfig;
use App\Entity\ServiceRequestParameter;
use App\Entity\ServiceResponseKey;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RequestConfigService
{
    private $entityManager;
    private $httpRequestService;
    private $serviceRepository;
    private $providerService;
    private $serviceRequestRepository;
    private $requestParametersRepo;
    private $requestConfigRepo;
    private $responseKeysRepo;

    public function __construct(EntityManagerInterface $entityManager, HttpRequestService $httpRequestService,
                                ProviderService $providerService)
    {
        $this->entityManager = $entityManager;
        $this->providerService = $providerService;
        $this->httpRequestService = $httpRequestService;
        $this->serviceRepository = $this->entityManager->getRepository(Service::class);
        $this->serviceRequestRepository = $this->entityManager->getRepository(ServiceRequest::class);
        $this->requestParametersRepo = $this->entityManager->getRepository(ServiceRequestParameter::class);
        $this->requestConfigRepo = $this->entityManager->getRepository(ServiceRequestConfig::class);
        $this->responseKeysRepo = $this->entityManager->getRepository(ServiceResponseKey::class);
    }

    public function getResponseKeysRequestsConfigList(int $serviceRequestId, int $providerId, string $sort, string $order, int $count) {
        $serviceRequest = $this->serviceRequestRepository->findOneBy(["id" => $serviceRequestId]);
        $provider = $this->providerService->getProviderById($providerId);
        $responseKeys = $this->responseKeysRepo->findBy(["service" => $serviceRequest->getService()]);
        $list = array_map(function ($item) use($provider, $serviceRequest) {
            $listObject = new \stdClass();
            $listObject->key_name = $item->getId();
            $listObject->key_name = $item->getKeyName();
            $listObject->key_value = $item->getKeyValue();
            $listObject->item_value = "";
            $listObject->item_array_value = "";
            $getConfig = $this->requestConfigRepo->getRequestConfigByName($provider, $serviceRequest, $item->getKeyName());
            if ($getConfig !== null) {
                $listObject->item_value = $getConfig->getItemValue();
                $listObject->item_array_value = $getConfig->getItemArrayValue();
            }
            return $listObject;
        }, $responseKeys);
    }

    public function findByParams(int $serviceRequestId, string $sort, string $order, int $count) {
        $serviceRequest = $this->serviceRequestRepository->findOneBy(["id" => $serviceRequestId]);
        if ($serviceRequest !== null) {
            return $this->requestConfigRepo->findByParams($serviceRequest, $sort, $order, $count);
        }
    }

    private function getRequestConfigObject(ServiceRequestConfig $requestConfig,
                                                       ServiceRequest $serviceRequest, array $data)
    {
        $requestConfig->setItemName($data['item_name']);
        $requestConfig->setValueType($data['selected_value_type']);
        $requestConfig->setItemValue($data['item_value']);
        $requestConfig->setItemArrayValue($data['item_array_value']);
        $requestConfig->setServiceRequest($serviceRequest);
        return $requestConfig;
    }

    public function createRequestConfig(array $data)
    {
        $serviceRequest = $this->serviceRequestRepository->findOneBy(["id" => $data["service_request_id"]]);
        $requestConfig = $this->getRequestConfigObject(new ServiceRequestConfig(), $serviceRequest, $data);
        if ($this->httpRequestService->validateData($requestConfig)) {
            return $this->requestConfigRepo->save($requestConfig);
        }
        return false;
    }

    public function updateRequestConfig(array $data)
    {
        $getRequestConfig = $this->requestConfigRepo->findOneBy(["id" => $data["id"]]);
        $serviceRequest = $this->serviceRequestRepository->findOneBy(["id" => $data["service_request_id"]]);
        $requestConfig = $this->getRequestConfigObject($getRequestConfig, $serviceRequest, $data);
        if ($this->httpRequestService->validateData($requestConfig)) {
            return $this->requestConfigRepo->save($requestConfig);
        }
        return false;
    }

    public function deleteRequestConfig(int $id) {
        $requestConfig = $this->requestConfigRepo->findOneBy(["id" => $id]);
        if ($requestConfig === null) {
            throw new BadRequestHttpException(sprintf("Service request config item id: %s not found in database.", $id));
        }
        return $this->requestConfigRepo->delete($requestConfig);
    }


}