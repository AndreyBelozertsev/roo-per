<?php

namespace Portal\AdminBundle\Controller;

use Portal\HelperBundle\Controller\Controller as Controller;
use Portal\UserBundle\Entity\User;
use Portal\UserBundle\Entity\RoleToUser;
use Portal\AdminBundle\Form\UserFormType;
use Portal\AdminBundle\Form\RoleToUserFormType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\DBALException;

class UserController extends Controller
{
    const PER_PAGE = 10;

    public function viewAllAction()
    {
        return $this->render('PortalAdminBundle:UserAdmin:viewAll.html.twig', [
            'users' => $this->getUserManager()->getUsersByRole('ROLE_OPERATOR')
        ]);
    }

    /**
     * @param $userId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editAction($userId)
    {
        $request = $this->get("request_stack")->getCurrentRequest();

        $newWorker = false;

        $worker = $this->getUserManager()->findOneById($userId);

        if (!$worker instanceof User) {
            $worker = new User();
            $newWorker = true;
            $validation_groups = ['add'];
        } else {
            $validation_groups = ['edit'];
        }

//        $user = $this->getUser();
        $context = $this->get('security.authorization_checker');
        // TO DO: get isntances for user
        if ($context->isGranted("ROLE_SUPER_ADMIN")) {
            $instanceList = $this->getInstanceManager()->findAll();
        } else {
            $instanceList = $this->getInstanceManager()->findAll();
        };

        $form = $this->createForm(UserFormType::class, $worker, [
            'newUser' => $newWorker,
            'instanceList' => $instanceList,
            'validation_groups' => $validation_groups,
        ]);

        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));
            $validationErrors = $this->get('validator')->validate($form);
            if ($form->isSubmitted() && count($validationErrors) == 0) {
                //system
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($worker);

                $worker = $form->getData();

                $userManager = $this->get('fos_user.user_manager');
                if ($newWorker) {
                    $password = $encoder->encodePassword($form->getData()->getPassword(), $worker->getSalt());
                    $worker->setPassword($password);
                    $worker->setEnabled(TRUE);

                    $worker->addRole('ROLE_OPERATOR');
                }

                // Update User
                $userManager->updateUser($worker);

                // Set Roles
                foreach ($worker->getUserRoles() as $userRole) {
                    $userRole->setUser($worker);
                    $this->getEm()->persist($userRole);
                }
                $this->getEm()->flush();

                $session = $this->get('session');
                $session->getFlashBag()->add('message', $this->get('translator')->trans('successfully_save', array(), 'messages'));

                return $this->redirect($this->generateUrl('admin_admin_user_edit',
                    array('userId' => $worker->getId())
                ));
            }
        }

        return $this->render('PortalAdminBundle:UserAdmin:edit.html.twig',
            [
                'form' => $form->createView(),
                'add' => $newWorker,
                'userId' => $userId,
                'dataMultipleSelectPlaceholder' => $this->get('translator')->trans('make_some_choices'),
                'dataSingleSelectPlaceholder' => $this->get('translator')->trans('make_your_choice'),
            ]
        );
    }

    /**
     * remove User
     *
     * @param integer $userId
     * @return JsonResponse
     */
    public function removeAction($userId)
    {
        // Response
        $resultResponse = array(
            "status" => false,
            "message" => "",
            "redirectUrl" => ''
        );

        // check for token && check for user
        $currentUser = $this->get('user_helper')->getCurrentUser();

        $em = $this->getDoctrine()->getManager();

        $worker = $this->getUserManager()->findOneById($userId);

        $authChecker = $this->get('security.authorization_checker');

        if (!$authChecker->isGranted("ROLE_SUPER_ADMIN")) {
            if (!$authChecker->isGranted("ROLE_ADMIN")) {
                $resultResponse['status'] = false;
                $resultResponse['message'] = $this->get('translator')->trans('users_form.not_granted', array(), 'messages');
                return new JsonResponse($resultResponse);
            }
        }

        // check for exists
        if (!$worker instanceof User) {
            $resultResponse['message'] = $this->get('translator')->trans('users_form.already_deleted', array(), 'messages');
        } else {
            try {
                $em->remove($worker);
                $em->flush();
                $resultResponse['message'] = $this->get('translator')->trans('users_form.successfully_delete', array(), 'messages');
                $resultResponse['status'] = true;
            } catch (DBALException $e) {
                $resultResponse['message'] = $this->get('translator')->trans('users_form.error_remove', array(), 'messages');
            }
        }

        return new JsonResponse($resultResponse);
    }

    /**
     * @param $roleIndex
     * @return JsonResponse
     */
    public function addNewRoleFormAction($roleIndex)
    {
        // role to user
        $roleToUser = new RoleToUser();

        $form = $this->createForm(RoleToUserFormType::class, $roleToUser);

        // Content
        $content = $this->render('PortalAdminBundle:UserAdmin/part:_role.html.twig', [
            'roleForm' => $form->createView(),
            'roleToUserid' => 0
        ])->getContent();
        // Replace admin_role2user[role] TO user_form[userRoles][0][role]
        $content = str_replace("admin_role2user[role]", "user_form[userRoles][{$roleIndex}][role]", $content);
        // Replace admin_role2user[instance] TO user_form[userRoles][0][instance]
        $content = str_replace("admin_role2user[instance]", "user_form[userRoles][{$roleIndex}][instance]", $content);
        // Replace admin_role2user_role TO user_form_userRoles_0_role
        $content = str_replace("admin_role2user_role", "user_form_userRoles_{$roleIndex}_role", $content);
        // Replace admin_role2user_instance TO user_form_userRoles_0_instance
        $content = str_replace("admin_role2user_instance", "user_form_userRoles_{$roleIndex}_instance", $content);
        // Replace admin_role2user_id to user_form_userRoles_1_id
        $content = str_replace("admin_role2user_id", "user_form_userRoles_{$roleIndex}_id", $content);
        $response = new JsonResponse([
            'status' => true,
            'content' => $content
        ]);
        return $response;
    }

    /**
     * @return JsonResponse
     */
    public function removeRoleFormItemAction()
    {
        $request = $this->get("request_stack")->getCurrentRequest();
        $id = (int)$request->request->get("id");
        if ($id > 0) {
            $roleToUser = $this->getUserRoleManager()->findOneRoleToUserById($id);
            if ($roleToUser instanceof RoleToUser) {
                $this->getEm()->remove($roleToUser);
                $this->getEm()->flush();
            }
        }

        return new JsonResponse([
            'status' => true
        ]);
    }
}
