<?php

namespace Portal\AdminBundle\Form;

use Portal\ContentBundle\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class ArticleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
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
            ->add('attachment', ArticleAttachmentType::class, [
                'label' => false
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
            'data_class' => Article::class,
            'translation_domain' => 'messages'
        ]);
//        $resolver->setDefined([
//            'permissions',
//            'isSuperAdmin',
//            'slug',
//            'validation_groups'
//        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_article';
    }
}
