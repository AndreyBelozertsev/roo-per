<?php

namespace Portal\UserBundle\Controller;

use Doctrine\DBAL\DBALException;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Model\UserInterface;
use Portal\UserBundle\Form\OperatorEmailType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Controller managing the user profile.
 */
class ProfileController extends Controller
{
    /**
     * Show the user.
     */
    public function showAction(Request $request)
    {
        $paramRedirectTo = $request->getSession()->get('for_esia_redirect_to');
        $user = $this->getUser();
        $esiaUserData = [];

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        if ($paramRedirectTo !== null) {
            $request->getSession()->remove('for_esia_redirect_to');
            return $this->redirectToRoute($paramRedirectTo['route'], [$paramRedirectTo['param'] => $paramRedirectTo['value']]);
        }

        if ($user->getEsiaRefreshToken()) {
            $esiaUserData['full_name'] = $this->get('esia_user_resource_owner')->getUserEsiaFullNameInArray($user);
            $esiaUserData['email'] = $this->get('esia_user_resource_owner')->getUserEsiaEmail($user);
            $esiaUserData['phone'] = $this->get('esia_user_resource_owner')->getUserEsiaPhone($user);
            $esiaUserData['home_phone'] = $this->get('esia_user_resource_owner')->getUserEsiaHomePhone($user);
            $esiaUserData['address_registered'] = $this->get('esia_user_resource_owner')->getUserEsiaAddressRegistered($user);
            $esiaUserData['address_actual'] = $this->get('esia_user_resource_owner')->getUserEsiaAddressActual($user);
        }

        $form = $this->createForm(OperatorEmailType::class, $user, [
            'validation_groups' => ['edit_email']
        ]);
        $form->handleRequest($request);

        return $this->render('@PortalProfile/Petition/petition_person_data.html.twig', [
            'user' => $user,
            'esiaUserData' => $esiaUserData,
            'form' => $form->createView()
        ]);
    }

    /**
     * Edit the user.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory FactoryInterface */
        $formFactory = $this->get('fos_user.profile.form.factory');

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var $userManager UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            $request->getSession()->getFlashBag()->add('message', $this->get('translator')->trans('successfully_change', array(), 'messages'));

            return $response;
        }

        return $this->render('PortalUserBundle:Profile:edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function emailEditAction(Request $request)
    {

        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $form = $this->createForm(OperatorEmailType::class, $user, [
            'validation_groups' => ['edit_email']
        ]);
        $jsonParams = [
            'message' => '',
            'status' => false,
            'content' => '1'
        ];

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $userManager = $this->get('fos_user.user_manager');
                    $user->setEmail($user->getEmail());
                    $userManager->updateUser($user);
                    $jsonParams['status'] = true;
                } catch (DBALException $e) {
                    $jsonParams['message'] = $this->get('translator')->trans('wrong_date', [], 'messages');
                }
            } else {
            $jsonParams['content'] = $this->render('@PortalProfile/Petition/petition_operator_email.html.twig', [
                'form' => $form->createView()
            ])->getContent();
            }
        } else {
            $jsonParams['content'] = "error";
        }

        return new JsonResponse($jsonParams);


    }
}
