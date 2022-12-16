<?php

namespace Portal\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Portal\ContentBundle\Entity\Page;
use Symfony\Component\HttpFoundation\Request;

class InformPageController extends Controller
{
    public function pageAction(Request $request)
    {
        if ($request->get('slug')) {
            $param['slug'] = $request->get('slug');
        } else {
            $param['id'] = (int)$request->get('id');
        }
        $page = $this->get('customer_page_manager')->findOneBy($param);
        if (!$page instanceof Page) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('error_page.text_404', [], 'messages')
            );
        }

        $id = $page->getId();
        $templateId = $page->getTemplateId();
        $templateName = Page::TEMPLATE_LIST[$templateId] ?? 'standart_template';

        return $this->render('PortalContentBundle:InformPage:' . $templateName . '.html.twig', [
            'page' => ['templateId' => $templateId, 'title' => $page->getTitle()],
            'template' => $this->get('customer_page_manager')->findTemplate($templateId, $id)
        ]);
    }
}
