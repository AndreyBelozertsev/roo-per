<?php

namespace Portal\AdminBundle\Form;

use Portal\ContentBundle\Entity\Feedback;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class FeedbackType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contentUk', TextareaType::class, [
                'label' => 'content_uk',
            ])
            ->add('contentRu', TextareaType::class, [
                'label' => 'content_ru',
            ])
            ->add('contentEn', TextareaType::class, [
                'label' => 'content_en',
            ])
            ->add('isFormShown', CheckboxType::class, [
                'label' => 'feedback.form_shown',
                'required' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Feedback::class,
            'translation_domain' => 'messages'
        ]);
        $resolver->setDefined([
            'permissions',
            'slug',
            'validation_groups'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_feedback';
    }
}
