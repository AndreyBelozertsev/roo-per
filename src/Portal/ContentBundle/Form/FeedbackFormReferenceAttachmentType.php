<?php

namespace Portal\ContentBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Portal\ContentBundle\Entity\FeedbackFormAttachment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeedbackFormReferenceAttachmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference', TextType::class, [
                'required' => false,
                'label' => 'feedback_form_value.reference',
                'attr' => ['class' => 'page-form__pole link-res', 'placeholder' => 'feedback_form_value.reference_placeholder'],
                'label_attr' => ['class' => 'page-form__label _padding-left'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FeedbackFormAttachment::class,
        ]);
    }
}
