<?php

namespace Portal\AdminBundle\Form;

use Portal\AdminBundle\Entity\Instance;
use Portal\UserBundle\Entity\Role;
use Portal\UserBundle\Entity\RoleToUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RoleToUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('role', EntityType::class, [
                'class' => Role::class,
                'label' => 'users_form.role',
                'required' => false,
            ])
            ->add('instance', EntityType::class, [
                'class' => Instance::class,
                'label' => 'users_form.instance',
                'required' => false,
            ])
            ->add('removeRole', ButtonType::class, array(
                'label' => 'users_form.btn_remove_role',
                'attr' => array(
                    'class' => 'remove-question',
                ),
            ))
            ->add('id', HiddenType::class, array(
                'label' => false,
                'attr' => array(
                    'class' => 'roleToUserId',
                ),
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RoleToUser::class,
            'translation_domain' => 'messages',
        ]);
        $resolver->setDefined(array(
            'cascade_validation' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_role2user';
    }
}
