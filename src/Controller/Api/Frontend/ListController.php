<?php
namespace App\Controller\Api\Frontend;

use App\Controller\Api\BaseController;
use App\Entity\Category;
use App\Entity\Provider;
use App\Entity\ServiceRequestConfig;
use App\Service\ApiServicesService;
use App\Service\CategoryService;
use App\Service\HttpRequestService;
use App\Service\ProviderService;
use App\Service\RequestConfigService;
use App\Service\SerializerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Require ROLE_ADMIN for *every* controller method in this class.
 *
 * @IsGranted("ROLE_USER")
 */
class ListController extends BaseController
{
    private $providerService;
    private $serializerService;
    private $httpRequestService;
    private $categoryService;
    private $requestConfigService;

    public function __construct(ProviderService $providerService, HttpRequestService $httpRequestService,
                                SerializerService $serializerService, CategoryService $categoryService)
    {
        $this->providerService = $providerService;
        $this->serializerService = $serializerService;
        $this->httpRequestService = $httpRequestService;
        $this->categoryService = $categoryService;
    }

    /**
     * @Route("/api/category/{category_name}/providers", name="api_get_category_providerlist", methods={"GET"})
     * @param Category $category
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getCategoryProviderList(Category $category)
    {
        if ($category === null) {
            throw new BadRequestHttpException("Category doesn't exist");
        }
        return $this->jsonResponseSuccess("success",
            $this->categoryService->getCategoryProviderList($category));
    }

}
