<?php

namespace Portal\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Portal\ContentBundle\Entity\SiteMapTemplate;
use Portal\ContentBundle\Entity\Page;

class SiteMapTemplateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pageId', EntityType::class, [
                'class' => Page::class,
                'attr'=> ['style'=>'display:none'],
                'em' => 'customer'
            ])
            ->add('link', TextType::class, [
                'label' => 'template.root_link',
                'required' => false
            ])
            ->add('map', HiddenType::class, [
                'label' => 'site_map_template'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'save'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SiteMapTemplate::class,
            'translation_domain' => 'messages'
        ]);
    }
}
