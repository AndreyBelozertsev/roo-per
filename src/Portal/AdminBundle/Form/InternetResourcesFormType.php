<?php

namespace Portal\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Portal\ContentBundle\Entity\InternetResources;

class InternetResourcesFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'title',
                'required' => false
            ])
            ->add('url', TextType::class, [
                'label' => 'template.link',
                'required' => false
            ])
            ->add('file', VichImageType::class, [
                'label' => 'image',
                'required' => false
            ])
            ->add('isPublished', CheckboxType::class, [
                'label' => 'to_public',
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'save'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => InternetResources::class,
            'translation_domain' => 'messages',
        ]);
    }
}
