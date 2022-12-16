<?php

namespace Portal\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use Portal\ContentBundle\Entity\Menu;
use Portal\ContentBundle\Entity\Quiz;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class QuizType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array(
                    'label' => 'quiz_form.title',
                    'required' => false,
                )
            )
            ->add('subTitle', TextType::class, array(
                    'label' => 'quiz_form.subtitle',
                    'required' => false,
                )
            )
            ->add('menuNode', EntityType::class, [
                'label' => 'document_form.bloc',
                'class' => 'PortalContentBundle:MenuNode',
                'choice_label' => 'title',
                'required' => false,
                'placeholder' => 'placeholder_select',
                'choices' => $options['listStructure'],
                'em' => 'customer'
            ])
            ->add('dateStart', DateType::class, array(
                    'label' => 'quiz_form.date_start',
                    'widget' => 'single_text',
                    'required' => false,
                )
            )
            ->add('dateEnd', DateType::class, array (
                    'label' => 'quiz_form.date_end',
                    'widget' => 'single_text',
                    'required' => false,
                )
            )
            ->add('goodResult', TextType::class, array(
                    'label' => 'quiz_form.good_result',
                    'required' => false,
                )
            )
            ->add('badResult', TextType::class, array(
                    'label' => 'quiz_form.bad_result',
                    'required' => false,
                )
            )
            ->add('criterion', IntegerType::class, array(
                    'label' => 'quiz_form.criterion',
                    'required' => false,
                )
            )
            ->add('isPublished', CheckboxType::class, array(
                    'label' => 'quiz_form.is_published',
                    'required' => false,
                )
            )
            ->add('questions',  CollectionType::class, array(
                    'entry_type' => QuizQuestionType::class,
                    'entry_options' => array('label' => false),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'by_reference' => false,
                    'required' => false,
                    'error_bubbling' => false,
                    'label' => false,
                )
            )
            ->add('addQuestion', ButtonType::class, array(
                    'label' => 'quiz_form.btn_add_question',
                )
            )
        ;
        if (isset($options['slug']) && $options['slug']) {
            $builder->add('slug', TextType::class, [
                'label' => 'quiz_form.slug',
                'required' => false,
            ])
            ;
        }

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Quiz::class,
            'translation_domain' => 'messages',
        ));
        $resolver->setDefined(array(
            'listStructure',
            'validation_groups',
            'slug',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_quiz';
    }

}
