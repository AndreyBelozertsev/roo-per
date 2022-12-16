<?php

namespace Portal\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use Portal\ContentBundle\Entity\Menu;
use Portal\ContentBundle\Entity\StructureTemplate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Portal\ContentBundle\Entity\Document;
use Portal\ContentBundle\Entity\DocumentTag;
use Portal\ContentBundle\Entity\DocumentCategory;

class DocumentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => DocumentCategory::class,
                'label' => 'document_form.category_label',
                'placeholder' => 'document_form.category_empty',
                'em' => 'customer',
                'required' => false
            ])
            ->add('title', TextType::class, [
                'label' => 'document_form.title',
                'required' => false
            ])
            ->add('content', TextareaType::class, [
                'label' => 'document_form.content',
                'required' => false
            ])
            ->add('menuNode', EntityType::class, [
                'label' => 'document_form.bloc',
                'class' => 'PortalContentBundle:MenuNode',
                'choice_label' => 'title',
                'em' => 'customer',
                'placeholder' => 'document_form.select_menu_node',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('m_n')
                        ->join('m_n.menu', 'm')
                        ->leftJoin('m_n.structureTemplate', 'st')
                        ->where('m.code = :code')
                        ->andWhere('m_n.isDeleted = false')
                        ->andWhere('m_n.isPublished = true')
                        ->andWhere('st.code = :codeTpl1 OR st.code = :codeTpl2 OR m_n.structureTemplate IS NULL')
                        ->setParameters([
                            'code' => Menu::STRUCTURE_MENU,
                            'codeTpl1' => StructureTemplate::SIMPLE,
                            'codeTpl2' => StructureTemplate::DOCUMENT
                        ]);
                },
                'required' => false
            ])
            ->add('isPublished', CheckboxType::class, [
                'label' => 'document_form.public',
                'required' => false
            ])
            ->add('documentNumber', TextType::class, [
                'label' => 'document_form.doc_number',
                'required' => false
            ])
            ->add('approvalDate', DateType::class, [
                'label' => 'document_form.approv_date',
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('documentType', ChoiceType::class, [
                'label' => 'document_form.doc_type',
                'choices' => array_flip(Document::DOC_TYPES),
                'data' => Document::TYPE_PUBLIC_DOCUMENT,
                'required' => false
            ])
            ->add('attachments', CollectionType::class, [
                'entry_type' => AttachmentFormForUploadDocumentType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => '__prototype_photo__',
                'by_reference' => false,
                'label' => false,
                'required' => true
            ])
            ->add('tags', EntityType::class, [
                'class' => DocumentTag::class,
                'label' => 'tags',
                'choice_label' => 'tag',
                'multiple' => true,
                'em' => 'customer',
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'save'
            ]);

        if (in_array('edit_document_pub_date', $options['permissions']) || $options['isSuperAdmin']) {
            $builder->add('publishedAt', DateType::class, [
                'label' => 'article_form.published_date',
                'widget' => 'single_text',
                'required' => false,
            ]);
        }

        if (isset($options['slug']) && $options['slug']) {
            $builder
                ->add('slug', TextType::class, [
                    'label' => 'document_form.slug',
                    'required' => false
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
            'data_class' => Document::class,
            'translation_domain' => 'messages',
        ]);

        $resolver->setDefined([
            'permissions',
            'isSuperAdmin',
            'slug',
            'validation_groups'
        ]);
    }
}
