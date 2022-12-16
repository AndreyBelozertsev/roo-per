<?php

namespace Portal\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use Portal\ContentBundle\Entity\Menu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class StructureAdminType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'structure_form.label',
            ])
            ->add('menu', EntityType::class, [
                'class' => 'PortalContentBundle:Menu',
                'choice_label' => 'title',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('m')
                        ->where('m.code = :code')
                        ->setParameter('code', Menu::STRUCTURE_MENU);
                },
                'em' => 'customer',
                'required' => true,
            ])
            ->add('route', TextType::class, [
                'label' => 'structure_form.route',
                'required' => false,
            ])
            ->add('beforeText', TextareaType::class, [
                'label' => 'menu_page.before_text',
                'required' => false,
            ])
            ->add('afterText', TextareaType::class, [
                'label' => 'menu_page.after_text',
                'required' => false,
            ])
            ->add('isPublished', CheckboxType::class, [
                'label' => 'menu_page.is_published',
                'required' => false,
            ])
            ->add('isLinkOnId', CheckboxType::class, [
                'label' => 'menu_page.is_link_on_id',
                'required' => false,
                'data' => true,
            ])
            ->add('parent', HiddenType::class, [
                'required' => true,
            ])
            ->add('order', HiddenType::class, [
                'required' => true,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Portal\ContentBundle\Entity\MenuNode'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'structure';
    }
}
