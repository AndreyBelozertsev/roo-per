<?php

namespace Portal\AdminBundle\Form;

use Portal\ContentBundle\Entity\Attachment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Portal\ContentBundle\Entity\EventAttachment;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventAttachmentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', VichImageType::class, array(
                'required' => false,
                'label' => false,
                'attr' => ['accept' => 'image/jpeg,image/png', 'class' => 'load-img-button',],
                'allow_delete' => false,
            ))
            ->add('cropStartX', HiddenType::class, array(
                    'required' => false,
                )
            )
            ->add('cropStartY', HiddenType::class, array(
                    'required' => false,
                )
            )
            ->add('cropWidth', HiddenType::class, array(
                    'required' => false,
                )
            )
            ->add('cropHeight', HiddenType::class, array(
                    'required' => false,
                )
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EventAttachment::class,
            'translation_domain' => 'messages'
        ]);
    }
}
