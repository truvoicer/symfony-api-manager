<?php
namespace App\Service;


use App\Entity\Category;
use App\Entity\Service;
use App\Entity\Provider;
use App\Entity\ServiceRequest;
use App\Entity\ServiceRequestParameter;
use App\Entity\ServiceResponseKey;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RequestParametersService
{
    private $entityManager;
    private $httpRequestService;
    private $serviceRepository;
    private $serviceRequestRepository;
    private $requestParametersRepo;
    private $responseKeysRepo;

    public function __construct(EntityManagerInterface $entityManager, HttpRequestService $httpRequestService)
    {
        $this->entityManager = $entityManager;
        $this->httpRequestService = $httpRequestService;
        $this->serviceRepository = $this->entityManager->getRepository(Service::class);
        $this->serviceRequestRepository = $this->entityManager->getRepository(ServiceRequest::class);
        $this->requestParametersRepo = $this->entityManager->getRepository(ServiceRequestParameter::class);
        $this->responseKeysRepo = $this->entityManager->getRepository(ServiceResponseKey::class);
    }

    public function findByParams(int $serviceRequestId, string $sort, string $order, int $count) {
        $serviceRequest = $this->serviceRequestRepository->findOneBy(["id" => $serviceRequestId]);
        if ($serviceRequest !== null) {
            return $this->requestParametersRepo->findByParams($serviceRequest, $sort, $order, $count);
        }
    }

    private function getServiceRequestParametersObject(ServiceRequestParameter $requestParameters,
                                                       ServiceRequest $serviceRequest, array $data)
    {
        $requestParameters->setParameterValue($data['parameter_value']);
        $requestParameters->setParameterName($data['parameter_name']);
        $requestParameters->setServiceRequest($serviceRequest);
        return $requestParameters;
    }

    public function createRequestParameter(array $data)
    {
        $serviceRequest = $this->serviceRequestRepository->findOneBy(["id" => $data["service_request_id"]]);
        $requestParameter = $this->getServiceRequestParametersObject(new ServiceRequestParameter(), $serviceRequest, $data);
        if ($this->httpRequestService->validateData($requestParameter)) {
            return $this->requestParametersRepo->save($requestParameter);
        }
        return false;
    }

    public function updateRequestParameter(array $data)
    {
        $getRequestParameter = $this->requestParametersRepo->findOneBy(["id" => $data["id"]]);
        $serviceRequest = $this->serviceRequestRepository->findOneBy(["id" => $data["service_request_id"]]);
        $requestParameter = $this->getServiceRequestParametersObject($getRequestParameter, $serviceRequest, $data);
        if ($this->httpRequestService->validateData($requestParameter)) {
            return $this->requestParametersRepo->save($requestParameter);
        }
        return false;
    }

    public function deleteRequestParameter(int $id) {
        $requestParameter = $this->requestParametersRepo->findOneBy(["id" => $id]);
        if ($requestParameter === null) {
            throw new BadRequestHttpException(sprintf("Service request parameter id: %s not found in database.", $id));
        }
        return $this->requestParametersRepo->delete($requestParameter);
    }


}