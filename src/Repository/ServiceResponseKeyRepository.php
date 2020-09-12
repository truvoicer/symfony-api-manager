<?php

namespace App\Repository;

use App\Entity\Provider;
use App\Entity\Service;
use App\Entity\ServiceRequest;
use App\Entity\ServiceRequestParameter;
use App\Entity\ServiceRequestResponseKey;
use App\Entity\ServiceResponseKey;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ServiceResponseKey|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiceResponseKey|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiceResponseKey[]    findAll()
 * @method ServiceResponseKey[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceResponseKeyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceResponseKey::class);
    }

    public function getResponseKeyByName(Provider $provider, ServiceRequest $serviceRequest)
    {
        return $this->getEntityManager()
            ->createQuery("SELECT srk FROM App\Entity\ServiceRequest sr
                            JOIN App\Entity\ServiceResponseKey srk
                            WHERE sr.provider = :provider
                            AND  sr = :serviceRequest
                            AND sr.service = srk.service")
            ->setParameter("provider", $provider)
            ->setParameter("serviceRequest", $serviceRequest)
            ->getResult();
    }

    public function getResponseKeys(Provider $provider, ServiceRequest $serviceRequest)
    {
        return $this->getEntityManager()
            ->createQuery("SELECT srk FROM App\Entity\ServiceRequest sr
                            JOIN App\Entity\ServiceResponseKey srk
                            JOIN App\Entity\ServiceRequestResponseKey srrk
                            WHERE sr.provider = :provider
                            AND  sr = :serviceRequest
                            AND sr.service = srk.service")
            ->setParameter("provider", $provider)
            ->setParameter("serviceRequest", $serviceRequest)
            ->getResult();
    }

    public function getRequestResponseKey(ServiceRequest $serviceRequest, ServiceResponseKey $responseKey) {
        $requestResponseKeyRepo = $this->getEntityManager()->getRepository(ServiceRequestResponseKey::class);
        return $requestResponseKeyRepo->findOneBy([
            "service_request" => $serviceRequest,
            "service_response_key" => $responseKey
        ]);
    }

    public function save(ServiceResponseKey $service)
    {
        $this->getEntityManager()->persist($service);
        $this->getEntityManager()->flush();
        return $service;
    }

    public function delete(ServiceResponseKey $service) {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($service);
        $entityManager->flush();
        return $service;
    }
}
