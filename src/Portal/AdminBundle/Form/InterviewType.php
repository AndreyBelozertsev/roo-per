<?php

namespace Portal\AdminBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Portal\ContentBundle\Entity\Interview;

class InterviewType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'interview_form.title',
                'required' => false,
            ])
            ->add('subTitle', TextType::class, [
                'label' => 'interview_form.subtitle',
                'required' => false,
            ])
            ->add('menuNode', EntityType::class, [
                'label' => 'document_form.bloc',
                'class' => 'PortalContentBundle:MenuNode',
                'choice_label' => 'title',
                'required' => false,
                'placeholder' => 'placeholder_select',
                'choices' => $options['listStructure'],
                'em' => 'customer'
            ])
            ->add('dateStart', DateType::class, [
                'label' => 'interview_form.date_start',
                'widget' => 'single_text',
//                'data' => new \DateTime(),
                'required' => false,
            ])
            ->add('dateEnd', DateType::class, [
                'label' => 'interview_form.date_end',
                'widget' => 'single_text',
//                'data' => new \DateTime(),
                'required' => false,
            ])
            ->add('isPublished', CheckboxType::class, [
                'label' => 'interview_form.is_published',
                'required' => false,
            ])
            ->add('isRegisteredUser', CheckboxType::class, [
                'label' => 'interview_form.is_registered_user',
                'required' => false,
            ])
            ->add('isViewResult', CheckboxType::class, [
                'label' => 'interview_form.is_view_result',
                'required' => false,
            ])
            ->add('messageEnd', TextareaType::class, [
                'label' => 'interview_form.message_end',
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'interview_form.description',
                'required' => false,
            ])
            ->add('questions', CollectionType::class, [
                'entry_type' => InterviewQuestionType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                'required' => false,
                'label' => false,
                'error_bubbling' => false,
//                    'em' => 'customer',
            ])
            ->add('addQuestion', ButtonType::class, [
                'label' => 'interview_form.btn_add_question',
            ])
            ->add('messageNotRegisteredUser', TextareaType::class, [
                'label' => 'interview_form.message_not_user',
                'required' => false,
                'data' => $options['message_not_user'],
            ]);

        if (isset($options['slug']) && $options['slug']) {
            $builder->add('slug', TextType::class, [
                'label' => 'interview_form.slug',
                'required' => false,
            ]);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Interview::class,
            'translation_domain' => 'messages',
        ]);

        $resolver->setDefined([
            'listStructure',
            'validation_groups',
            'slug',
            'message_not_user',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_interview';
    }
}
