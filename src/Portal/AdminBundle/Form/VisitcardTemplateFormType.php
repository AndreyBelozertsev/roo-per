<?php

namespace Portal\AdminBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Portal\ContentBundle\Entity\VisitcardTemplate;
use Portal\ContentBundle\Entity\Page;

class VisitcardTemplateFormType extends AbstractType
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
            ->add('link', TextType::class, [
                'label' => 'template.link',
                'required' => false
            ])
            ->add('attachment', VisitcardAttachmentFormType::class)
            ->add('submit', SubmitType::class, [
                'label' => 'save'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => VisitcardTemplate::class,
            'translation_domain' => 'messages'
        ]);
    }
}
