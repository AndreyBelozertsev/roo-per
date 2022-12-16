<?php

namespace Portal\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use Portal\ContentBundle\Entity\StructureTemplate;
use Portal\ContentBundle\Entity\Menu;
use Portal\ContentBundle\Entity\MenuNode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class MenuNodeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('structureTemplate', EntityType::class, [
                'class' => StructureTemplate::class,
                'label' => 'menu_page.structure_template',
                'placeholder' => 'menu_page.select_empty_structure_template',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('st')
                        ->where('st.type = :type')
                        ->setParameter('type', StructureTemplate::TEMPLATE_TYPE_LIST['structure']);
                },
                'em' => 'customer',
                'required' => false,
            ])
            ->add('parent', EntityType::class, [
                'class' => MenuNode::class,
                'label' => 'menu_page.parent',
                'placeholder' => 'menu_page.select_empty_parent',
                'required' => false,
                'em' => 'customer'
            ])
            ->add('title', TextType::class, [
                'label' => 'menu_page.title',
                'required' => false,
            ])
            ->add('route', TextType::class, [
                'label' => 'menu_page.route',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'save'
            ])
            ->add('beforeText', TextareaType::class, [
                'label' => 'menu_page.before_text',
                'required' => false,
            ])
            ->add('afterText', TextareaType::class, [
                'label' => 'menu_page.after_text',
                'required' => false,
            ])
            ->add('isSeparator', CheckboxType::class, [
                'label' => 'menu_page.is_separator',
                'required' => false
            ])
            ->add('isHidden', CheckboxType::class, [
                'label' => 'menu_page.is_hidden',
                'required' => false
            ])
            ->add('isMain', CheckboxType::class, [
                'label' => 'menu_page.is_main',
                'required' => false
            ])
            ->add('isHideChilds', CheckboxType::class, [
                'label' => 'menu_page.is_hide_childs',
                'required' => false
            ])
            ->add('isPublished', CheckboxType::class, [
                'label' => 'menu_page.is_published',
                'required' => false
            ])
        ;

        if ($options['isSuperAdmin']) {
            $builder
                ->add('manualCreatedAt', DateType::class, [
                    'label' => 'menu_page.manual_create_date',
                    'widget' => 'single_text',
                    'required' => false,
                ])
                ->add('manualUpdatedAt', DateType::class, [
                    'label' => 'menu_page.manual_upadate_date',
                    'widget' => 'single_text',
                    'required' => false,
                ])
            ;
        }

        if (!$options['data']->getMenu() instanceof Menu) {
            $builder
                ->add('menu', EntityType::class, [
                    'class' => Menu::class,
                    'label' => 'menu_page.menu',
                    'placeholder' => 'menu_page.select_empty_category',
                    'em' => 'customer',
                    'required' => false
                ])
            ;
        }

        if (isset($options['slug']) && $options['slug']) {
            $builder
                ->add('slug', TextType::class, [
                    'label' => 'menu_page.menu_node_slug',
                    'required' => false,
                ])
                ->add('isLinkOnId', CheckboxType::class, [
                    'label' => 'menu_page.is_link_on_id',
                    'required' => false
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MenuNode::class,
            'translation_domain' => 'messages',
        ]);

        $resolver->setDefined([
            'isSuperAdmin',
            'slug',
//            'listStructure',
//            'validation_groups',
        ]);
    }
}
