<?php

namespace App\Repository;

use App\Entity\Provider;
use App\Entity\ServiceRequest;
use App\Entity\ServiceRequestConfig;
use App\Entity\ServiceRequestParameter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ServiceRequestConfig|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiceRequestConfig|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiceRequestConfig[]    findAll()
 * @method ServiceRequestConfig[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceRequestConfigRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceRequestConfig::class);
    }

    public function findByParams(ServiceRequest $serviceRequest, string $sort, string $order, int $count)
    {
        $query = $this->createQueryBuilder('p')
            ->addOrderBy('p.' . $sort, $order);
        if ($count !== null && $count > 0) {
            $query->setMaxResults($count);
        }
        $query->where("p.service_request = :serviceRequest")
            ->setParameter("serviceRequest", $serviceRequest);
        return $query->getQuery()
            ->getResult();
    }

    public function getRequestConfigByName(Provider $provider, ServiceRequest $serviceRequest, string $configItemName)
    {
        return $this->getEntityManager()
        ->createQuery("SELECT src FROM App\Entity\ServiceRequest sr
                            JOIN App\Entity\ServiceRequestConfig src
                            WHERE sr.provider = :provider
                            AND  src.service_request = sr
                            AND sr = :serviceRequest
                            AND src.item_name =:configItemName")
        ->setParameter("provider", $provider)
        ->setParameter("serviceRequest", $serviceRequest)
        ->setParameter('configItemName', $configItemName)
        ->getOneOrNullResult();
    }

    public function save(ServiceRequestConfig $service)
    {
        $this->getEntityManager()->persist($service);
        $this->getEntityManager()->flush();
        return $service;
    }

    public function delete(ServiceRequestConfig $service)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($service);
        $entityManager->flush();
        return $service;
    }
}
