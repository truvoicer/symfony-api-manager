<?php

namespace App\Repository;

use App\Entity\Provider;
use App\Entity\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Service|null find($id, $lockMode = null, $lockVersion = null)
 * @method Service|null findOneBy(array $criteria, array $orderBy = null)
 * @method Service[]    findAll()
 * @method Service[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Service::class);
    }

    public function findByQuery($query)
    {
        return $this->createQueryBuilder('s')
            ->where("s.service_label LIKE :query")
            ->orWhere("s.service_name LIKE :query")
            ->setParameter("query", "%" . $query . "%")
            ->getQuery()
            ->getResult();
    }

    public function saveService(Service $service)
    {
        $this->getEntityManager()->persist($service);
        $this->getEntityManager()->flush();
        return $service;
    }

    public function getServiceByRequestName(Provider $provider, string $serviceName) {
        return $this->getEntityManager()
            ->createQuery("SELECT s from App\Entity\ServiceRequest sr
                            JOIN App\Entity\Service s 
                            WHERE sr.service = s
                            AND sr.provider = :provider 
                            AND sr.service_request_name = :serviceName")
            ->setParameter("provider", $provider)
            ->setParameter("serviceName", $serviceName)
            ->getOneOrNullResult();
    }

    public function getServiceParameters(Service $service) {
        return $this->getEntityManager()
            ->createQuery("SELECT p from App\Entity\ServiceParameter sp
                            JOIN App\Entity\Service s 
                            JOIN App\Entity\Parameter p
                            WHERE sp.service = :service
                            AND sp.parameter = p 
                            AND sp.service = s")
            ->setParameter("service", $service)
            ->getResult();
    }
    public function findByParams(string $sort, string  $order, int $count)
    {
        $query = $this->createQueryBuilder('s')
            ->addOrderBy('s.'.$sort, $order);
        if ($count !== null && $count > 0) {
            $query->setMaxResults($count);
        }
        return $query->getQuery()
            ->getResult()
            ;
    }

    public function deleteService(Service $service) {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($service);
        $entityManager->flush();
        return $service;
    }
}
