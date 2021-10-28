<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Group;
use App\Entity\AccessRight;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Interface\HasAccessRights;
use App\Repository\AccessRightRepository;
use Symfony\Component\HttpKernel\KernelInterface;

class AccessRightService
{
    public const PERMISSIONS_NAMESPACE = 'App\\Module\\';
    public const PERMISSIONS_FOLDER = '/src/Module';

    public function __construct(
        protected AccessRightRepository $accessRightRepository,
        protected EntityManagerInterface $entityManager,
        protected KernelInterface $appKernel
    ) { }

    public function checkUserHasAccess(
        User $user,
        string $module,
        string $function
    ): bool {
        return null !== $this->accessRightRepository->hasUserAccess(
            [
                'user_type' => $this->getAuthorizedEntityName($user),
                'user_id' => $user->getId(),
                'group_type' => $this->getAuthorizedEntityName($user->getGroup()),
                'group_id' => $user->getGroup()->getId()
            ],
            $module,
            $function
        );
    }

    public function getRightsByOwner(HasAccessRights $owner)
    {
        return $this->accessRightRepository->getRightsByOwner(
            $this->getAuthorizedEntityName($owner),
            $owner->getId()
        );
    }

    public function getPermissionsList()
    {
        $modules = array_filter(
            scandir(
                $this->appKernel->getProjectDir() .
                self::PERMISSIONS_FOLDER
            ),
            function ($file) {
                return strpos($file, '.php') > 0;
            }
        );

        $list = [];

        foreach ($modules as $moduleClass) {
            $moduleName = str_replace(
                '.php',
                '',
                $moduleClass
            );

            $list[$moduleName] = get_class_methods(
                self::PERMISSIONS_NAMESPACE .
                $moduleName
            );
        }

        return $list;
    }

    public function getAuthorizedEntitiesList()
    {
        return [
            'User' => User::class,
            'Group' => Group::class
        ];
    }

    public function grantPermissionToEntity(
        HasAccessRights $entity,
        string $module,
        string $function
    ) {
        # code...
    }

    public function getRightOwner(
        AccessRight $right,
        string $module,
        string $function
    ) {
        # code...
    }

    protected function getAuthorizedEntityName(HasAccessRights $owner) : string
    {
        return $this->entityManager
            ->getClassMetadata($owner::class)
            ->name;
    }
}
