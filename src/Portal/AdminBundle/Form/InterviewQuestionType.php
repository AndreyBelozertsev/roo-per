<?php

namespace Portal\AdminBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Portal\ContentBundle\Entity\InterviewQuestion;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class InterviewQuestionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextType::class, array(
                    'label' => 'interview_question_form.content',
                    'required' => false,
                    'attr' => array('class' => 'form-control question-content'),
                    'label_attr' => array(
                        'class' => 'col-sm-2 control-label',
                    ),
                )
            )
            ->add('description', TextareaType::class, array(
                    'label' => 'interview_form.description',
                    'required' => false,
                )
            )
            ->add('isRequired', CheckboxType::class, array(
                    'label' => 'interview_question_form.is_required',
                    'required' => false,
                    'attr' => array('class' => 'form-control question-required-checked'),
                    'label_attr' => array(
                        'class' => 'col-sm-2 control-label',
                    ),
                )
            )
            ->add('questionType', ChoiceType::class, array(
                'label' => 'interview_question_form.question_type',
                'multiple' => false,
                'attr' => array(
                    'class' => 'select-question-type',
                ),
                'choices'  => InterviewQuestion::QUESTION_TYPE_LIST,
                'placeholder' => false,
                'required' => false,
            ))
            ->add('answers', CollectionType::class, array(
                    'entry_type' => InterviewAnswerType::class,
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
            ->add('isDependent', CheckboxType::class, array(
                    'label' => 'interview_question_form.is_dependent',
                    'required' => false,
                    'attr' => array('class' => 'form-control question-dependent-checked'),
                    'label_attr' => array(
                        'class' => 'col-sm-2 control-label question-dependent-label',
                    ),
                    'value' => 0,
                    'mapped' => false,
                )
            )
            ->add('dependentInterviewAnswerId', HiddenType::class, array(
                    'attr' => array('class' => 'question-answer-dependent-id'),
                    'required' => false,
                )
            )
            ->add('addAnswer', ButtonType::class, array(
                    'label' => 'interview_question_form.btn_add_answer',
                        'attr' => array(
                            'class' => 'add_answer_link btn btn-sm',
                        ),

                )
            )
            ->add('removeQuestion', ButtonType::class, array(
                    'label' => 'interview_question_form.btn_remove_question',
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
            'data_class' => InterviewQuestion::class,
            'translation_domain' => 'messages',
        ));
        $resolver->setDefined(array(
            'validation_groups',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_interview_question';
    }

}
