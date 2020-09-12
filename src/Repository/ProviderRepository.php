<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Property;
use App\Entity\Provider;
use App\Entity\ProviderProperty;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Provider|null find($id, $lockMode = null, $lockVersion = null)
 * @method Provider|null findOneBy(array $criteria, array $orderBy = null)
 * @method Provider[]    findAll()
 * @method Provider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProviderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Provider::class);
    }

    public function findByQuery($query)
    {
        $query = $this->createQueryBuilder('p')
            ->where("p.provider_name LIKE :query")
            ->orWhere("p.provider_label LIKE :query")
            ->setParameter("query", "%" . $query . "%")
            ->getQuery()
            ->getResult();
        return $query;
    }

    /**
     * @param string $value
     * @return Provider|null Returns an array of Provider objects
     */
    public function findByName(string $value)
    {
        return $this->findOneBy(["provider_name" => $value]);
    }

    /**
     * @param int $providerId
     * @return Provider|null Returns an array of Provider objects
     */
    public function getProviderById(int $providerId)
    {
        return $this->findOneBy(["id" => $providerId]);
    }

    public function findByParams(string $sort,  string $order, int $count)
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
    public function createProvider(Provider $provider)
    {
        $provider->setDateUpdated(new DateTime());
        $provider->setDateAdded(new DateTime());
        $this->getEntityManager()->persist($provider);
        $this->getEntityManager()->flush();
        return $provider;
    }

    public function updateProvider(Provider $provider)
    {
        $provider->setDateUpdated(new DateTime());
        $this->getEntityManager()->persist($provider);
        $this->getEntityManager()->flush();
        return $provider;
    }

    public function createProviderProperty(Provider $provider, Property $property, string $propertyValue) {
        $providerProp = new ProviderProperty();
        $providerProp->setProvider($provider);
        $providerProp->setProperty($property);
        $providerProp->setPropertyValue($propertyValue);
        $provider->addProviderProperty($providerProp);
        $this->getEntityManager()->persist($providerProp);
        $this->getEntityManager()->flush();
        return $providerProp;
    }

    public function getProviderProperty(Provider $provider, Property $property) {
        $em = $this->getEntityManager();
        $providerPropertyRepo = $em->getRepository(ProviderProperty::class);
        return $providerPropertyRepo->findOneBy(["provider" => $provider, "property" => $property]);
    }

    public function getProviderPropsByProviderId(int $providerId) {
        $em = $this->getEntityManager();
        $provider = $this->findOneBy(["id" => $providerId]);
        return $em->createQuery("SELECT   provprop FROM App\Entity\Property prop
                                   JOIN App\Entity\Provider provider 
                                   JOIN App\Entity\ProviderProperty provprop 
                                   WHERE provprop.provider = :provider")
            ->setParameter('provider', $provider)
            ->getResult();
    }

    public function deleteProvider(Provider $provider) {
        $entityManager = $this->getEntityManager();
        $getProvider = $this->findOneBy(["id" => $provider->getId()]);
        if ($getProvider != null) {
            $entityManager->remove($getProvider);
            $entityManager->flush();
            return true;
        }
        return false;
    }
    public function deleteProviderPropsByProvider(Provider $provider) {
        $em = $this->getEntityManager();
        return $em->createQuery("DELETE FROM App\Entity\ProviderProperty provprop WHERE provprop.provider = :provider")
            ->setParameter("provider", $provider)->execute();
    }
    public function deleteProviderCategories(Provider $provider) {
        $em = $this->getEntityManager();
        return $em->createQuery("DELETE FROM App\Entity\ProviderProperty provprop WHERE provprop.provider = :provider")
            ->setParameter("provider", $provider)->execute();
    }

}
