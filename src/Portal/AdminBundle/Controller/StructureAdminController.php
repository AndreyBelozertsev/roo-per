<?php

namespace Portal\AdminBundle\Controller;

use Pagerfanta\Adapter\DoctrineCollectionAdapter;
use Portal\ContentBundle\Entity\Menu;
use Portal\ContentBundle\Entity\MenuNode;
use Portal\HelperBundle\Helper\PortalHelper;
use Symfony\Component\HttpFoundation\Request;
use Portal\AdminBundle\Form\StructureAdminType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Doctrine\DBAL\DBALException;
use Pagerfanta\Pagerfanta;

/**
 * Structure controller.
 *
 */
class StructureAdminController extends Controller
{
    /**
     * Lists all.
     * @param Request $request
     * @param $instanceCode
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, $instanceCode)
    {
        $isGranted = $this->get('user_manager')->isGranted(
            MenuNode::PERMISSIONS_STRUCTURE['edit'],
            $instanceCode,
            $this->get('user_helper')->getCurrentUser()->getId()
        );
        if (!$isGranted) {

            throw $this->createAccessDeniedException($this->get('translator')->trans('error_page.text_403'));
        }

        return $this->render('PortalAdminBundle:Structure:index.html.twig', [
            'instanceCode' => $instanceCode,
            'ref' => $request->get('ref', false)
        ]);
    }

    /**
     * Creates a new structure element.
     * @param Request $request
     * @param $instanceCode
     *
     * @return JsonResponse
     */
    public function newAction(Request $request, $instanceCode)
    {
        $menuNode = new MenuNode();
        $node = $request->request->all();
        $form = $this->createForm(StructureAdminType::class, $menuNode);
        $form->handleRequest($request);
        $status = true;

        if (!empty($node) && $form->isValid()) {
            $em = $this->getDoctrine()->getManager('customer');
            if ($menuNode->getMenu()->getCode() === Menu::STRUCTURE_MENU) {
                $parentId = $menuNode->getParent() == 0 ? null : $menuNode->getParent();
                $this->get('customer_menu_node_manager')->resortingStructure($parentId);
            }
            $menuNode->setParent($em->find(MenuNode::class, $menuNode->getParent()));
            $em->persist($menuNode);
            $em->flush();
        } else {
            $status = false;
        }

        if (empty($node)) {
            $status = true;
        }

        return new JsonResponse([
            'status' => $status,
            'content' => $this->render('PortalAdminBundle:Structure:new.html.twig', [
                'bloc' => $menuNode,
                'form' => $form->createView(),
                'instanceCode' => $instanceCode
            ])->getContent(),
            'selected_node' => $menuNode->getId()
        ]);
    }

    /**
     * Lists all structure.
     *
     */
    public function getStructureAction()
    {
        $em = $this->getDoctrine()->getManager('customer');
        $structMenu = $em->getRepository('PortalContentBundle:MenuNode')->getStructureMenu();
        if (empty($structMenu)) {

            return new JsonResponse([
                'status' => false,
                'message' => $this->get('translator')->trans('bloc_structure.not_data')
            ]);
        }
        $structure = [];
        foreach ($structMenu as $item) {
            $css = ($item['is_hidden'] ? 'trans' : '') . ($item['is_published'] ? '' : ' unpub') . ($item['is_separator'] ? ' line' : '');
            array_push($structure, [
                'id' => $item['id'],
                'parent' => $item['parent_id'] ?: '#',
                'text' => $item['title'] . ($item['is_deleted'] ?
                    $this->render('PortalAdminBundle:Structure:deletedItem.html.twig', ['itemId' => $item['id']])->getContent() :
                    ''
                ),
                'template' => $item['structure_template_code'],
                'state' => $item['is_deleted'] ? ['disabled' => true] : null,
                'a_attr' => ['class' => $css]
            ]);
        }

        return new JsonResponse(['status' => true, 'content' => $structure]);
    }

