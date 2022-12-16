<?php

namespace Portal\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Portal\ContentBundle\Entity\Head;
use Symfony\Component\HttpFoundation\Request;

class HeadController extends Controller
{
    public function showAction(Request $request)
    {
        $instanceCode = $this->get('kernel')->getInstance();
        if ($instanceCode !== null) {
            $arrParams['isDepartment'] = true;
        } else {
            $arrParams['isDepartment'] = false;
            $instanceCode = $this->getParameter('instance_code');
        }
//        $arrParams['depCode'] = $instanceCode;
        $arrParams['instanceCode'] = $instanceCode;
        $arrParams['depTitle'] = $this->get('instance_manager')->getPageTitle($instanceCode);

        if ($request->get('slug')) {
            $param['slug'] = $request->get('slug');
        } else {
            $param['id'] = (int)$request->get('id');
        }
        $objHead = $this->get('customer_head_manager')->findOneBy($param);
        if (!$objHead instanceof Head || !$objHead->getIsPublished() || $objHead->getIsDeleted()) {

            throw $this->createNotFoundException($this->get('translator')->trans('no_head'));
        }
        $arrParams['head'] = $objHead;

        return $this->render('PortalContentBundle:Head:show.html.twig', $arrParams);
    }

    public function viewAllAction()
    {
        $instanceCode = $this->getParameter('instance_code');

        return $this->render('PortalContentBundle:Head:list.html.twig', [
            'ignoreTime' => date('Y-m-d H:i:s'),
            'site_name' => $this->getParameter('site_name'),
//            'depCode' => $instanceCode,
            'depTitle' => $this->get('instance_manager')->getPageTitle($instanceCode),
            'headList' => $this->get('customer_head_manager')->getHeadList()
        ]);
    }
}
