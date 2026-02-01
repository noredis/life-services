<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\User;
use App\Domain\Interfaces\UserRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function existsByEmail(string $email): bool
    {
        $result = $this->createQueryBuilder('u')
            ->select('1')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result !== null;
    }

    public function getByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function getByVerificationToken(string $token): ?User
    {
        return $this->createQueryBuilder('u')
            ->select('u', 'ev')
            ->innerJoin('u.emailVerification', 'ev')
            ->where('ev.token = :token')
            ->setParameter('token', $token)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getByPasswordResetToken(string $token): ?User
    {
        return $this->createQueryBuilder('u')
            ->select('u', 'pr')
            ->innerJoin('u.passwordReset', 'pr')
            ->where('pr.token = :token')
            ->setParameter('token', $token)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function save(User $user): User
    {
        if (!$user->getId()) {
            $this->getEntityManager()->persist($user);
        }

        $this->getEntityManager()->flush();

        return $user;
    }
}
