<?php

namespace Portal\AdminBundle\Controller;

use Doctrine\DBAL\DBALException;
use Portal\AdminBundle\Form\ArticleCategoryType;
use Portal\ContentBundle\Entity\ArticleCategory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CategoryAdminController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('PortalAdminBundle:ArticleCategoryAdmin:list.html.twig', [
            'category' => $em->getRepository('PortalContentBundle:ArticleCategory')->findAll()
        ]);
    }

    public function createAction(Request $request)
    {
        $category = new ArticleCategory();
        $form = $this->createForm(ArticleCategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($category);
                    $em->flush();

                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

                    return $this->redirectToRoute('admin_admin_category_edit', [
                        'id' => $category->getId()
                    ]);
                } catch (DBALException $e) {
//                    $flashBag->add('error_message', $e->getMessage());
                    $flashBag->add('error_message', $this->get('translator')->trans('query_error'));
                }
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }

        return $this->render('PortalAdminBundle:ArticleCategoryAdmin:create.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }

    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('PortalContentBundle:ArticleCategory')->findOneBy(['id' => $id]);

        if (!$category instanceof ArticleCategory) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('error_page.text_404')
            );
        }

        $form = $this->createForm(ArticleCategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($category);
                    $em->flush();

                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

                    return $this->redirectToRoute('admin_admin_category_edit', [
                        'id' => $category->getId()
                    ]);
                } catch (DBALException $e) {
//                    $flashBag->add('error_message', $e->getMessage());
                    $flashBag->add('error_message', $this->get('translator')->trans('query_error'));
                }
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }

        return $this->render('PortalAdminBundle:ArticleCategoryAdmin:create.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }
    
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('PortalContentBundle:ArticleCategory')->find($id);
        if ($category instanceof ArticleCategory) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();

            $response['status'] = true;
            $response['redirectUrl'] = $this->get('router')->generate('admin_admin_category_viewall');
        } else {
            $response['message'] = $this->get('translator')->trans('can_not_delete');
        }

        return new JsonResponse($response);
    }
}
