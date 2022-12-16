<?php

namespace Portal\AdminBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Portal\ContentBundle\Entity\ContactTemplate;
use Portal\ContentBundle\Entity\Page;

class ContactTemplateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pageId', EntityType::class, [
                'class' => Page::class,
                'attr'=> ['style'=>'display:none'],
                'em' => 'customer'
            ])
            ->add('subtitle', TextType::class, [
                'label' => 'subtitle',
                'required' => false
            ])
            ->add('address', TextType::class, [
                'label' => 'template.address',
                'required' => false
            ])
            ->add('contentTable', HiddenType::class, [
                'label' => 'template.contact'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'save'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContactTemplate::class,
            'translation_domain' => 'messages'
        ]);
        $resolver->setDefined([
            'validation_groups'
        ]);
    }
}
