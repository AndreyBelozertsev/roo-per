<?php

namespace Portal\AdminBundle\Controller;

use Doctrine\DBAL\DBALException;
use Portal\HelperBundle\Controller\Controller as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Portal\ContentBundle\Entity\InstanceCategory;
use Portal\AdminBundle\Form\InstanceCategoryFormType;
use Portal\HelperBundle\Helper\PortalHelper;

class InstanceCategoryAdminController extends Controller
{   
    public function viewAllAction()
    {
        $currentUser = $this->getUserHelper()->getCurrentUser();
        
        $listInstanceCategory = $this->getInstanceCategoryManager()->findAll();
        return $this->render('PortalAdminBundle:InstanceCategoryAdmin:viewAll.html.twig', array('listInstanceCategory' => $listInstanceCategory));
    }
    
    public function editAction(Request $request, $instanceCategoryId)
    {
        $newInstanceCategory = false;
        $currentUser = $this->container->get('user_helper')->getCurrentUser();

        $instanceCategory = $this->getInstanceCategoryManager()->findOneById($instanceCategoryId);
        if (!$instanceCategory instanceof InstanceCategory) {
            $instanceCategory = new InstanceCategory();
            $newInstanceCategory = true;
            $validation_groups = ['add'];
        } else {
            $validation_groups = ['edit'];
        }

        $form = $this->createForm(InstanceCategoryFormType::class, $instanceCategory, [
            'newInstanceCategory' => $newInstanceCategory,
            'validation_groups' => $validation_groups,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session = $request->getSession();
            
            $slug = PortalHelper::slug($instanceCategory->getTitle());
            $instanceCategory->setSlug($slug);
            
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($instanceCategory);
            $em->flush();
            
            $session->getFlashBag()->add('message', $this->get('translator')->trans('instance_category_form.successfully_save', array(), 'messages'));

            return $this->redirect($this->generateUrl('admin_admin_instance_category_edit',
                array('instanceCategoryId' => $instanceCategory->getId())
            ));
        }

        return $this->render('PortalAdminBundle:InstanceCategoryAdmin:edit.html.twig',
            array('form' => $form->createView(), 'add' => $newInstanceCategory, 'instanceCategoryId' => $instanceCategoryId));
    }
    
    /**
     * remove instanceCategory
     *
     * @param Request $request
     * @param integer $instanceCategoryId
     * @return JsonResponse
     */
    public function removeAction(Request $request, $instanceCategoryId)
    {
        // Response
        $resultResponse = array(
            "status" => false,
            "message" => "",
            "redirectUrl" => $request->headers->get('referer')
        );

        $em = $this->getDoctrine()->getManager();

        $session = $request->getSession();
        $instanceCategory = $this->getInstanceCategoryManager()->findOneById($instanceCategoryId);
        
        $authChecker = $this->container->get('security.authorization_checker');

        if (!$authChecker->isGranted("ROLE_SUPER_ADMIN")) {
            $resultResponse['status'] = false;
            $resultResponse['message'] = $this->get('translator')->trans('instance_form.not_granted', array(), 'messages');
            return new JsonResponse($resultResponse);
        }

        // check for exists
        if (!$instanceCategory instanceof InstanceCategory) {
            $resultResponse['message'] = $this->get('translator')->trans('instance_form.already_deleted', array(), 'messages');
        } else {
            try {
                $em->remove($instanceCategory);
                $em->flush();

                $resultResponse['message'] = $this->get('translator')->trans('instance_form.successfully_delete', array(), 'messages');
            } catch (DBALException $e) {
                $resultResponse['message'] = $this->get('translator')->trans('instance_form.error_remove', array(), 'messages');
            }
            $resultResponse['status'] = true;
        }

        return new JsonResponse($resultResponse);
    }
}
