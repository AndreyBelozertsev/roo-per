<?php

namespace Portal\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Portal\ContentBundle\Entity\ArticleCategory;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Portal\AdminBundle\Form\ArticleCategoryIconAttachmentType;
use Portal\AdminBundle\Form\ArticleCategoryThumbnailAttachmentType;


class ArticleCategoryType extends AbstractType
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
            ->add('icon_attachment', ArticleCategoryIconAttachmentType::class, [
                'label' => false
            ])
            ->add('thumbnail_attachment', ArticleCategoryThumbnailAttachmentType::class, [
                'label' => false
            ])
            ->add('show_in_menu', CheckboxType::class, [
                'label' => 'to_public',
                'required' => false,
            ])
            ->add('sort', IntegerType::class, [
                'label' => 'sort',
                'required' => false
            ])
            ->add('isPublished');
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ArticleCategory::class,
            'translation_domain' => 'messages'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_article_category';
    }
}
