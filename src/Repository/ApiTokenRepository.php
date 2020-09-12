<?php

namespace App\Repository;

use App\Entity\ApiToken;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ApiToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiToken[]    findAll()
 * @method ApiToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiToken::class);
    }

    public function setToken(User $user) {
        try {
            $apiToken = new ApiToken($user);
            $this->getEntityManager()->persist($apiToken);
            $this->getEntityManager()->flush();
            return $apiToken;
        } catch (ORMException $e) {
            throw new ORMException("ORM Exception... " . $e->getMessage());
        }
    }

    public function getLatestToken(User $user) {
        $apiToken = $this->createQueryBuilder("api_token")
            ->select("api_token")
            ->where("api_token.user = :user")
            ->andWhere("api_token.expiresAt > :currentDate")
            ->setParameter("user", $user)
            ->setParameter("currentDate", new \DateTime())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        if ($apiToken === null) {
            return $this->setToken($user);
        }
        return $apiToken;
    }

    public function updateTokenExpiry(ApiToken $apiToken, $expiryDate) {
        try {
            $apiToken->setExpiresAt($expiryDate);
            $this->getEntityManager()->persist($apiToken);
            $this->getEntityManager()->flush();
            return $apiToken;
        } catch (ORMException $e) {
            throw new ORMException("ORM Exception... " . $e->getMessage());
        }

    }

    public function deleteApiToken(ApiToken $apiToken) {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($apiToken);
        $entityManager->flush();
        return $apiToken;
    }
}
