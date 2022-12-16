<?php

namespace Portal\AdminBundle\Controller;

use Portal\AdminBundle\Form\UserRoleFormType;
use Portal\HelperBundle\Controller\Controller as Controller;
use Portal\UserBundle\Entity\DTO\RoleDTO;
use Portal\UserBundle\Entity\Permission;
use Portal\UserBundle\Entity\Role;
use Portal\UserBundle\Entity\RoleToPermission;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserRoleController extends Controller
{
    const PER_PAGE = 10;
    
    public function viewAllAction()
    {
        $roleList = $this->getUserRoleManager()->getRoleList();

        return $this->render('PortalAdminBundle:UserRoleAdmin:viewAll.html.twig', [
            'roleList' => $roleList
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
        $rolePermissionIds = [];
        // Init Role
        $role = $this->getUserRoleManager()->findOneById($id);

        if ($role instanceof Role) {
            $roleDTO = new RoleDTO($role);
            $rolePermissionIds = $roleDTO->getPermissionIds();
        } else {
            // New
            $role = new Role();
            $validation_groups = ['add'];
        }

        $form = $this->createForm(UserRoleFormType::class, $role, [
            'validation_groups' => $validation_groups,
        ]);

        // Permissions list
        $permissionsList = $this->getUserPermissionManager()->getPermissionsList();

        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));
            $validationErrors = $this->get('validator')->validate($form);
            if ($form->isSubmitted() && count($validationErrors) == 0) {
                $role = $form->getData();

                // Permissions
                $rolePermissions = $request->request->get("rolePermissions");

                // Update Role
                $this->getEm()->persist($role);
                $this->getEm()->flush();

                // Unset all permission
                $this->getUserRoleManager()->unsetPermissions($role->getId());

                // Add permissions
                if ($rolePermissions) {
                    foreach ($rolePermissions as $permissionId) {
                        $permission = $this->getUserPermissionManager()->findOneById($permissionId);

                        if ($permission instanceof Permission) {
                            $roleToPermission = new RoleToPermission();
                            $roleToPermission->setPermission($permission);
                            $roleToPermission->setRole($role);
                            $this->getEm()->persist($roleToPermission);
                            $this->getEm()->flush();
                        }
                    }
                }

                // Success Messaeg
                $session->getFlashBag()->add('message', $this->get('translator')->trans('successfully_save'));

                return $this->redirect($this->generateUrl('admin_admin_role_edit', [
                        'id' => $role->getId()
                    ]
                ));
            }
        }

        return $this->render('PortalAdminBundle:UserRoleAdmin:edit.html.twig',
            [
                'form' => $form->createView(),
                'id' => $id,
                'rolePermissionIds' => $rolePermissionIds,
                'permissionsList' => $permissionsList
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

        // Init Role
        $role = $this->getUserRoleManager()->findOneById($id);
        if (!$role instanceof Role) {
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
            $em->remove($role);
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
