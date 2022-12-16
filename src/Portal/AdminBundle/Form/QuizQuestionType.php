<?php

namespace Portal\AdminBundle\Form;

use Portal\ContentBundle\Entity\QuizQuestion;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizQuestionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextType::class, array(
                    'label' => 'quiz_question_form.content',
                    'required' => false,
                    'attr' => array('class' => 'form-control'),
                    'label_attr' => array(
                        'class' => 'col-sm-2 control-label',
                    ),
                )
            )
            ->add('answers', CollectionType::class, array(
                    'entry_type' => QuizAnswerType::class,
                    'entry_options' => array('label' => false),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'prototype_name' => '__prot_answer__',
                    'by_reference' => false,
                    'required' => false,
                    'error_bubbling' => false,
                    'label' => false,
                )
            )
            ->add('addAnswer', ButtonType::class, array(
                    'label' => 'quiz_question_form.btn_add_answer',
                    'attr' => array(
                        'class' => 'add_answer_link btn btn-sm',
                    ),

                )
            )
            ->add('removeQuestion', ButtonType::class, array(
                    'label' => 'quiz_question_form.btn_remove_question',
                    'attr' => array(
                        'class' => 'remove-question btn btn-danger btn-sm',
                    ),

                )
            )
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => QuizQuestion::class,
            'translation_domain' => 'messages',
        ));
        $resolver->setDefined(array(
            'cascade_validation' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_quiz_question';
    }

}
