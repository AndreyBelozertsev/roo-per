<?php

namespace Portal\ContentBundle\Controller;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityRepository;
use Portal\ContentBundle\Entity\Feedback;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Portal\ContentBundle\Entity\FeedbackForm;
use Symfony\Component\HttpFoundation\Request;
use Portal\ContentBundle\Entity\FeedbackCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Portal\ContentBundle\Entity\FeedbackFormValue;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Portal\ContentBundle\Form\FeedbackFormAttachmentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class FeedbackFormController extends Controller
{
    private $esiaValue = [];

    public function indexAction(Request $request)
    {
        $feedbackObject = $this->get('feedback_manager')
            ->findOneBy(['id' => 1]);

        if (!$feedbackObject instanceof Feedback) {
            throw $this->createNotFoundException($this->get('translator')->trans('error_page.text_404'));
        }

        $feedback = ['isFormShown' => $feedbackObject->getIsFormShown()];
        switch ($this->container->get('request_stack')->getCurrentRequest()->getLocale()) {
            case 'uk':
                $feedback['content'] = $feedbackObject->getContentUk();
                break;
            case 'ru':
                $feedback['content'] = $feedbackObject->getContentRu();
                break;
            case 'en':
                $feedback['content'] = $feedbackObject->getContentEn();
                break;
        }

        $formParams['feedback'] = $feedback;
        if ($feedbackObject->getIsFormShown()) {
            $param['id'] = (int)$request->get('id');
            $feedbackFormData = $this->get('customer_feedback_form_manager')->findOneBy($param);
            if (!$feedbackFormData instanceof FeedbackForm || !$feedbackFormData->getIsPublished()) {

                throw $this->createNotFoundException($this->get('translator')->trans('error_page.text_404'));
            }

            $formParams['feedbackFormData'] = $feedbackFormData;

            $formParams['user'] = true;
            $user = $this->getUser();
            if ($feedbackFormData->getIsRegisteredUser()) {
                if (!$user) {
                    $formParams['user'] = false;
                    return $this->render('PortalContentBundle:FeedbackForm:index.html.twig', $formParams);
                }
            }

            $idFeedbackForm = $feedbackFormData->getId();
            $feedbackFormValue = new FeedbackFormValue();
            $feedbackFormValue->setForm($this->get('customer_feedback_form_manager')->find($idFeedbackForm));
            $fields = json_decode($feedbackFormData->getSortOptions());
            $form = $this->feedbackFormBuild($feedbackFormValue, $fields, $feedbackFormData->getIsAgreePersonalData());
            $form->handleRequest($request);
            $formParams['form'] = $form->createView();
            $formParams['feedbackForm'] = $feedbackFormValue;
            $formParams['fieldList'] = $fields;
            if ($request->isMethod('POST')) {
                $flashBag = $this->get('session')->getFlashBag();
                if ($form->isSubmitted() && $form->isValid()) {
                    $em = $this->getDoctrine()->getManager('customer');
                    $em->getConnection()->beginTransaction();
                    try {
                        $feedbackFormValue->setForm($this->get('customer_feedback_form_manager')->find($idFeedbackForm));
                        $em->persist($feedbackFormValue);
                        $em->flush();
                        $result = $this->messageSend($feedbackFormValue);
                        if ($result) {
                            $em->getConnection()->commit();
                            $flashBag->add('message', $feedbackFormData->getMessageSuccess());
                            if ($request->isXmlHttpRequest()) {

                                return new JsonResponse([
                                    'status' => true,
                                    'route' => $this->generateUrl('feedback_page', ['id' => $param['id']])
                                ]);
                            } else {
                                return $this->redirectToRoute('feedback_page', ['id' => $param['id']]);
                            }
                        } else {
                            $em->getConnection()->rollback();
                        }
                    } catch (DBALException $e) {
                        $em->getConnection()->rollback();
                        $flashBag->add('message', $feedbackFormData->getMessageError());
                    }
                } else {
                    if ($request->isXmlHttpRequest()) {
                        $errors = [];
                        foreach ($form->getErrors(true) as $k => $err) {
                            if (null === $err->getCause() && !empty($err->getMessage())) {
                                $errors[$k]['field'] = 'children[captcha].data';
                            } else {
                                $errors[$k]['field'] = $err->getCause()->getPropertyPath();
                                $file = $err->getCause()->getInvalidValue();
                                if ($file instanceof UploadedFile) {
                                    $errors[$k]['file'] = $file->getClientOriginalName();
                                }
                            }
                            $errors[$k]['message'] = $err->getMessage();
                        }

                        return new JsonResponse([
                            'status' => false,
                            'errors' => $errors
                        ]);
                    }

                    $flashBag->add('message', $feedbackFormData->getMessageError());
                }
            }
        }

        return $this->render('PortalContentBundle:FeedbackForm:index.html.twig', $formParams);
    }

    private function messageSend(FeedbackFormValue $feedbackFormValue)
    {
        $body = $this->get('templating')->render('PortalContentBundle:FeedbackForm:messageBody.html.twig', [
            'feedbackFormValue' => $feedbackFormValue,
        ]);

        $toSend = $feedbackFormValue->getForm()->getEmailResponsible();
        $subject = $feedbackFormValue->getSubject();

        $path = $this->get('kernel')->getRootDir() . '/../web';

        $mailer = $this->get('mailer');
        try {
            $message = \Swift_Message::newInstance()
                ->setFrom($this->getParameter('mailer_user'))
                ->setTo($toSend)
                ->setSubject($subject)
                ->setBody($body, 'text/html');
            foreach ($feedbackFormValue->getPreviews() as $preview) {
                $message->attach(\Swift_Attachment::fromPath($path . $preview->getPreviewFileUrl())->setFilename($preview->getOriginalFileName()));
            }
            $result = $mailer->send($message);
            $resultFlush = $mailer->getTransport()->getSpool()->flushQueue($this->get('swiftmailer.transport.real'));
            if ($result && $resultFlush) {

                return true;
            } else {

                return false;
            }
        } catch (\Exception $e) {
//            throw \Exception::class
            return false;
        }
//        $message = \Swift_Message::newInstance()
//            ->setFrom($this->getParameter('mailer_user'))
//            ->setTo($toSend)
//            ->setSubject($subject)
//            ->setBody($body, 'text/html')
//        ;
//        foreach ($feedbackFormValue->getPreviews() as $preview) {
//            $message->attach(\Swift_Attachment::fromPath($path.$preview->getPreviewFileUrl())->setFilename($preview->getOriginalFileName()));
//        }
//        $result = $mailer->send($message);
//        $resultFlush = $mailer->getTransport()->getSpool()->flushQueue($this->get('swiftmailer.transport.real'));
//        if ($result && $resultFlush) {
//            return true;
//        } else {
//            return false;
//        }
    }

    private function feedbackFormBuild($feedbackFormValue, $arrayField, $isAgree = true)
    {
        $options = [];
        if (in_array('email', $arrayField) || in_array('phone', $arrayField)) {
            $options['validation_groups'] = 'email_phone_validation';
        }
        $form = $this->createFormBuilder($feedbackFormValue, $options);
        foreach ($arrayField as $field) {
                $fieldParams = $this->getFieldParam($field);
                if ($fieldParams) {
                    foreach ($fieldParams as $param) {
                        $form->add($param['field'], $param['type'], $param['param']);
                    }
                }
        }
        $form->add('captcha', CaptchaType::class, [
            'label' => 'captcha',
//            'captchaConfig' => 'TypoCaptcha',
            'reload' => true,
            'as_url' => true,
            'required' => false,
        ]);
        if ($isAgree) {
            $form->add('agree', CheckboxType::class, [
                'mapped' => false,
                'label' => 'feedback_form_value.agree',
                'required' => true,
                'constraints' => array(new NotBlank(['message' => 'feedback_form_value.agree']))
            ]);
        }

        return $form->getForm();
    }

    private function getFieldParam($fieldName)
    {
        switch ($fieldName) {
            case "subject":
                return [
                    [
                        'field' => 'subject',
                        'type' => TextType::class,
                        'param' => [
                            'validation_groups' => ['Default', 'email_phone_validation'],
                            'label' => 'feedback_form_value.subject',
                            'required' => false,
                            'attr' => [
                                'class' => 'page-form__pole _pole_subject'
                            ],
//                            'attr_label' => [
//                                'class' => 'page-form__pole _novalid'
//                            ],
                            'constraints' => [
                                new NotBlank(
                                    ['message' => 'feedback_form_value.not_blank']
                                ),
                                new Length(
                                    [
                                        'maxMessage' => 'feedback_form_value.too_much',
                                        'max' => 1000
                                    ]
                                ),
                            ]
                        ]
                    ]
                ];
                break;
            case "categoryId":
                return [
                    [
                        'field' => 'categoryId',
                        'type' => EntityType::class,
                        'param' => [
                            'validation_groups' => ['Default', 'email_phone_validation'],
                            'label' => 'feedback_form_value.category',
                            'class' => FeedbackCategory::class,
                            'placeholder' => 'feedback_form_value.placeholder_category',
                            'required' => false,
                            'em' => 'customer',
                            'attr' => [
                                'class' => 'chosen-select page-form__pole _pole_categoryId'
                            ],
                            'constraints' => [
                                new NotNull(
                                    ['message' => 'feedback_form_value.category']
                                ),
                            ],
                            'query_builder' => function (EntityRepository $er) {
                                return $er->createQueryBuilder('ff')
                                    ->where('ff.codeGroup = :codeGroup')
                                    ->setParameters([
                                        'codeGroup' => FeedbackCategory::DEFAULT_GROUP,
                                    ]);
                            },
                        ]
                    ]
                ];
                break;
            case "addressGroup":
                return [
                    [
                        'field' => 'addressGroup',
                        'type' => EntityType::class,
                        'param' => [
                            'validation_groups' => ['Default', 'email_phone_validation'],
                            'label' => 'feedback_form_value.address_group',
                            'class' => FeedbackCategory::class,
                            'placeholder' => 'feedback_form_value.placeholder_address_group',
                            'required' => false,
                            'em' => 'customer',
                            'attr' => [
                                'class' => 'chosen-select page-form__pole _pole_addressGroup'
                            ],
                            'constraints' => [
                                new NotNull(
                                    ['message' => 'feedback_form_value.category']
                                ),
                            ],
                            'query_builder' => function (EntityRepository $er) {
                                return $er->createQueryBuilder('ff')
                                    ->where('ff.codeGroup = :codeGroup')
                                    ->setParameters([
                                        'codeGroup' => FeedbackCategory::ADDRESS_GROUP,
                                    ])
                                    ->orderBy('ff.sortOrderGroup');
                            },
                        ]
                    ]
                ];
                break;
            case "sexGroup":
                return [
                    [
                        'field' => 'sexGroup',
                        'type' => EntityType::class,
                        'param' => [
                            'validation_groups' => ['Default', 'email_phone_validation'],
                            'label' => 'feedback_form_value.sex_group',
                            'class' => FeedbackCategory::class,
                            'placeholder' => 'feedback_form_value.placeholder_sex_group',
                            'required' => false,
                            'em' => 'customer',
                            'attr' => [
                                'class' => 'chosen-select page-form__pole _pole_sexGroup'
                            ],
                            'constraints' => [
                                new NotNull(
                                    ['message' => 'feedback_form_value.category']
                                ),
                            ],
                            'query_builder' => function (EntityRepository $er) {
                                return $er->createQueryBuilder('ff')
                                    ->where('ff.codeGroup = :codeGroup')
                                    ->setParameters([
                                        'codeGroup' => FeedbackCategory::SEX_GROUP,
                                    ]);
                            },
                        ]
                    ]
                ];
                break;
            case "ageGroup":
                return [
                    [
                        'field' => 'ageGroup',
                        'type' => EntityType::class,
                        'param' => [
                            'validation_groups' => ['Default', 'email_phone_validation'],
                            'label' => 'feedback_form_value.age_group',
                            'class' => FeedbackCategory::class,
                            'placeholder' => 'feedback_form_value.placeholder_age_group',
                            'required' => false,
                            'em' => 'customer',
                            'attr' => [
                                'class' => 'chosen-select page-form__pole _pole_ageGroup'
                            ],
                            'constraints' => [
                                new NotNull(
                                    ['message' => 'feedback_form_value.category']
                                ),
                            ],
                            'query_builder' => function (EntityRepository $er) {
                                return $er->createQueryBuilder('ff')
                                    ->where('ff.codeGroup = :codeGroup')
                                    ->setParameters([
                                        'codeGroup' => FeedbackCategory::AGE_GROUP,
                                    ]);
                            },
                        ]
                    ]
                ];
                break;
            case "socialGroup":
                return [
                    [
                        'field' => 'socialGroup',
                        'type' => EntityType::class,
                        'param' => [
                            'validation_groups' => ['Default', 'email_phone_validation'],
                            'label' => 'feedback_form_value.social_group',
                            'class' => FeedbackCategory::class,
                            'placeholder' => 'feedback_form_value.placeholder_social_group',
                            'required' => false,
                            'em' => 'customer',
                            'attr' => [
                                'class' => 'chosen-select page-form__pole _pole_socialGroup'
                            ],
                            'constraints' => [
                                new NotNull(
                                    ['message' => 'feedback_form_value.category']
                                ),
                            ],
                            'query_builder' => function (EntityRepository $er) {
                                return $er->createQueryBuilder('ff')
                                    ->where('ff.codeGroup = :codeGroup')
                                    ->setParameters([
                                        'codeGroup' => FeedbackCategory::SOCIAL_GROUP,
                                    ]);
                            },
                        ]
                    ]
                ];
                break;
            case "privilegeGroup":
                return [
                    [
                        'field' => 'privilegeGroup',
                        'type' => EntityType::class,
                        'param' => [
                            'validation_groups' => ['Default', 'email_phone_validation'],
                            'label' => 'feedback_form_value.privilege_group',
                            'class' => FeedbackCategory::class,
                            'placeholder' => 'feedback_form_value.placeholder_privilege_group',
                            'required' => false,
                            'em' => 'customer',
                            'attr' => [
                                'class' => 'chosen-select page-form__pole _pole_privilegeGroup'
                            ],
                            'constraints' => [
                                new NotNull(
                                    ['message' => 'feedback_form_value.category']
                                ),
                            ],
                            'query_builder' => function (EntityRepository $er) {
                                return $er->createQueryBuilder('ff')
                                    ->where('ff.codeGroup = :codeGroup')
                                    ->setParameters([
                                        'codeGroup' => FeedbackCategory::PRIVILEGE_GROUP,
                                    ]);
                            },
                        ]
                    ]
                ];
                break;
            case "message":
                return [
                    [
                        'field' => 'message',
                        'type' => TextareaType::class,
                        'param' => [
                            'validation_groups' => ['Default', 'email_phone_validation'],
                            'label' => 'feedback_form_value.message',
                            'required' => false,
                            'attr' => [
                                'class' => 'page-form__pole page-form__pole_textarea  _pole_message'
                            ],
                            'constraints' => [
                                new NotBlank(
                                    ['message' => 'feedback_form_value.not_blank']
                                ),
                            ]
                        ]
                    ]
                ];
                break;
            case "previews":
                return [
                    [
                        'field' => 'previews',
                        'type' => CollectionType::class,
                        'param' => [
//                            'label' => 'feedback_form_value.files',
                            'validation_groups' => ['Default', 'email_phone_validation'],
                            'label' => false,
                            'entry_type' => FeedbackFormAttachmentType::class,
                            'entry_options' => array('label' => false,),
                            'allow_add' => true,
                            'allow_delete' => true,
                            'prototype' => true,
                            'prototype_name' => '__attachment_file__',
                            'by_reference' => false,
                            'required' => false,
                            'attr' => [
                                'class' => 'page-form__label _block'
                            ],
                        ]
                    ],
//                    [
//                        'field' => 'references',
//                        'type' => CollectionType::class,
//                        'param' => [
//                            'label' => false,
//                            'entry_type' => FeedbackFormReferenceAttachmentType::class,
//                            'entry_options' => array('label' => false,),
//                            'allow_add' => true,
//                            'allow_delete' => true,
//                            'prototype' => true,
//                            'prototype_name' => '__feedback_file__',
//                            'by_reference' => false,
//                            'required' => false,
//                            'attr' => [
//                                'class' => 'page-form__label _block'
//                            ],
//                        ]
//                    ]
                ];
                break;
            case "author":
                return [
                    [
                        'field' => 'author',
                        'type' => TextType::class,
                        'param' => [
                            'validation_groups' => ['Default', 'email_phone_validation'],
                            'label' => 'feedback_form_value.author',
                            'required' => false,
                            'attr' => [
                                'class' => 'page-form__pole _pole_author'
                            ],
                            'constraints' => [
                                new NotBlank(
                                    [
                                        'message' => 'feedback_form_value.not_blank',
                                    ]
                                ),
                            ]
                        ]
                    ]
                ];
                break;
            case "email":
                return [
                    [
                        'field' => 'email',
                        'type' => TextType::class,
                        'param' => [
                            'validation_groups' => ['email_phone_validation'],
                            'label' => 'feedback_form_value.email',
                            'required' => false,
                            'attr' => [
                                'class' => 'page-form__pole _pole_email'
                            ],
                            'constraints' => [
                                new Length(
                                    [
                                        'maxMessage' => 'feedback_form_value.too_much',
                                        'max' => 1000,
                                        'groups' => 'email_phone_validation',
                                    ]
                                ),
                                new Email(
                                    [
                                        'message' => 'feedback_form_value.email.email',
                                        'checkMX' => true,
                                        'groups' => 'email_phone_validation',
                                    ]
                                )
                            ]
                        ]
                    ]
                ];
                break;
            case "phone":
                return [
                    [
                        'field' => 'phone',
                        'type' => TextType::class,
                        'param' => [
                            'validation_groups' => ['email_phone_validation'],
                            'label' => 'feedback_form_value.phone',
                            'required' => false,
                            'attr' => [
                                'class' => 'page-form__pole _pole_phone'
                            ],
//                            'constraints' => [
//                                new NotBlank(
//                                    ['message' => 'feedback_form_value.not_blank']
//                                ),
//                            ]
                        ]
                    ]
                ];
                break;
            default:
                return false;
                break;
        }
    }

    /**
     * Redirect to page authorize esia.
     *
     * @param Request $request
     * @param string $slug
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function authEsiaLoginAction(Request $request, $slug)
    {
        $session = $request->getSession();
        $options['route'] = 'feedback_page_slug';
        $options['param'] = 'slug';
        $options['value'] = $slug;
        $session->set('for_esia_redirect_to', $options);

        return $this->redirectToRoute('esia_login');
    }

    private function getEsiaUserData($feedbackFormValue, $esiaField)
    {
        $user = $this->getUser();
        switch ($esiaField) {
            case 'author':
                $feedbackFormValue->setAuthor($user->getFirstName() . ' ' . $user->getMiddleName() . ' ' . $user->getLastName());
                $this->esiaValue[$esiaField] = $user->getFirstName() . ' ' . $user->getMiddleName() . ' ' . $user->getLastName();
                break;
            case 'phone':
                if ($user->getPhone()) {
                    $feedbackFormValue->setPhone($user->getPhone());
                    $this->esiaValue[$esiaField] = $user->getPhone();
                    break;
                }
                if ($user->getHomePhone()) {
                    $feedbackFormValue->setPhone($user->getHomePhone());
                    $this->esiaValue[$esiaField] = $user->getHomePhone();
                }
                break;
            case 'email':
                if ($user->getEmail()) {
                    $feedbackFormValue->setEmail($user->getEmail());
                    $this->esiaValue[$esiaField] = $user->getEmail();
                }
                break;
//            case 'addressGroup':
//                if ($user->getAddressActual()) {
//                    $feedbackFormValue->setAddressGroup($user->getAddressActual());
//                    $this->esiaValue[$esiaField] = $user->getAddressActual();
//                    break;
//                }
//                if ($user->getAddressRegistered()) {
//                    $feedbackFormValue->setAddressGroup($user->getAddressRegistered());
//                    $this->esiaValue[$esiaField] = $user->getAddressRegistered();
//                }
//                break;
        }
    }
}
