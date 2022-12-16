<?php

namespace Portal\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Portal\ContentBundle\Entity\Typo;
use Portal\ContentBundle\Form\TypoReportFormType;

class TypoReportController extends Controller
{
    public function reportAction(Request $request)
    {
        $typo = new Typo();
        $form = $this->createForm(TypoReportFormType::class, $typo);
        $form->handleRequest($request);
        if ($request->getMethod() === 'POST' && $form->isValid()) {
            $em = $this->getDoctrine()->getManager('customer');
            $em->persist($typo);
            $em->flush();
            $arrParam['status'] = true;
            $arrParam['message'] = $this->get('translator')->trans('successfully_save');
        } else {
            $arrParam['message'] = $this->get('translator')->trans('wrong_captcha') . 'zzz';
        }
        $arrParam['form'] = $this->render('PortalContentBundle:Typo:typoForm.html.twig', [
            'form' => $form->createView()
        ])->getContent();

        return new JsonResponse($arrParam);
    }
}
