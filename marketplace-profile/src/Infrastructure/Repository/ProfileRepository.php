<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Profile;
use App\Domain\Interfaces\ProfileRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProfileRepository extends ServiceEntityRepository implements ProfileRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Profile::class);
    }

    public function getByEmail(string $email): ?Profile
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function save(Profile $profile): Profile
    {
        if (!$profile->getId()) {
            $this->getEntityManager()->persist($profile);
        }
        $this->getEntityManager()->flush();

        return $profile;
    }
}
