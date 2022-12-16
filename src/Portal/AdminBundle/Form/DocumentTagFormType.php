<?php

namespace Portal\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Portal\ContentBundle\Entity\DocumentTag;

class DocumentTagFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tag', TextType::class, [
                'label' => 'tag.tag',
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'save'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DocumentTag::class,
            'translation_domain' => 'messages'
        ]);
    }
}
