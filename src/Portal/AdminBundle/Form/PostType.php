<?php

namespace Portal\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Portal\ContentBundle\Entity\Post;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titleUk', TextType::class, [
                'label' => 'title_uk',
                'required' => false
            ])
            ->add('titleRu', TextType::class, [
                'label' => 'title_ru',
                'required' => false
            ])
            ->add('titleEn', TextType::class, [
                'label' => 'title_en',
                'required' => false
            ])
            ->add('userNameUk', TextType::class, [
                'label' => 'blogger_name_uk',
                'required' => false
            ])
            ->add('userNameRu', TextType::class, [
                'label' => 'blogger_name_ru',
                'required' => false
            ])
            ->add('userNameEn', TextType::class, [
                'label' => 'blogger_name_en',
                'required' => false
            ])
            ->add('userPositionUk', TextType::class, [
                'label' => 'blogger_position_uk',
                'required' => false
            ])
            ->add('userPositionRu', TextType::class, [
                'label' => 'blogger_position_ru',
                'required' => false
            ])
            ->add('userPositionEn', TextType::class, [
                'label' => 'blogger_position_en',
                'required' => false
            ])
            ->add('attachment', PostAttachmentType::class, [
                'label' => false
            ])
            ->add('contentUk', TextareaType::class, [
                'label' => 'content_uk',
                'required' => false
            ])
            ->add('contentRu', TextareaType::class, [
                'label' => 'content_ru',
                'required' => false
            ])
            ->add('contentEn', TextareaType::class, [
                'label' => 'content_en',
                'required' => false
            ])
            ->add('isPublished', CheckboxType::class, [
                'label' => 'to_public',
                'required' => false
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'portal_contentbundle_post';
    }
}
