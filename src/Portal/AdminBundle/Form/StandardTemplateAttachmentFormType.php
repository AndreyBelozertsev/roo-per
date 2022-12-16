<?php

namespace Portal\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Portal\ContentBundle\Entity\StandardTemplateAttachment;
use Vich\UploaderBundle\Form\Type\VichImageType;

class StandardTemplateAttachmentFormType extends AbstractType
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
            ->add('cropStartX', HiddenType::class)
            ->add('cropStartY', HiddenType::class)
            ->add('cropWidth', HiddenType::class)
            ->add('cropHeight', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => StandardTemplateAttachment::class,
            'translation_domain' => 'messages'
        ]);
    }

    public function getBlockPrefix()
    {
        return 'admin_standard_template_attachment';
    }
}
