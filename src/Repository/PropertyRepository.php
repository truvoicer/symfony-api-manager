<?php

namespace App\Repository;

use App\Entity\Property;
use App\Entity\Provider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    public function createProperty(Property $propertyObject)
    {
        $property = new Property();
        $property->setPropertyName($propertyObject->getPropertyName());
        $property->setPropertyLabel($propertyObject->getPropertyLabel());
        $this->getEntityManager()->persist($property);
        $this->getEntityManager()->flush();
        return $property;
    }

    public function updateProperty(Property $propertyObject)
    {
        $getProperty = $this->findOneBy(["id" => $propertyObject->getId()]);
        if ($getProperty !== null) {
            $getProperty->setPropertyName($propertyObject->getPropertyName());
            $getProperty->setPropertyLabel($propertyObject->getPropertyLabel());
            $this->getEntityManager()->flush();
            return $getProperty;
        }
        return false;
    }

    public function deleteProperty(Property $property) {
        $entityManager = $this->getEntityManager();
        $getProperty = $this->findOneBy(["id" => $property->getId()]);
        if ($getProperty != null) {
            $entityManager->remove($getProperty);
            $entityManager->flush();
            return true;
        }
        return false;
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
}
