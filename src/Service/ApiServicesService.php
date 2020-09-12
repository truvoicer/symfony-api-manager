<?php
namespace App\Service;


use App\Entity\Category;
use App\Entity\Service;
use App\Entity\ServiceRequest;
use App\Entity\ServiceRequestParameter;
use App\Entity\ServiceResponseKey;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ApiServicesService
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

    public function findByQuery(string $query)
    {
        return $this->serviceRepository->findByQuery($query);
    }

    public function findByParams(string $sort, string $order, int $count) {
        return $this->serviceRepository->findByParams($sort, $order, $count);
    }

    public function getServiceById($id) {
        $getService = $this->serviceRepository->findOneBy(["id" => $id]);
        if ($getService === null) {
            throw new BadRequestHttpException("Service does not exist in database.");
        }
        return $getService;
    }

    private function getServiceObject(Service $service, array $data)
    {
        $categoryRepo = $this->entityManager->getRepository(Category::class);
        $service->setServiceLabel($data['service_label']);
        $service->setServiceName($data['service_name']);
        if (!isset($data["category"]) || !isset($data["category"]["id"])) {
            throw new BadRequestHttpException("No category selected.");
        }
        $category = $categoryRepo->findOneBy(["id" => $data["category"]["id"]]);
        $service->setCategory($category);
        return $service;
    }

    public function createService(array $data)
    {
        $service = $this->getServiceObject(new Service(), $data);
        if ($this->httpRequestService->validateData($service)) {
            return $this->serviceRepository->saveService($service);
        }
        return false;
    }

    public function updateService(array $data)
    {
        $service = $this->serviceRepository->findOneBy(["id" => $data['id']]);
        $update = $this->getServiceObject($service, $data);
        if ($this->httpRequestService->validateData($update)) {
            return $this->serviceRepository->saveService($service);
        }
        return false;
    }

    public function deleteService(int $serviceId) {
        $service = $this->serviceRepository->findOneBy(["id" => $serviceId]);
        if ($service === null) {
            throw new BadRequestHttpException(sprintf("Service id: %s not found in database.", $serviceId));
        }
        return $this->serviceRepository->deleteService($service);
    }

}