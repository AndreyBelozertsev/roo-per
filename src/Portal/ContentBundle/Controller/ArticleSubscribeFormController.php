<?php

namespace Portal\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Portal\ContentBundle\Form\ArticleSubscribeFormType;
use Portal\ContentBundle\Entity\ArticleSubscribe;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\DBALException;

class ArticleSubscribeFormController extends Controller
{
    public function subscribeAction(Request $request)
    {
        $arrJson['status'] = false;
        $instanceCode = $this->getParameter('instance_code');
        $instance = $this->get('instance_manager')->findOneBy(['code' => $instanceCode]);

        $subscribe = new ArticleSubscribe();
        $form = $this->createForm(ArticleSubscribeFormType::class, $subscribe, ['instanceId' => $instance->getId()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $subscribe->setInstance($instance);

                $em = $this->getDoctrine()->getManager();
                $em->persist($subscribe);
                $em->flush();

                $arrJson['status'] = true;
                $arrJson['content'] = $this->render('PortalContentBundle:ArticleSubscribe:article_unsubscribe_form.html.twig', [
                    'form' => $form->createView()
                ])->getContent();

            } catch (DBALException $e) {
                $arrJson['error'] = $this->get('translator')->trans('query_error');
            }
        } else {
            $arrJson['content'] = $this->render('PortalContentBundle:ArticleSubscribe:article_subscribe_form.html.twig', [
                'form' => $form->createView()
            ])->getContent();
        }

        return new JsonResponse($arrJson);
    }

    public function unsubscribeAction($hash, $instanceId)
    {
        $where['uid'] = $hash;
        if ((int)$instanceId !== 0) {
            $where['instance'] = (int)$instanceId;
        }
        $subs = $this->get('article_subscribe_manager')->findBy($where);

        $em = $this->getDoctrine()->getManager();
        foreach ($subs as $sub) {
            $em->remove($sub);
        }
        $em->flush();

        return $this->render('PortalContentBundle:ArticleSubscribe:article_unsubscribe.html.twig', [
            'subs' => $subs
        ]);
    }
}
