<?php

namespace Portal\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Portal\ContentBundle\Entity\SocialNetwork;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class SocialNetworkType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'social_network.name',
            ])
            ->add('link', TextType::class, [
                'label' => 'social_network.link',
            ])
            ->add('sort', IntegerType::class, [
                'label' => 'sort',
                'required' => false
            ])
            ->add('prefix', TextType::class, [
                'label' => 'social_network.prefix'
            ])
            ->add('isPublished', CheckboxType::class, [
                'label' => 'to_public',
                'required' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SocialNetwork::class,
            'translation_domain' => 'messages'
        ]);
        $resolver->setDefined([
            'permissions',
            'isSuperAdmin',
            'slug',
            'validation_groups'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_socialnetwork';
    }
}
