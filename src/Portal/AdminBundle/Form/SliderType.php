<?php

namespace Portal\AdminBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use \Portal\ContentBundle\Entity\Slider;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SliderType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'slider.title',
                'required' => false,
            ])
            ->add('type', IntegerType::class, [
                'label' => 'slider.type',
                'required' => false,
            ])
            ->add('slideDuration', IntegerType::class, [
                'label' => 'slider.slide_duration',
                'required' => false,
            ])
            ->add('isPublished', CheckboxType::class, [
                'label' => 'to_public',
                'required' => false,
            ])
            ->add('frontendClass', ChoiceType::class, [
                'label' => 'slider.frontend_class',
                'choices' => Slider::$CLASS_LIST_SLIDER,
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'save'
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Slider::class,
            'translation_domain' => 'messages',
        ]);

        $resolver->setDefined([
            'validation_groups',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_slider';
    }
}
