<?php
namespace App\Service;

use App\Entity\Category;
use App\Entity\Service;
use App\Entity\Provider;
use App\Entity\ServiceRequest;
use App\Entity\ServiceRequestParameter;
use App\Entity\ServiceRequestResponseKey;
use App\Entity\ServiceResponseKey;
use App\Repository\ServiceRequestResponseKeyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ResponseKeysService
{
    private $entityManager;
    private $httpRequestService;
    private $serviceRepository;
    private $serviceRequestRepository;
    private $requestKeysRepo;
    private $responseKeyRepository;

    public function __construct(EntityManagerInterface $entityManager, HttpRequestService $httpRequestService)
    {
        $this->entityManager = $entityManager;
        $this->httpRequestService = $httpRequestService;
        $this->serviceRepository = $this->entityManager->getRepository(Service::class);
        $this->serviceRequestRepository = $this->entityManager->getRepository(ServiceRequest::class);
        $this->responseKeyRepository = $this->entityManager->getRepository(ServiceResponseKey::class);
        $this->requestKeysRepo = $this->entityManager->getRepository(ServiceRequestResponseKey::class);
    }

    public function findByParams(string $sort, string $order, int $count) {
        return $this->responseKeyRepository->findByParams($sort, $order, $count);
    }

    public function getResponseKeyById(int $responseKeyId) {
        $responseKey = $this->responseKeyRepository->findOneBy(["id" => $responseKeyId]);
        if ($responseKey === null) {
            throw new BadRequestHttpException(sprintf("Response key id:%s not found in database.",
                $responseKeyId
            ));
        }
        return $responseKey;
    }

    public function getRequestResponseKeyById(int $requestResponseKeyId) {
        $requestResponseKey = $this->requestKeysRepo->findOneBy(["id" => $requestResponseKeyId]);
        if ($requestResponseKey === null) {
            throw new BadRequestHttpException(sprintf("Request response key id:%s not found in database.",
                $requestResponseKeyId
            ));
        }
        return $requestResponseKey;
    }

    private function getServiceResponseKeysObject(ServiceResponseKey $responseKeys, Service $service, array $data)
    {
        $responseKeys->setService($service);
        $responseKeys->setKeyName($data['key_name']);
        $responseKeys->setKeyValue($data['key_value']);
        return $responseKeys;
    }

    public function getResponseKeysByServiceId(int $serviceId) {
        $service = $this->serviceRepository->findBy(["id" => $serviceId]);
        if ($service === null) {
            throw new BadRequestHttpException(sprintf("Service id:%s not found.". $serviceId));
        }
        return $this->responseKeyRepository->findBy(["service" => $service]);
    }
    public function createServiceResponseKeys(array $data)
    {
        $service = $this->serviceRepository->findOneBy(["id" => $data["service_id"]]);
        $responseKey = $this->getServiceResponseKeysObject(new ServiceResponseKey(), $service, $data);
        if ($this->httpRequestService->validateData($responseKey)) {
            return $this->responseKeyRepository->save($responseKey);
        }
        return false;
    }

    public function updateServiceResponseKeys(array $data)
    {
        $getResponseKey = $this->responseKeyRepository->findOneBy(["id" => $data["id"]]);
        $service = $this->serviceRepository->findOneBy(["id" => $data["service_id"]]);
        $responseKey = $this->getServiceResponseKeysObject($getResponseKey, $service, $data);
        if ($this->httpRequestService->validateData($responseKey)) {
            return $this->responseKeyRepository->save($responseKey);
        }
        return false;
    }

    public function deleteServiceResponseKey(int $id) {
        $responseKey = $this->responseKeyRepository->findOneBy(["id" => $id]);
        if ($responseKey === null) {
            throw new BadRequestHttpException(sprintf("Service response key id: %s not found in database.", $id));
        }
        return $this->responseKeyRepository->delete($responseKey);
    }

    public function getRequestResponseKeyObjectById(ServiceRequest $serviceRequest, int $responseKeyId) {
        $responseKey = $this->getResponseKeyById($responseKeyId);
        return $this->getRequestResponseKey($serviceRequest, $responseKey);
    }

    private function buildRequestResponseKeyObject(ServiceResponseKey $responseKey, 
                                                   $requestResponseKey = null) {
        $object = new \stdClass();
        $object->id = $responseKey->getId();
        $object->key_name = $responseKey->getKeyName();
        $object->key_value = ($requestResponseKey !== null)? $requestResponseKey->getResponseKeyValue() : "";
        $object->show_in_response = ($requestResponseKey !== null)? $requestResponseKey->getShowInResponse() : false;
        $object->list_item = ($requestResponseKey !== null)? $requestResponseKey->getListItem() : false;
        $object->has_array_value = ($requestResponseKey !== null)? $requestResponseKey->getHasArrayValue() : false;
        $object->array_keys = ($requestResponseKey !== null)? $requestResponseKey->getArrayKeys() : false;
        $object->return_data_type = ($requestResponseKey !== null)? $requestResponseKey->getReturnDataType() : false;
        $object->prepend_extra_data = ($requestResponseKey !== null)? $requestResponseKey->getPrependExtraData() : false;
        $object->prepend_extra_data_value = ($requestResponseKey !== null)? $requestResponseKey->getPrependExtraDataValue() : false;
        $object->append_extra_data = ($requestResponseKey !== null)? $requestResponseKey->getAppendExtraData() : false;
        $object->append_extra_data_value = ($requestResponseKey !== null)? $requestResponseKey->getAppendExtraDataValue() : false;
        return $object;
    }
    
    public function getRequestResponseKey(ServiceRequest $serviceRequest, ServiceResponseKey $responseKey) {
        $getRequestResponseKey = $this->responseKeyRepository->getRequestResponseKey($serviceRequest, $responseKey);
        return $this->buildRequestResponseKeyObject($responseKey, $getRequestResponseKey);
    }

    public function getRequestResponseKeys(int $requestId, string $sort = "key_name", string $order = "asc", int $count = null) {
        $request = $this->serviceRequestRepository->findOneBy(["id" => $requestId]);
        $responseKeys = $this->responseKeyRepository->findBy(["service" => $request->getService()]);

        return array_map(function ($responseKey) use($request) {
            $requestResponseKey = $this->requestKeysRepo->findOneBy(["service_request" => $request, "service_response_key" => $responseKey]);
            return $this->buildRequestResponseKeyObject($responseKey, $requestResponseKey);
        }, $responseKeys);
    }
    public function getRequestResponseKeyByName(Provider $provider, ServiceRequest $serviceRequest, string $configItemName)
    {
        return $this->requestKeysRepo->getRequestResponseKeyByName($provider, $serviceRequest, $configItemName);
    }

    public function setRequestResponseKeyObject(ServiceRequestResponseKey $requestResponseKey,
                                                ServiceRequest $serviceRequest,
                                                ServiceResponseKey $responseKey, array $data) {
        $requestResponseKey->setServiceRequest($serviceRequest);
        $requestResponseKey->setServiceResponseKey($responseKey);
        $requestResponseKey->setResponseKeyValue($data['key_value']);
        $requestResponseKey->setShowInResponse($data['show_in_response']);
        $requestResponseKey->setListItem($data['list_item']);

        $requestResponseKey->setAppendExtraData($data['append_extra_data']);
        $requestResponseKey->setAppendExtraDataValue($data['append_extra_data_value']);
        $requestResponseKey->setPrependExtraData($data['prepend_extra_data']);
        $requestResponseKey->setPrependExtraDataValue($data['prepend_extra_data_value']);

        $requestResponseKey->setHasArrayValue($data['has_array_value']);
        if((array_key_exists("array_keys", $data) && is_array($data['array_keys'])) ||
            (array_key_exists("array_keys", $data) && $data['array_keys'] === null)
        ) {
            $requestResponseKey->setArrayKeys($data['array_keys']);
        } else {

            $requestResponseKey->setArrayKeys(null);
        }
        if(array_key_exists("return_data_type", $data) && isset($data['return_data_type']) && $data['return_data_type'] !== null) {
            $requestResponseKey->setReturnDataType($data['return_data_type']);
        }
        return $requestResponseKey;
    }

    public function createRequestResponseKey(array $data) {
        $serviceRequest = $this->serviceRequestRepository->findOneBy(["id" => $data['service_request_id']]);
        $responseKey = $this->getResponseKeyById($data['id']);
        $setRequestResponseKey = $this->setRequestResponseKeyObject(
            new ServiceRequestResponseKey(),
            $serviceRequest, $responseKey, $data);
        return $this->requestKeysRepo->saveRequestResponseKey($setRequestResponseKey);
    }

    public function updateRequestResponseKey(array $data) {
        $serviceRequest = $this->serviceRequestRepository->findOneBy(["id" => $data['service_request_id']]);
        $responseKey = $this->getResponseKeyById($data['id']);
        $requestResponseKey = $this->requestKeysRepo->findOneBy(["service_request" => $serviceRequest, "service_response_key" => $responseKey]);

        if ($requestResponseKey !== null) {
            $setRequestResponseKey = $this->setRequestResponseKeyObject(
                $requestResponseKey,
                $requestResponseKey->getServiceRequest(),
                $requestResponseKey->getServiceResponseKey(),
                $data
            );
            return $this->requestKeysRepo->saveRequestResponseKey($setRequestResponseKey);
        }
        $setRequestResponseKey = $this->setRequestResponseKeyObject(
            new ServiceRequestResponseKey(),
            $serviceRequest, $responseKey, $data);
        return $this->requestKeysRepo->saveRequestResponseKey($setRequestResponseKey);
    }


    public function deleteRequestResponseKey(int $serviceRequestId, int $responseKeyId) {
        $serviceRequest = $this->serviceRequestRepository->findOneBy(["id" => $serviceRequestId]);
        $responseKey = $this->getResponseKeyById($responseKeyId);

        $requestResponseKey = $this->requestKeysRepo->findOneBy([
            "service_request" => $serviceRequest,
            "service_response_key" => $responseKey]);


        if ($requestResponseKey !== null) {
            return $this->requestKeysRepo->deleteRequestResponseKeys($requestResponseKey);
        }
        throw new BadRequestHttpException(
            sprintf("Error deleting property value. (Service request id:%s, Response key id:%s)",
                $requestResponseKey, $responseKeyId
            ));
    }
}