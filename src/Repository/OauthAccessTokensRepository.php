<?php

namespace App\Repository;

use App\Entity\OauthAccessTokens;
use App\Entity\Provider;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OauthAccessTokens|null find($id, $lockMode = null, $lockVersion = null)
 * @method OauthAccessTokens|null findOneBy(array $criteria, array $orderBy = null)
 * @method OauthAccessTokens[]    findAll()
 * @method OauthAccessTokens[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OauthAccessTokensRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OauthAccessTokens::class);
    }
    public function saveOathToken(OauthAccessTokens $oathToken, Provider $provider) {
        $oathToken->setDateAdded(new DateTime());
        $provider->addOauthAccessToken($oathToken);
        $this->getEntityManager()->persist($oathToken);
        $this->getEntityManager()->flush();
        return $oathToken;
    }


    public function getLatestAccessToken(Provider $provider) {
        $dateTime = new DateTime();
        return $this->createQueryBuilder('a')
            ->Where('a.provider = :provider')
            ->andWhere('a.expiry > :currentTime')
            ->setParameter("provider", $provider)
            ->setParameter("currentTime", $dateTime)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
