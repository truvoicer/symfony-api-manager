<?php
namespace App\Controller\Api\Backend;

use App\Controller\Api\BaseController;
use App\Entity\Category;
use App\Entity\Provider;
use App\Service\ApiServicesService;
use App\Service\HttpRequestService;
use App\Service\CategoryService;
use App\Service\SearchService;
use App\Service\SerializerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Contains api endpoint functions for search related tasks
 *
 * Require ROLE_ADMIN for *every* controller method in this class.
 *
 * @IsGranted("ROLE_ADMIN")
 */
class SearchController extends BaseController
{
    private $serializerService;
    private $httpRequestService;
    private $searchService;

    /**
     * SearchController constructor.
     * Initialise services to be used in this class
     *
     * @param SerializerService $serializerService
     * @param HttpRequestService $httpRequestService
     * @param SearchService $searchService
     */
    public function __construct(
        SerializerService $serializerService, HttpRequestService $httpRequestService, SearchService $searchService)
    {
        $this->serializerService = $serializerService;
        $this->httpRequestService = $httpRequestService;
        $this->searchService = $searchService;
    }

    /**
     * Performs a database search based on query parameters in the get request
     * Returns array of search results
     *
     * @Route("/api/admin/search/{query}", name="api_admin_search", methods={"GET"})
     * @param string $query
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function search(string $query)
    {
        $searchArray = $this->searchService->performSearch($query);
        if (count($searchArray) > 0) {
            $searchArray["items"] = array_map(function ($item) {
                return $this->serializerService->entityToArray($item, ["search"]);
            }, $searchArray["items"]);
        }
        return $this->jsonResponseSuccess("success", $searchArray);
    }
}
