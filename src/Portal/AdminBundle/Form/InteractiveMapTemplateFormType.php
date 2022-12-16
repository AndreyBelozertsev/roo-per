<?php

namespace Portal\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Portal\ContentBundle\Entity\InteractiveMapTemplate;
use Portal\ContentBundle\Entity\Page;

class InteractiveMapTemplateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pageId', EntityType::class, [
                'class' => Page::class,
                'attr'=> ['style'=>'display:none'],
                'em' => 'customer'
            ])
            ->add('content', TextareaType::class, [
                'label' => 'template.content',
                'required' => false
            ])
            ->add('mapCode', HiddenType::class, [
                'label' => 'template.interactive_map'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'save'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => InteractiveMapTemplate::class,
            'translation_domain' => 'messages',
        ]);
    }
}
