<?php

namespace Portal\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class UserFormType extends AbstractType
{    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName', TextType::class, array(
                'label' => 'users_form.lastname',
                'required' => false,
            ))
            ->add('firstName', TextType::class, array(
                'label' => 'users_form.firstname',
                'required' => false,
            ))
            ->add('middleName', TextType::class, array(
                'label' => 'users_form.middlename',
                'required' => false,
            ))
            ->add('email', EmailType::class, array(
                'label' => 'users_form.email',
                'required' => false,
            ))
            ->add('phone', TextType::class, array(
                'label' => 'users_form.phone',
                'required' => false,
            ))
            ->add('username', TextType::class, array(
                'label' => 'users_form.username',
                'required' => false,
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'users_form.save'
            ))
            ->add('userRoles',  CollectionType::class, array(
                    'entry_type' => RoleToUserFormType::class,
                    'entry_options' => array('label' => false),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'by_reference' => false,
                    'required' => false,
                    'label' => false,
                )
            )
        ;
        if (isset($options['newUser']) && $options['newUser']) {
            $builder
                ->add('password', RepeatedType::class, array(
                    'type' => 'password',
                    'options' => array('translation_domain' => 'messages'),
                    'first_options' => array(
                        'label' => 'users_form.new_password',
                        'attr' => array(
                            'class' => 'form-control',
                            'label_attr' => "col-sm-2 control-label"
                        )
                    ),
                    'second_options' => array(
                        'label' => 'users_form.repeat_password',
                        'required' => true,
                        'attr' => array(
                            'class' => 'form-control',
                            'label_attr' => "col-sm-2 control-label"
                        )
                    ),
                    'invalid_message' => 'users_form.repeat_password_error',
                    'required' => false
                ));
        }
    }

    public function getName()
    {
        return 'UserFormType';
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'newUser' => false,
        ));
        $resolver->setDefined(array(
            'newUser', 
            'instanceList',
        ));
    }

}
