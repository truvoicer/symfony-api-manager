<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Provider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function findByQuery($query)
    {
        return $this->createQueryBuilder('c')
            ->where("c.category_label LIKE :query")
            ->orWhere("c.category_name LIKE :query")
            ->setParameter("query", "%" . $query . "%")
            ->getQuery()
            ->getResult();
    }

    public function saveCategory(Category $category)
    {
        $this->getEntityManager()->persist($category);
        $this->getEntityManager()->flush();
        return $category;
    }

    public function findByParams(string $sort, string  $order, int $count)
    {
        $query = $this->createQueryBuilder('p')
            ->addOrderBy('p.'.$sort, $order);
        if ($count !== null && $count > 0) {
            $query->setMaxResults($count);
        }
        return $query->getQuery()
            ->getResult()
            ;
    }

    public function deleteCategory(Category $category) {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($category);
        $entityManager->flush();
        return $category;
    }
}
