<?php

namespace Portal\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Portal\ContentBundle\Entity\MenuNode;

class SiteMapController extends Controller
{
    public function indexAction()
    {
        $instanceCode = $this->get('kernel')->getInstance();
        if ($instanceCode !== null) {
            $depTitle = $this->get('instance_manager')->getPageTitle($instanceCode);
        }

        // crutch begin
        $em = $this->getDoctrine()->getManager('customer');
        $dataSet = $em->getRepository('PortalContentBundle:MenuNode')->getSiteMap();
        $prepDataSet = [];
        foreach ($dataSet as $v) {
            $prepDataSet[$v['id']] = $v;
        }
        $tree = [];
        foreach ($prepDataSet as $id => &$node) {
            if (isset($node['parent_id']) && isset($prepDataSet[$node['parent_id']])) {
                $prepDataSet[$node['parent_id']]['child'][$id] = &$node;
            } else {
                $tree[$id] = &$node;
            }
        }
        // crutch end

        return $this->render('PortalContentBundle:SiteMap:sitemap.html.twig', [
            'isDepartment' => $instanceCode,
            'depTitle' => $depTitle ?? false,
            'siteMap' => $this->render('PortalContentBundle:Menu:list.html.twig', ['child' => $tree])->getContent() // crutch
        ]);
    }

    public function getMapAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dataSet = $em->getRepository(MenuNode::class)->getTreeFromNode($request->request->get('route'));

        $prepDataSet = [];
        if (is_array($dataSet)) {
            foreach ($dataSet as $v) {
                $prepDataSet[$v['id']] = $v;
            }
        }
        $tree = [];
        foreach ($prepDataSet as $id => &$node) {
            if (isset($node['parent_id']) && isset($prepDataSet[$node['parent_id']])) {
                $prepDataSet[$node['parent_id']]['child'][$id] = &$node;
            } else {
                $tree[$id] = &$node;
            }
        }

        return new JsonResponse([
            'content' => $this->render('PortalContentBundle:Menu:list.html.twig', ['child' => $tree])->getContent()
        ]);
    }
}
