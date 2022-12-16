<?php

namespace Portal\AdminBundle\Form;

use Portal\ContentBundle\Entity\Attachment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Portal\ContentBundle\Entity\DocumentAttachment;
use Vich\UploaderBundle\Form\Type\VichFileType;

class AttachmentFormForUploadDocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', VichFileType::class, [
                'label' => false,
                'attr' => [
                    'accept' => implode(',', Attachment::DOCUMENTS).','.implode(',', Attachment::ARCHIVES),
                    'data-max-size' => Document::MAX_SIZE_FILE_ATTACHMENT
                ],
                'required' => false
            ])
            ->add('description', TextType::class, [
                'label' => 'photo_report_form.description',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DocumentAttachment::class,
        ]);
    }
}
