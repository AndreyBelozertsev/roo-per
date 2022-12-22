<?php

namespace Portal\AdminBundle\Controller;

use Doctrine\DBAL\DBALException;
use Portal\ContentBundle\Entity\MagazineArticle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Portal\HelperBundle\Helper\PortalHelper;
use Portal\AdminBundle\Form\MagazineArticleType;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class MagazineArticleAdminController extends Controller
{
    public function listAction(Request $request, $magazine)
    {
        $isSuperAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN');
        $permissionsArray = array_intersect($this->get('permissions')->getCode(), MagazineArticle::PERMISSIONS_ARTICLE);
        if (count($permissionsArray) === 0 && !$isSuperAdmin) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $filterParam = $request->query->all();
        $magazineArticleList = $this->get('customer_magazine_article_manager')->getAllMagazineArticleForPagination($filterParam, $magazine);
        $adapter = new ArrayAdapter($magazineArticleList);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(PortalHelper::MAX_PER_PAGE_PAGINATION);
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        $em = $this->getDoctrine()->getManager();
        $objMagazine = $em->getRepository('PortalContentBundle:MagazineNewspaper')->find($magazine);

        return $this->render('PortalAdminBundle:MagazineArticleAdmin:list.html.twig', [
            'pagerFanta' => $pagerfanta,
            'filterParams' => $filterParam,
            'users' => $this->getUsersArray(),
            'magazine' => $objMagazine
        ]);
    }

    public function createAction(Request $request, $magazine, $instanceCode)
    {
        $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
        $isGranted = $this->get('user_manager')->isGranted(
            MagazineArticle::PERMISSIONS_ARTICLE['create'],
            $instanceCode,
            $currentUserId
        );
        if (!$isGranted) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $magazineArticle = new MagazineArticle();
        if ($request->get('menuNodeId')) {
            $magazineArticle->setMenuNode($this->get('customer_menu_node_manager')->find($request->get('menuNodeId')));
        }

        $em = $this->getDoctrine()->getManager();
        $objMagazine = $em->getRepository('PortalContentBundle:MagazineNewspaper')->find($magazine);
        
        $form = $this->createForm(MagazineArticleType::class, $magazineArticle);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                try {
                    $magazineArticle->setAuthor($currentUserId);
                    $magazineArticle->setMagazine($objMagazine);

                    $em->persist($magazineArticle);
                    $em->flush();
                    $id = $magazineArticle->getId();

                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

                    return $this->redirectToRoute('admin_instance_magazine_article_form_edit', [
                        'id' => $id,
                        'magazine' => $magazine
                    ]);
                } catch (DBALException $e) {
//                    $flashBag->add('error_message', $e->getMessage());
                    $flashBag->add('error_message', $this->get('translator')->trans('query_error'));
                }
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }

        return $this->render('PortalAdminBundle:MagazineArticleAdmin:create.html.twig', [
            'form' => $form->createView(),
            'magazineArticle' => $magazineArticle,
            'magazine' => $objMagazine,
        ]);
    }

    public function editAction(Request $request, $id, $magazine)
    {
        $em = $this->getDoctrine()->getManager();

        $magazineArticle = $this->get('magazine_article_manager')->findOneById($id);
        if (!$magazineArticle instanceof MagazineArticle || $magazineArticle->getIsDeleted()) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('error_page.text_404')
            );
        }

        $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
        $objMagazine = $em->getRepository('PortalContentBundle:MagazineNewspaper')->find($magazine);

        $form = $this->createForm(MagazineArticleType::class, $magazineArticle);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                try {
                    $magazineArticle->setMagazine($objMagazine);
                    $magazineArticle->setAuthor($currentUserId);

                    $em->persist($magazineArticle);
                    $em->flush();
                    $id = $magazineArticle->getId();

                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

                    return $this->redirectToRoute('admin_instance_magazine_article_form_edit', [
                        'id' => $id,
                        'magazine' => $magazine
                    ]);
                } catch (DBALException $e) {
                    $flashBag->add('error_message', $this->get('translator')->trans('query_error'));
//                    $flashBag->add('error_message', $e->getMessage());
                }
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }

        return $this->render('PortalAdminBundle:MagazineArticleAdmin:edit.html.twig', [
            'form' => $form->createView(),
            'magazineArticle' => $magazineArticle,
            'magazine' => $objMagazine,
        ]);
    }

    public function deleteAction(Request $request, $id, $magazine)
    {
        $magazineArticle = $this->get('magazine_article_manager')->findOneById($id);
        if ($magazineArticle instanceof MagazineArticle) {
            $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
            $isAuthor = ($article->getAuthor() === $currentUserId);
            if ($isAuthor) {
                $magazineArticle->setIsDeleted(true);
                $em = $this->getDoctrine()->getManager();
                $em->persist($magazineArticle);
                $em->flush();

                $page = [];
                if ($request->query->get('page') !== null) {
                    $page['page'] = (int)$request->query->get('page');
                }
                $response['redirectUrl'] = $this->get('router')->generate('admin_instance_magazine_article_list', [
                    'magazine' => $magazine,
                    'page' => (int)$request->query->get('page') ?: null
                ]);
                $response['status'] = true;
            } else {
                $response['message'] = $this->get('translator')->trans('error_page.text_403');
            }
        } else {
            $response['message'] = $this->get('translator')->trans('unexpected_error');
        }

        return new JsonResponse($response);
    }

    public function restoreAction($id, $instanceCode)
    {
        $magazineArticle = $this->get('magazine_article_manager')->findOneById($id);
        if ($magazineArticle instanceof MagazineArticle) {
            $isGranted = $this->get('user_manager')->isGranted(
                MagazineArticle::PERMISSIONS_ARTICLE['restore'],
                $instanceCode,
                $this->get('user_helper')->getCurrentUser()->getId()
            );
            if ($isGranted) {
                $em = $this->getDoctrine()->getManager();
                $magazineArticle->setIsDeleted(false);
                $em->persist($magazineArticle);
                $em->flush();

                $response['status'] = true;
                $response['reload'] = true;
            } else {
                $response['message'] = $this->get('translator')->trans('error_page.text_403');
            }
        } else {
            $response['message'] = $this->get('translator')->trans('unexpected_error');
        }

        return new JsonResponse($response);
    }

    /**
     * @return array
     */
    protected function getUsersArray()
    {
        $users = [];
        foreach ($this->get('user_manager')->getUsersInfo() as $v) {
            $users[$v['id']] = $v['last_name'] . ' ' . $v['first_name'];
        }

        return $users;
    }
}
