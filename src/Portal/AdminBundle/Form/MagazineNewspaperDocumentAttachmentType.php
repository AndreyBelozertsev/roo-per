<?php

namespace Portal\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Portal\ContentBundle\Entity\Attachment;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Portal\ContentBundle\Entity\MagazineNewspaperDocumentAttachment;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


class MagazineNewspaperDocumentAttachmentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', VichFileType::class, [
                'label' => false,
                'attr' => [
                    'accept' => implode(',', Attachment::DOCUMENTS).','.implode(',', Attachment::ARCHIVES),
                    'data-max-size' => 20480
                ],
                'required' => false

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MagazineNewspaperDocumentAttachment::class,
            'translation_domain' => 'messages'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_magazine_newspaper_document_attachment';
    }
}
