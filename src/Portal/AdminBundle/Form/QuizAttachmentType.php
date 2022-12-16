<?php

namespace Portal\AdminBundle\Form;

use Portal\ContentBundle\Entity\Attachment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Portal\ContentBundle\Entity\DocumentAttachment;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class QuizAttachmentType extends AbstractType
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
                'required' => false
            ])
//            ->add('fileDescription', TextType::class, [
//                'label' => 'photo_report_form.description',
//                'attr' => [
//                    'class' => 'form-control'
//                ],
//                'required' => true
//            ])
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Attachment::class
        ]);
    }
}
