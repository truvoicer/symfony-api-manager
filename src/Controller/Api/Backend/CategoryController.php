<?php
namespace App\Controller\Api\Backend;

use App\Controller\Api\BaseController;
use App\Entity\Category;
use App\Service\HttpRequestService;
use App\Service\CategoryService;
use App\Service\SerializerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Contains api endpoint functions for category related tasks
 *
 * Require ROLE_ADMIN for *every* controller method in this class.
 *
 * @IsGranted("ROLE_ADMIN")
 */
class CategoryController extends BaseController
{
    private $serializerService;
    private $httpRequestService;
    private $categoryService;

    /**
     * CategoryController constructor.
     * Initialise services for this class
     *
     * @param SerializerService $serializerService
     * @param CategoryService $categoryService
     * @param HttpRequestService $httpRequestService
     */
    public function __construct(
                                SerializerService $serializerService, 
                                CategoryService $categoryService,
                                HttpRequestService $httpRequestService)
    {
        $this->serializerService = $serializerService;
        $this->httpRequestService = $httpRequestService;
        $this->categoryService = $categoryService;
    }

    /**
     * Gets a list of categories from database based on the request get query parameters
     *
     * @Route("/api/categories", name="api_get_categories", methods={"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getCategories(Request $request)
    {
        $getCategories = $this->categoryService->findByParams(
            $request->get('sort', "category_name"),
            $request->get('order', "asc"),
            (int) $request->get('count', null)
        );
        return $this->jsonResponseSuccess("success",
            $this->serializerService->entityArrayToArray($getCategories));
    }

    /**
     * Gets a single category from the database based on the get request query parameters
     *
     * @Route("/api/category/{id}", name="api_get_single_category", methods={"GET"})
     * @param Category $category
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getSingleCategory(Category $category)
    {
        return $this->jsonResponseSuccess("success",
            $this->serializerService->entityToArray($category));
    }

    /**
     * Creates a new category based on the request post data
     *
     * @param Request $request
     * @Route("/api/category/create", name="api_create_category", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createCategory(Request $request) {
        $requestData = $this->httpRequestService->getRequestData($request, true);
        $create = $this->categoryService->createCategory($requestData);
        if(!$create) {
            return $this->jsonResponseFail("Error creating category.");
        }
        return $this->jsonResponseSuccess("Successfully created category.",
            $this->serializerService->entityToArray($create));
    }

    /**
     * Updates a new category based on request post data
     *
     * @param Request $request
     * @Route("/api/category/update", name="api_update_category", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateCategory(Request $request) {
        $requestData = $this->httpRequestService->getRequestData($request, true);
        $create = $this->categoryService->updateCategory($requestData);
        if(!$create) {
            return $this->jsonResponseFail("Error updating category.");
        }
        return $this->jsonResponseSuccess("Successfully updated category.",
            $this->serializerService->entityToArray($create));
    }

    /**
     * Deletes a category based on the request post data
     *
     * @param Request $request
     * @Route("/api/category/delete", name="api_delete_category", methods={"POST"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteCategory(Request $request)
    {
        $requestData = $this->httpRequestService->getRequestData($request, true);
        $delete = $this->categoryService->deleteCategoryById($requestData['item_id']);
        if (!$delete) {
            return $this->jsonResponseFail("Error deleting category", $this->serializerService->entityToArray($delete, ['main']));
        }
        return $this->jsonResponseSuccess("Category deleted.", $this->serializerService->entityToArray($delete, ['main']));
    }
}
