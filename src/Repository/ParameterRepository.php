<?php

namespace App\Repository;

use App\Entity\Parameter;
use App\Entity\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Parameter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Parameter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Parameter[]    findAll()
 * @method Parameter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParameterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parameter::class);
    }

    public function saveParameter(Parameter $parameter)
    {
        $this->getEntityManager()->persist($parameter);
        $this->getEntityManager()->flush();
        return $parameter;
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

    public function deleteParameter(Parameter $parameter) {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($parameter);
        $entityManager->flush();
        return $parameter;
    }
}
