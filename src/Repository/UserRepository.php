<?php

namespace App\Repository;

use App\Entity\Service;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function createUser(User $user)
    {
        $user->setDateUpdated(new DateTime());
        $user->setDateAdded(new DateTime());
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
        return $user;
    }

    public function updateUser(User $user)
    {
        $user->setDateUpdated(new DateTime());
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
        return $user;
    }

    public function deleteUser(User $user) {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($user);
        $entityManager->flush();
        return $user;
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

    public function findApiTokensByParams(User $user, string $sort,  string $order, int $count)
    {
        $em = $this->getEntityManager();
        return $em->createQuery("SELECT apitok FROM App\Entity\ApiToken apitok
                                   WHERE apitok.user = :user")
            ->setParameter('user', $user)
            ->getResult();
    }

    public function findByEmail(string $email)
    {
        return $this->findOneBy(["email" => $email]);
    }
}
