<?php

namespace Portal\ContentBundle\Form;

//use Captcha\Bundle\CaptchaBundle\Form\Type\CaptchaType;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Portal\ContentBundle\Entity\Typo;

class TypoReportFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', HiddenType::class)
            ->add('typo', HiddenType::class)
            ->add('comment', TextareaType::class, [
                'label' => 'comment',
                'attr' => ['maxlength' => 255],
                'required' => false
            ])
//            ->add('captchaCode', CaptchaType::class, [
//                'captchaConfig' => 'TypoCaptcha',
//            ])
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
            'data_class' => Typo::class
        ]);
    }
}
