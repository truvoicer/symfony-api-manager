<?php
namespace App\Service;

use App\Entity\Category;
use App\Entity\Provider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CategoryService
{
    private $entityManager;
    private $httpRequestService;
    private $categoryRepository;

    public function __construct(EntityManagerInterface $entityManager, HttpRequestService $httpRequestService)
    {
        $this->entityManager = $entityManager;
        $this->httpRequestService = $httpRequestService;
        $this->categoryRepository = $this->entityManager->getRepository(Category::class);
    }

    public function findByQuery(string $query)
    {
        return $this->categoryRepository->findByQuery($query);
    }

    public function getCategoryProviderList(Category $category) {
        $providerArray = [];
        $i = 0;
        foreach ($category->getProviders() as $provider) {
            $providerArray[$i]['id'] = $provider->getId();
            $providerArray[$i]['provider_name'] = $provider->getProviderName();
            $providerArray[$i]['provider_label'] = $provider->getProviderLabel();
            $i++;
        };
        return $providerArray;
    }

    public function getCategoryById(int $categoryId) {
        $category = $this->categoryRepository->findOneBy(["id" => $categoryId]);
        if ($category === null) {
            throw new BadRequestHttpException(sprintf("Category id:%s not found in database.",
                $categoryId
            ));
        }
        return $category;
    }

    private function getCategoryObject(Category $category, array $data) {
        $category->setCategoryName($data['category_name']);
        $category->setCategoryLabel($data['category_label']);
        return $category;
    }

    public function createCategory(array $data)
    {
        $checkCategory = $this->categoryRepository->findOneBy(["category_name" => $data['category_name']]);
        if ($checkCategory !== null) {
            throw new BadRequestHttpException(sprintf("Category (%s) alreqady exists.", $data['category_name']));
        }
        $category = $this->getCategoryObject(new Category(), $data);
        if ($this->httpRequestService->validateData(
            $category
        )) {
            return $this->categoryRepository->saveCategory($category);
        }
        return false;
    }

    public function updateCategory(array $data)
    {
        $category = $this->categoryRepository->findOneBy(["id" => $data["id"]]);
        if ($category === null) {
            throw new BadRequestHttpException(sprintf("Category id:%d not found in database.", $data["id"]));
        }
        if ($this->httpRequestService->validateData(
            $this->getCategoryObject($category, $data)
        )) {
            return $this->categoryRepository->saveCategory($category);
        }
        return false;
    }

    public function findByParams(string $sort, string  $order, int $count) {
       return  $this->categoryRepository->findByParams($sort,  $order, $count);
    }

    public function deleteCategoryById(int $categoryId) {
        $category = $this->categoryRepository->findOneBy(["id" => $categoryId]);
        if ($category === null) {
            throw new BadRequestHttpException(sprintf("Category id: %s not found in database.", $categoryId));
        }
        return $this->categoryRepository->deleteCategory($category);
    }
    public function deleteCategory(Category $category) {
        if ($category === null) {
            return false;
        }
        return $this->categoryRepository->deleteCategory($category);
    }



}