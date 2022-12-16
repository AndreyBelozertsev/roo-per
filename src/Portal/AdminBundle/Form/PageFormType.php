<?php

namespace Portal\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Portal\ContentBundle\Entity\Page;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('templateId', ChoiceType::class, [
                'label' => 'template_page',
                'choices' => array_flip(Page::TEMPLATE_LIST),
                'required' => false
            ])
            ->add('title', TextType::class, [
                'label' => 'title',
                'required' => false
            ])
            ->add('isPublished', CheckboxType::class, [
                'label' => 'article_form.is_published',
                'required' => false,
                'data' => true
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'create'
            ])
        ;
        if (isset($options['slug']) && $options['slug']) {
            $builder->add('slug', TextType::class, [
                'label' => 'slug',
                'required' => false
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
            'translation_domain' => 'messages'
        ]);
        $resolver->setDefined([
            'validation_groups',
            'slug'
        ]);
    }
}
