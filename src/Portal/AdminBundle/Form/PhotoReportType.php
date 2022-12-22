<?php

namespace Portal\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Portal\ContentBundle\Entity\PhotoReport;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class PhotoReportType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('descriptionUk', TextareaType::class, [
                'label' => 'descriptionUk',
                'required' => false
            ])
            ->add('descriptionRu', TextareaType::class, [
                'label' => 'descriptionRu',
                'required' => false
            ])
            ->add('descriptionEn', TextareaType::class, [
                'label' => 'descriptionEn',
                'required' => false
            ])
            ->add('attachments', CollectionType::class, [
                'entry_type' => AttachmentFormForUploadImageType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'prototype' => true,
                'prototype_name' => '__prototype_photo__',
                'by_reference' => false,
                'label' => false,
                'required' => false
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PhotoReport::class,
            'translation_domain' => 'messages'
        ]);

        $resolver->setDefined([
            'validation_groups'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_photo_report';
    }
}
