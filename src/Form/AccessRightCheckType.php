<?php

namespace App\Form;

use App\Entity\AccessRight;
use App\Entity\Interface\HasAccessRights;
use App\Service\AccessRightService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AccessRightCheckType extends AbstractType
{
    public function __construct(
        protected AccessRightService $accessRightService
    ) { }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $permissions = $this->accessRightService->getPermissionsList();

        $functionsList = $builder->getData()['module'] ? $permissions[$builder->getData()['module']] : [];

        $builder
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
                'choices' => $functionsList,
                'disabled' => empty($functionsList),
                'choice_label' => function(?string $type) {
                    return ucfirst($type);
                },
                'choice_attr' => function(?string $type) {
                    return ['class' => 'function_'.strtolower($type)];
                },
            ]);
    }
}
