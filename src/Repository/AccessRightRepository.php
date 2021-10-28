<?php

namespace App\Repository;

use App\Entity\AccessRight;
use App\Entity\Interface\HasAccessRights;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AccessRight|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccessRight|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccessRight[]    findAll()
 * @method AccessRight[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccessRightRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccessRight::class);
    }

    public function getRightsByOwner(
        string $ownerType,
        int $ownerId
    ): ?array {
        return $this->createQueryBuilder('ar')
            ->andWhere('ar.owner_type = :owner_type')
            ->andWhere('ar.owner_id = :owner_id')
            ->setParameters([
                'owner_type' => $ownerType,
                'owner_id' => $ownerId
            ])
            ->getQuery()
            ->getResult()
        ;
    }

    public function hasUserAccess(
        array $ownerParams,
        string $module,
        string $function
    ): bool {
        $record = $this->createQueryBuilder('ar')
        ->where(
            '(' .
                '(' .
                    'ar.owner_type = :user_type AND ' .
                    'ar.owner_id = :user_id ' .
                ') OR ' .
                '(' .
                    'ar.owner_type = :group_type AND ' .
                    'ar.owner_id = :group_id ' .
                ')' .
            ') AND ' .
            'ar.module = :module AND ' .
            '(' .
                'ar.function = :function OR ' .
                'ar.function = :wildcard' .
            ')'
        )
        ->setParameters([
            'user_type' => $ownerParams['user_type'],
            'user_id' => $ownerParams['user_id'],
            'group_type' => $ownerParams['group_type'],
            'group_id' => $ownerParams['group_id'],
            'module' => $module,
            'function' => $function,
            'wildcard' => AccessRight::FUNCTION_WILDCARD
        ])
        ->getQuery()
        ->getOneOrNullResult();

        return $record !== null;
    }
}
