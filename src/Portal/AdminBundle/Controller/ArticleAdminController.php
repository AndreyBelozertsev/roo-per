<?php

namespace Portal\AdminBundle\Controller;

use Doctrine\DBAL\DBALException;
use Portal\ContentBundle\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Portal\HelperBundle\Helper\PortalHelper;
use Portal\AdminBundle\Form\ArticleType;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class ArticleAdminController extends Controller
{
    public function listAction(Request $request, $cat)
    {
        $isSuperAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN');
        $permissionsArray = array_intersect($this->get('permissions')->getCode(), Article::PERMISSIONS_ARTICLE);
        if (count($permissionsArray) === 0 && !$isSuperAdmin) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $filterParam = $request->query->all();
        $articleList = $this->get('customer_article_manager')->getAllArticleForPagination($filterParam, $cat);
        $adapter = new ArrayAdapter($articleList);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(PortalHelper::MAX_PER_PAGE_PAGINATION);
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        return $this->render('PortalAdminBundle:ArticleAdmin:list.html.twig', [
            'pagerFanta' => $pagerfanta,
            'filterParams' => $filterParam,
            'users' => $this->getUsersArray()
        ]);
    }

    public function createAction(Request $request, $cat, $instanceCode)
    {
        $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
        $isGranted = $this->get('user_manager')->isGranted(
            Article::PERMISSIONS_ARTICLE['create'],
            $instanceCode,
            $currentUserId
        );
        if (!$isGranted) {
            throw $this->createAccessDeniedException(
                $this->get('translator')->trans('error_page.text_403')
            );
        }

        $article = new Article();
        if ($request->get('menuNodeId')) {
            $article->setMenuNode($this->get('customer_menu_node_manager')->find($request->get('menuNodeId')));
        }

        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('PortalContentBundle:ArticleCategory')->find($cat);

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                try {
                    $article->setAuthor($currentUserId);
                    $article->setCategory($category);

                    $em->persist($article);
                    $em->flush();
                    $id = $article->getId();

                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

                    return $this->redirectToRoute('admin_instance_article_form_edit', [
                        'id' => $id,
                        'cat' => $cat
                    ]);
                } catch (DBALException $e) {
//                    $flashBag->add('error_message', $e->getMessage());
                    $flashBag->add('error_message', $this->get('translator')->trans('query_error'));
                }
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }

        return $this->render('PortalAdminBundle:ArticleAdmin:create.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
            'category' => $category,
        ]);
    }

    public function editAction(Request $request, $id, $cat)
    {
        $em = $this->getDoctrine()->getManager();

        $article = $this->get('article_manager')->findOneById($id);
        if (!$article instanceof Article || $article->getIsDeleted()) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('error_page.text_404')
            );
        }

        $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
        $category = $em->getRepository('PortalContentBundle:ArticleCategory')->find($cat);

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                try {
                    $article->setCategory($category);
                    $article->setAuthor($currentUserId);

                    $em->persist($article);
                    $em->flush();
                    $id = $article->getId();

                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

                    return $this->redirectToRoute('admin_instance_article_form_edit', [
                        'id' => $id,
                        'cat' => $cat
                    ]);
                } catch (DBALException $e) {
                    $flashBag->add('error_message', $this->get('translator')->trans('query_error'));
//                    $flashBag->add('error_message', $e->getMessage());
                }
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }

        return $this->render('PortalAdminBundle:ArticleAdmin:edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
            'category' => $category,
        ]);
    }

    public function deleteAction(Request $request, $id, $cat)
    {
        $article = $this->get('article_manager')->findOneById($id);
        if ($article instanceof Article) {
            $currentUserId = $this->get('user_helper')->getCurrentUser()->getId();
            $isAuthor = ($article->getAuthor() === $currentUserId);
            if ($isAuthor) {
                $article->setIsDeleted(true);
                $em = $this->getDoctrine()->getManager();
                $em->persist($article);
                $em->flush();

                $page = [];
                if ($request->query->get('page') !== null) {
                    $page['page'] = (int)$request->query->get('page');
                }
                $response['redirectUrl'] = $this->get('router')->generate('admin_instance_article_list', [
                    'cat' => $cat,
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
        $article = $this->get('article_manager')->findOneById($id);
        if ($article instanceof Article) {
            $isGranted = $this->get('user_manager')->isGranted(
                Article::PERMISSIONS_ARTICLE['restore'],
                $instanceCode,
                $this->get('user_helper')->getCurrentUser()->getId()
            );
            if ($isGranted) {
                $em = $this->getDoctrine()->getManager();
                $article->setIsDeleted(false);
                $em->persist($article);
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
