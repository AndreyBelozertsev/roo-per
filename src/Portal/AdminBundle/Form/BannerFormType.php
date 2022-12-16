<?php

namespace Portal\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Portal\ContentBundle\Entity\Banner;

//use Portal\AdminBundle\Form\BannerAttachmentFormType;

class BannerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'title',
                'required' => false
            ])
            ->add('ref', TextType::class, [
                'label' => 'banner.reference',
                'required' => false
            ])
//            ->add('slider', EntityType::class, [
//                'class' => Slider::class,
//                'label' => 'tags',
//                'choice_label' => 'banner.choice_slider',
//                'multiple' => true,
//                'required' => false,
//                'em' => 'customer'
//            ])
            ->add('file', VichImageType::class, [
                'label' => 'image',
                'attr' => [
                    'accept' => 'image/jpeg,image/png',
                    'class' => 'load-img-button'
                ],
                'allow_delete' => false,
                'required' => false
            ])
            ->add('cropStartX', HiddenType::class)
            ->add('cropStartY', HiddenType::class)
            ->add('cropWidth', HiddenType::class)
            ->add('cropHeight', HiddenType::class)

            ->add('isPublished', CheckboxType::class, [
                'label' => 'to_public',
                'required' => false
            ])
            ->add('openInNewTab', CheckboxType::class, [
                'label' => 'open_in_new_tab',
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'save'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Banner::class,
            'translation_domain' => 'messages'
        ]);
        $resolver->setDefined([
            'validation_groups',
        ]);
    }
}
