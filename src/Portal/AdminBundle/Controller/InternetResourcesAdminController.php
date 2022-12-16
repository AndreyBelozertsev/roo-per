<?php

namespace Portal\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Portal\ContentBundle\Entity\InternetResources;
use Portal\ContentBundle\Entity\Attachment;
use Portal\AdminBundle\Form\InternetResourcesFormType;
use Doctrine\DBAL\DBALException;

class InternetResourcesAdminController extends Controller
{
    public function indexAction($instanceCode)
    {
        return $this->render('PortalAdminBundle:InternetResourcesAdmin:index.html.twig', [
            'resources' => $this->get('customer_internet_resources_manager')->findAll(),
            'instanceCode' => $instanceCode
        ]);
    }

    public function editAction(Request $request, $id, $instanceCode)
    {
        $resource = $this->get('customer_internet_resources_manager')->findOneById($id);
        if (!$resource instanceof InternetResources) {
            $resource = new InternetResources();
        }

        $form = $this->createForm(InternetResourcesFormType::class, $resource);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                try {
                    $em = $this->getDoctrine()->getManager('customer');
                    $em->persist($resource);
                    $em->flush();
                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

                    return $this->redirectToRoute('admin_instance_internet_resources_index', [
                        'instanceCode' => $instanceCode
                    ]);

                } catch (DBALException $e) {
                    $flashBag->add('error_message', $this->get('translator')->trans('query_error'));
                }
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }

        return $this->render('PortalAdminBundle:InternetResourcesAdmin:edit.html.twig', [
            'form' => $form->createView(),
            'resource' => $resource,
            'instanceCode' => $instanceCode
        ]);
    }

    public function deleteAction($id, $instanceCode)
    {
        $resource = $this->get('customer_internet_resources_manager')->find($id);
        if ($resource instanceof InternetResources) {
            $em = $this->getDoctrine()->getManager('customer');
            $em->remove($resource);
            $em->flush();

            $response['status'] = true;
        } else {
            $response['status'] = false;
            $response['message'] = $this->get('translator')->trans('unexpected_error');
        }
        $response['redirectUrl'] = $this->get('router')->generate('admin_instance_internet_resources_index', [
            'instanceCode' => $instanceCode
        ]);

        return new JsonResponse($response);
    }
}
