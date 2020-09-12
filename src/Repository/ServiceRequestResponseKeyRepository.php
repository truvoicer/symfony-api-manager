<?php

namespace App\Repository;

use App\Entity\Provider;
use App\Entity\ServiceRequest;
use App\Entity\ServiceRequestResponseKey;
use App\Entity\ServiceResponseKey;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ServiceRequestResponseKey|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiceRequestResponseKey|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiceRequestResponseKey[]    findAll()
 * @method ServiceRequestResponseKey[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceRequestResponseKeyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceRequestResponseKey::class);
    }

    public function getRequestResponseKeyByName(Provider $provider, ServiceRequest $serviceRequest, string $keyName)
    {
        foreach ($serviceRequest->getServiceRequestResponseKeys() as $serviceRequestResponseKey) {
            if ($serviceRequestResponseKey->getServiceResponseKey()->getKeyValue() === $keyName) {
                return $serviceRequestResponseKey;
            }
        }
        return null;
    }

    public function saveRequestResponseKey(ServiceRequestResponseKey $requestResponseKey) {
        $this->getEntityManager()->persist($requestResponseKey);
        $this->getEntityManager()->flush();
        return $requestResponseKey;
    }

    public function deleteRequestResponseKeys(ServiceRequestResponseKey $requestResponseKey) {
        $this->getEntityManager()->remove($requestResponseKey);
        $this->getEntityManager()->flush();
        return $requestResponseKey;
    }
}
