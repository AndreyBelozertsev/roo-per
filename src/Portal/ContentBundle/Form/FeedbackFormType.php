<?php

namespace Portal\ContentBundle\Form;

//use Captcha\Bundle\CaptchaBundle\Form\Type\CaptchaType;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Portal\ContentBundle\Entity\FeedbackForm;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FeedbackFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('emailResponsible', TextType::class)
            ->add('captcha', CaptchaType::class, [
                'label' => 'captcha',
                'reload' => true,
                'as_url' => true,
                'width' => 200,
                'height' => 50,
                'length' => 6,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'sand'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FeedbackForm::class
        ]);
    }
}
