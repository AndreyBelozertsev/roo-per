<?php

namespace Portal\ContentBundle\Form;

use Portal\ContentBundle\Entity\ArticleSubscribe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;


class ArticleSubscribeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => ['placeholder' => 'Email'],
                'required' => false
            ])
            ->add('instance', HiddenType::class, [
                'data' => $options['instanceId']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ArticleSubscribe::class,
            'translation_domain' => 'messages',
            'instanceId' => null
        ]);
    }
}
