<?php

namespace App\Repository;

use App\Entity\Property;
use App\Entity\Provider;
use App\Entity\ProviderProperty;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @method ProviderProperty|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProviderProperty|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProviderProperty[]    findAll()
 * @method ProviderProperty[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProviderPropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProviderProperty::class);
    }

    public function createProviderProperty(Provider $provider, Property $property, string $propertyValue) {
        $providerProp = new ProviderProperty();
        $providerProp->setProvider($provider);
        $providerProp->setProperty($property);
        $providerProp->setPropertyValue($propertyValue);
        $this->getEntityManager()->persist($providerProp);
        $this->getEntityManager()->flush();
        return $providerProp;
    }
    public function saveProviderProperty(ProviderProperty $providerProperty) {
        $this->getEntityManager()->persist($providerProperty);
        $this->getEntityManager()->flush();
        return $providerProperty;
    }

    public function deleteProviderProperty(ProviderProperty $providerProperty) {
        $this->getEntityManager()->remove($providerProperty);
        $this->getEntityManager()->flush();
        return $providerProperty;
    }

}
