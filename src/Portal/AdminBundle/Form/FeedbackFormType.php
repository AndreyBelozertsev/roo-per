<?php

namespace Portal\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use Portal\ContentBundle\Entity\Menu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Portal\ContentBundle\Entity\FeedbackForm;

class FeedbackFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titleUk', TextType::class, array(
                    'label' => 'title_uk',
                    'required' => false,
                )
            )
            ->add('titleRu', TextType::class, array(
                    'label' => 'title_ru',
                    'required' => false,
                )
            )
            ->add('titleEn', TextType::class, array(
                    'label' => 'title_en',
                    'required' => false,
                )
            )
            ->add('emailResponsible', TextType::class, array(
                    'label' => 'feedback_form.resp-email',
                    'required' => false,
                )
            )
//            ->add('menuNode', EntityType::class, array(
//                    'label' => 'article_form.bloc',
//                    'class' => 'PortalContentBundle:MenuNode',
//                    'choice_label' => 'title',
//                    'placeholder' => 'placeholder_select',
//                    'required' => false,
//                    'attr' => array('class' => 'form-control'),
//                    'query_builder' => function (EntityRepository $er) {
//                        return $er->createQueryBuilder('m_n')->join('m_n.menu','m')
//                            ->where('m.code = :code')
//                            ->andWhere('m_n.isDeleted = false')
//                            ->andWhere('m_n.isPublished = true')
//                            ->setParameter('code', Menu::STRUCTURE_MENU);
//                    },
//                    'em' => 'customer'
//                )
//            )
            ->add('descriptionUk', TextareaType::class, array(
                    'label' => 'descriptionUk',
                    'required' => false,
                )
            )
            ->add('descriptionRu', TextareaType::class, array(
                    'label' => 'descriptionRu',
                    'required' => false,
                )
            )
            ->add('descriptionEn', TextareaType::class, array(
                    'label' => 'descriptionEn',
                    'required' => false,
                )
            )
//            ->add('messageSuccess', TextType::class, array(
//                    'label' => 'feedback_form.form_message_success',
//                    'required' => false,
//                    'data' => $options['message_success'],
//                )
//            )
//            ->add('messageError', TextType::class, array(
//                    'label' => 'feedback_form.form_message_error',
//                    'required' => false,
//                    'data' => $options['message_error'],
//                )
//            )
//            ->add('isPublished', CheckboxType::class, array(
//                'label' => 'feedback_form.is_published',
//                'required' => false,
//            ))
//            ->add('isRegisteredUser', CheckboxType::class, array(
//                'label' => 'feedback_form.is_registered_user',
//                'required' => false,
//            ))
            ->add('isAgreePersonalData', CheckboxType::class, array(
                'label' => 'feedback_form.is_agree_personal_data',
                'required' => false,
            ))
            ->add('visible', ChoiceType::class, array(
                'label' => 'feedback_form.visible',
                'multiple' => true,
                'choices'  => FeedbackForm::FIELD_FOR_SELECT,
                'data' => $options['visible_data'],
                'mapped' => false,
                'required' => false,
            ))
        ;
//        if (isset($options['slug']) && $options['slug']) {
//            $builder->add('slug', TextType::class, [
//                'label' => 'feedback_form.slug',
//                'required' => false,
//            ])
//            ;
//        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => FeedbackForm::class,
            'translation_domain' => 'messages',
        ));
        $resolver->setDefined(array(
            'visible_data',
            'message_success',
            'message_error',
            'validation_groups',
            'slug',
        ));

    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_feedbackform';
    }

}
