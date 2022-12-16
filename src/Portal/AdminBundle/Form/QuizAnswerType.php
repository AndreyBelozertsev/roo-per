<?php

namespace Portal\AdminBundle\Form;

use Portal\ContentBundle\Entity\QuizAnswer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizAnswerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextType::class, array(
                    'label' => 'quiz_answer_form.content',
                    'required' => false
                )
            )
            ->add('isCorrect', RadioType::class, array(
                    'label' => 'quiz_answer_form.is_correct',
                    'required' => false,
                    'attr' => array(
                        'class' => 'correct-answer',
                    ),
                )
            )
//            ->add('removeAnswer', ButtonType::class, array(
//                    'label' => 'quiz_answer_form.btn_remove_answer',
//                    'attr' => array(
//                        'class' => 'remove-answer btn btn-warning btn-xs',
//                    ),
//
//                )
//            )
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => QuizAnswer::class,
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
        return 'admin_quiz_answer';
    }

}
