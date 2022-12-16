<?php

namespace Portal\AdminBundle\Controller;

use Portal\AdminBundle\Form\UserPermissionFormType;
use Portal\HelperBundle\Controller\Controller as Controller;
use Portal\UserBundle\Entity\Permission;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserPermissionController extends Controller
{
    const PER_PAGE = 10;
    
    public function viewAllAction()
    {
        $permissionsList = $this->getUserPermissionManager()->getPermissionsList();

        return $this->render('PortalAdminBundle:UserPermissionAdmin:viewAll.html.twig', [
            'permissionsList' => $permissionsList
        ]);
    }

    /**
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editAction($id)
    {
        $request = $this->get("request_stack")->getCurrentRequest();
        $session = $request->getSession();

        // Default
        $validation_groups = ['edit'];

        // Init Permission
        $permission = $this->getUserPermissionManager()->findOneById($id);

        if ($permission instanceof Permission) {
            if ($permission->getIsSystem()) {
                // Cannot be editable
                return $this->redirectToRoute('admin_admin_permission_viewall');
            }
        } else {
            // New
            $permission = new Permission();
            $validation_groups = ['add'];
        }

        $form = $this->createForm(UserPermissionFormType::class, $permission, [
            'validation_groups' => $validation_groups,
        ]);

        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));
            $validationErrors = $this->get('validator')->validate($form);
            if ($form->isSubmitted() && count($validationErrors) == 0) {
                $permission = $form->getData();
                if ((int)$id == 0) {
                    $permission->setIsSystem(0); // for new
                }
                $this->getEm()->persist($permission);
                $this->getEm()->flush();

                // Success Messaeg
                $session->getFlashBag()->add('message', $this->get('translator')->trans('successfully_save'));

                return $this->redirect($this->generateUrl('admin_admin_permission_edit', [
                    'id' => $permission->getId()
                    ]
                ));
            }
        }

        return $this->render('PortalAdminBundle:UserPermissionAdmin:edit.html.twig',
            [
                'form' => $form->createView(),
                'id' => $id
            ]
        );
    }

    /**
     * remove
     *
     * @param integer $id
     * @return JsonResponse
     */
    public function removeAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        // Init Permission
        $permission = $this->getUserPermissionManager()->findOneById($id);
        if ($permission instanceof Permission) {
            if ($permission->getIsSystem()) {
                // Cannot be droppable
                return new JsonResponse([
                    "status" => false,
                    "message" => $this->get('translator')->trans('users_form.not_granted', array(), 'messages'),
                    "redirectUrl" => ''
                ]);
            }
        } else {
            return new JsonResponse([
                "status" => false,
                "message" => $this->get('translator')->trans('users_form.already_deleted', array(), 'messages'),
                "redirectUrl" => ''
            ]);
        }

        $authChecker = $this->container->get('security.authorization_checker');
        if (!$authChecker->isGranted("ROLE_SUPER_ADMIN") || !$authChecker->isGranted("ROLE_ADMIN")) {
            return new JsonResponse([
                "status" => false,
                "message" => $this->get('translator')->trans('users_form.not_granted', array(), 'messages'),
                "redirectUrl" => ''
            ]);
        }

        try {
            $em->remove($permission);
            $em->flush();
        } catch (DBALException $e) {
            return new JsonResponse([
                "status" => false,
                "message" => $this->get('translator')->trans('users_form.error_remove', array(), 'messages'),
                "redirectUrl" => ''
            ]);
        }

        return new JsonResponse([
            "status" => true,
            "message" => $this->get('translator')->trans('users_form.successfully_delete', array(), 'messages'),
            "redirectUrl" => ''
        ]);
    }
}
