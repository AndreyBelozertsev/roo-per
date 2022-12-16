<?php

namespace Portal\ContentBundle\Form;

use Portal\ContentBundle\Entity\FeedbackFormValue;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Portal\ContentBundle\Entity\FeedbackFormAttachment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class FeedbackFormAttachmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', VichFileType::class, [
                'required' => false,
                'label' => false,
                'label_attr' => ['class' => 'page-form__label _padding-left'],
                'attr' => [
                    'accept' => 'application/octet-stream,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.ms-office,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.oasis.opendocument.text,application/vnd.oasis.opendocument.spreadsheet,application/pdf,application/rtf,text/richtext,text/rtf,application/x-rtf,text/csv,text/plain,image/jpeg,image/png,image/gif,.xls,.xlsx,.doc,.docx,.rtf,.csv',
                    'class' => 'load-img-button',
                    'data-max-size' => FeedbackFormValue::MAX_SIZE_UPLOAD_FILE,
                    'data-max-counter' => FeedbackFormValue::MAX_COUNTER_UPLOAD_FILE
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FeedbackFormAttachment::class,
        ]);
    }
}
