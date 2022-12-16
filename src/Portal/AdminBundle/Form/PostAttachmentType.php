<?php

namespace Portal\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Portal\ContentBundle\Entity\PostAttachment;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PostAttachmentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', VichImageType::class, [
                'label' => false,
                'attr' => [
                    'accept' => 'image/jpeg,image/png',
                    'class' => 'load-img-button'
                ],
                'required' => false,
                'allow_delete' => false,
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
            'data_class' => PostAttachment::class,
            'translation_domain' => 'messages'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_post_attachment';
    }
}
