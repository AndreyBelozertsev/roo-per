<?php

namespace Portal\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Portal\AdminBundle\Entity\Instance;

class InstanceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (isset($options['newInstance']) && $options['newInstance']) {
            $builder
                ->add('category', EntityType::class, array(
                    'label' => 'instance_form.instance_category',
                    'class' => 'Portal\ContentBundle\Entity\InstanceCategory',
                    'choices' => $options['instanceCategoryList'],
                    'multiple' => false,
                    'required' => false
                ))
                ->add('code', TextType::class, array(
                    'label' => 'instance_form.code',
                    'required' => false,
                ))
                ;
        }
        $builder
                ->add('parentInstance', EntityType::class, array(
                    'label' => 'instance_form.parent_instance',
                    'class' => 'Portal\AdminBundle\Entity\Instance',
                    'choices' => $options['instanceList'],
                    'multiple' => false,
                    'required' => false
                ))
                ->add('title', TextType::class, array(
                    'label' => 'instance_form.title',
                    'required' => false,
                ))
                ->add('save', SubmitType::class, array(
                    'label' => 'instance_form.save'
                ))
                ;
    }

    public function getName()
    {
        return 'InstanceFormType';
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'newInstance' => false,
        ));
        $resolver->setDefined(array(
            'newInstance', 
            'instanceCategoryList',
            'instanceList',
        ));
    }

}