    /**
     * Move structure.
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function moveStructureAction(Request $request)
    {
        $params[] = $request->request->all();
        try {
            $this->get('customer_menu_node_manager')->moveCategory($params);
        } catch (DBALException $e) {

            return new JsonResponse([
                'status' => false,
                'message' => $this->get('translator')->trans('bloc_structure.not_move')
            ]);
        }

        return new JsonResponse([
            'status' => true,
            'message' => $this->get('translator')->trans('bloc_structure.move_complite')
        ]);
    }

    /**
     * Rename structure.
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function renameStructureAction(Request $request)
    {
        $params = $request->request->all();
        try {
            if (isset($params['id']) && isset($params['oldName']) && isset($params['newName'])) {
                $structureId = (int)$params['id'];
                $newName = htmlspecialchars($params['newName']);
                $em = $this->getDoctrine()->getManager('customer');
                $em->getRepository('PortalContentBundle:MenuNode')->renameStrcture($structureId, $newName);
            }
        } catch (Exception $e) {

            return new JsonResponse([
                'status' => false,
                'message' => $this->get('translator')->trans('bloc_structure.request_error')
            ]);
        }

        return new JsonResponse(['status' => true]);
    }

    /**
     * Deletes a bloc entity.
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function deleteStructureAction(Request $request)
    {
        $instanceCode = $this->getParameter('instance_code');
        $isGranted = $this->get('user_manager')->isGranted(
            MenuNode::PERMISSIONS_STRUCTURE['delete'],
            $instanceCode,
            $this->get('user_helper')->getCurrentUser()->getId()
        );
        if ($isGranted) {
            $status = true;
            $id = (int)$request->request->get('id') ?: $request->get('id');
            try {
                $this->get('customer_menu_node_manager')->deleteNode($id);
            } catch (\Exception $e) {
                $status = false;
                $errMsg = $this->get('translator')->trans('bloc_structure.request_error');
            }
        } else {
            $status = false;
            $errMsg = $this->get('translator')->trans('error_page.text_403');
        }

        return new JsonResponse([
            'status' => $status,
            'message' => $errMsg ?? '',
            'redirectUrl' => $this->generateUrl('admin_instance_structure_index', [
                'instanceCode' => $instanceCode
            ])
        ]);
    }

    public function restoreStructureAction(Request $request)
    {
        $status = true;
        try {
            $this->get('customer_menu_node_manager')->restoreMenuNode($request->request->get('nodeId'));
        } catch (\Exception $e) {
            $status = false;
            $errMessage = $this->get('translator')->trans('bloc_structure.request_error');
        }

        return new JsonResponse([
            'status' => $status,
            'message' => $errMessage ?? ''
        ]);
    }

    public function getListContentAction(Request $request, $instanceCode)
    {
        $arrParams['status'] = false;
        try {
            $id = (int)$request->get('id');
            $block = $this->getDoctrine()->getManager('customer')->getRepository(MenuNode::class)->find($id);
            if ($block instanceof MenuNode) {
                $template = $block->getStructureTemplate() ? $block->getStructureTemplate()->getCode() : null;
                if ($template === 'head') {
                    $items['heads'] = $this->get('customer_head_manager')->getAdminStructureHeadList($id);
                } else {
                    if (!$block->getDocuments()->isEmpty()) {
                        $adapter = new DoctrineCollectionAdapter($block->getDocuments());
                        $pagerfantaDocument = new Pagerfanta($adapter);
                        $pagerfantaDocument->setMaxPerPage(PortalHelper::MAX_PER_PAGE_PAGINATION);
                        $pagerfantaDocument->setNormalizeOutOfRangePages(true);
                        $pagerfantaDocument->setCurrentPage($request->get('page', 1));
                    }
                    $items['pages'] = $block->getPages();
                    $items['events'] = $block->getEvents();
                    $items['quizzes'] = $block->getQuizzes();
                    $items['articles'] = $block->getArticles();
                    $items['materials'] = $block->getMaterials();
                    $items['feedbacks'] = $block->getFeedBacks();
                    $items['interviews'] = $block->getInterviews();
                    $items['photo_reports'] = $block->getPhotoReports();
                    $items['video_reports'] = $block->getVideoReports();
                }
                $arrParams['status'] = true;
                $arrParams['content'] = $this->render('PortalAdminBundle:Structure:content_structure.html.twig', [
                    'itemsStructure' => $items,
                    'instanceCode' => $instanceCode,
                    'pagerfantaDocument' => $pagerfantaDocument ?? null
                ])->getContent();
                $arrParams['buttons'] = $this->render('PortalAdminBundle:Structure:additional_buttons.html.twig', [
                    'instanceCode' => $instanceCode,
                    'structureId' => $id,
                    'template' => $template
                ])->getContent();
            }
        } catch (DBALException $e) {
            $arrParams['message'] = $this->get('translator')->trans('unexpected_error');
        }

        return new JsonResponse($arrParams);
    }
}
