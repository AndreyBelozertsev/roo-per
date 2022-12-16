<?php

namespace Portal\AdminBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Portal\ContentBundle\Entity\StandardTemplate;
use Portal\ContentBundle\Entity\Page;

class StandardTemplateFormType extends AbstractType
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
            ->add('attachment', StandardTemplateAttachmentFormType::class)
            ->add('submit', SubmitType::class, [
                'label' => 'save'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => StandardTemplate::class,
            'translation_domain' => 'messages'
        ]);
    }
}
