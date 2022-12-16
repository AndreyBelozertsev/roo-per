<?php

namespace Portal\AdminBundle\Form;

use Portal\ContentBundle\Entity\PhotoReportAttachment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AttachmentFormForUploadImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', VichImageType::class, [
                'label' => false,
                'attr' => [
                    'accept' => 'image/jpeg,image/png',
                    'class' => 'load-img-button'
                ],
                'allow_delete' => false,
                'required' => false
            ])
            ->add('fileDescriptionUk', TextType::class, [
                'label' => 'descriptionUk',
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('fileDescriptionRu', TextType::class, [
                'label' => 'descriptionRu',
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('fileDescriptionEn', TextType::class, [
                'label' => 'descriptionEn',
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('isDeleted', CheckboxType::class, [
                'label' => 'delete',
                'attr' => ['class' => 'disabled-chk'],
                'label_attr' => ['class' => 'disabled-chk'],
                'required' => false
            ])
            ->add('cropStartX', HiddenType::class, [
                'required' => false
            ])
            ->add('cropStartY', HiddenType::class, [
                'required' => false
            ])
            ->add('cropWidth', HiddenType::class, [
                'required' => false
            ])
            ->add('cropHeight', HiddenType::class, [
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PhotoReportAttachment::class
        ]);
    }
}
