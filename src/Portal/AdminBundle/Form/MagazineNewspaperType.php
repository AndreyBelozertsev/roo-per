<?php

namespace Portal\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Portal\ContentBundle\Entity\MagazineNewspaper;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


class MagazineNewspaperType extends AbstractType
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
            ->add('attachment', MagazineNewspaperAttachmentType::class, [
                'label' => false
            ])
            ->add('document_attachment', MagazineNewspaperDocumentAttachmentType::class, [
                'label' => false
            ])
            ->add('type_of', ChoiceType::class, array(
                'choices' => array(
                    'журнал' => 'magazine',
                    'газета' => 'newspaper'
                )
            ))
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
            'data_class' => MagazineNewspaper::class
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'portal_contentbundle_magazine_newspaper';
    }
}
