<?php

namespace Portal\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Portal\HelperBundle\Helper\PortalHelper;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class TypoReportAdminController extends Controller
{
    public function viewAllAction(Request $request, $instanceCode)
    {
        $adapter = new ArrayAdapter($this->get('customer_typo_report_manager')->findAll());
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(PortalHelper::MAX_PER_PAGE_PAGINATION);
        $pagerfanta->setNormalizeOutOfRangePages(true);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        return $this->render('PortalAdminBundle:TypoReport:list.html.twig', [
            'pagerFanta' => $pagerfanta,
            'instanceCode' => $instanceCode
        ]);
    }
}
