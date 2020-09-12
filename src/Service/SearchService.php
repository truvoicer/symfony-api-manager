<?php
namespace App\Service;

use App\Entity\Provider;
use Doctrine\ORM\EntityManagerInterface;

class SearchService
{
    private $entityManager;
    private $httpRequestService;
    private $providerService;
    private $requestService;
    private $categoryService;
    private $apiService;

    public function __construct(EntityManagerInterface $entityManager, HttpRequestService $httpRequestService,
                                ProviderService $providerService, RequestService $requestService,
                                CategoryService $categoryService, ApiServicesService $apiService)
    {
        $this->entityManager = $entityManager;
        $this->httpRequestService = $httpRequestService;
        $this->providerService = $providerService;
        $this->requestService = $requestService;
        $this->categoryService = $categoryService;
        $this->apiService = $apiService;
    }

    public function performSearch($query)
    {
        $getProviders = $this->providerService->findByQuery($query);
        if (count($getProviders) > 0) {
            return [
              "type" => "provider",
              "items" => $getProviders
            ];
        }

        $getServiceRequests = $this->requestService->findByQuery($query);
        if (count($getServiceRequests) > 0) {
            return [
                "type" => "service_requests",
                "items" => $getServiceRequests
            ];
        }

        $getCategories = $this->categoryService->findByQuery($query);
        if (count($getCategories) > 0) {
            return [
                "type" => "categories",
                "items" => $getCategories
            ];
        }

        $getApiServices = $this->apiService->findByQuery($query);
        if (count($getApiServices) > 0) {
            return [
                "type" => "services",
                "items" => $getApiServices
            ];
        }
        return [];
    }


}