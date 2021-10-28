<?php

namespace App\Form;

use App\Entity\AccessRight;
use App\Entity\Interface\HasAccessRights;
use App\Service\AccessRightService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AccessRightType extends AbstractType
{
    public function __construct(
        protected AccessRightService $accessRightService,
        protected EntityManagerInterface $entityManager
    ) { }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $permissions = $this->accessRightService->getPermissionsList();

        $functionsList = $builder->getData()->getModule() ? $permissions[$builder->getData()->getModule()] : [];

        $ownersList = $builder->getData()->getOwnerType() ? $this->entityManager->getRepository(
            $builder->getData()->getOwnerType()
        )->findAll() : [];

        $builder
            ->add('owner_type', ChoiceType::class, [
                'choices' => array_merge(
                    ['' => 'Select Owner Type'],
                    $this->accessRightService->getAuthorizedEntitiesList()
                ),
                'choice_label' => function(string $type, string $key) {
                    return ucfirst($key);
                },
                'choice_attr' => function(string $type, string $key) {
                    return ['class' => 'type_'.strtolower($type)];
                },
            ])
            ->add('owner', ChoiceType::class, [
                'choices' => $ownersList,
                'disabled' => empty($ownersList),
                'choice_value' => 'id',
                'choice_label' => function(?HasAccessRights $entity) {
                    return $entity ? strtoupper($entity->getAccessRightOwnerName()) : '';
                },
                'choice_attr' => function(?HasAccessRights $entity) {
                    return $entity ? ['class' => 'entity_'.strtolower($entity->getId())] : [];
                },
            ])
            ->add('module', ChoiceType::class, [
                'choices' => array_merge([''], array_keys($permissions)),
                'choice_label' => function(string $module) {
                    return $module;
                },
                'choice_attr' => function(string $module) {
                    return ['class' => 'module_'.strtolower($module)];
                },
            ])
            ->add('function', ChoiceType::class, [
                'choices' => (!empty($functionsList)
                    ? array_merge(
                        [AccessRight::FUNCTION_WILDCARD],
                        $functionsList
                    )
                    : []
                ),
                'disabled' => empty($functionsList),
                'choice_label' => function(?string $type) {
                    return $type === AccessRight::FUNCTION_WILDCARD
                        ? 'All Functions'
                        : ucfirst($type);
                },
                'choice_attr' => function(?string $type) {
                    return ['class' => 'function_'.strtolower($type)];
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AccessRight::class,
        ]);
    }
}
