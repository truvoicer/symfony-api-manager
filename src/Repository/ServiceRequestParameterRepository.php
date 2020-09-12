<?php

namespace App\Repository;

use App\Entity\Provider;
use App\Entity\Service;
use App\Entity\ServiceRequest;
use App\Entity\ServiceRequestParameter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ServiceRequestParameter|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiceRequestParameter|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiceRequestParameter[]    findAll()
 * @method ServiceRequestParameter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceRequestParameterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceRequestParameter::class);
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

    public function getRequestParametersByRequestName(Provider $provider, string $serviceRequestName = null)
    {
        return $this->getEntityManager()
            ->createQuery("SELECT srp FROM App\Entity\ServiceRequest sr
                            JOIN App\Entity\ServiceRequestParameter srp
                            WHERE sr.provider = :provider
                            AND  srp.service_request = sr
                            AND sr.service_request_name = :serviceRequestName")
            ->setParameter("provider", $provider)
            ->setParameter("serviceRequestName", $serviceRequestName)
            ->getResult();
    }

    public function save(ServiceRequestParameter $service)
    {
        $this->getEntityManager()->persist($service);
        $this->getEntityManager()->flush();
        return $service;
    }

    public function delete(ServiceRequestParameter $service)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($service);
        $entityManager->flush();
        return $service;
    }
}
