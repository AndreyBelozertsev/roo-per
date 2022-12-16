<?php

namespace Portal\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Portal\ContentBundle\Entity\Param;
use Portal\AdminBundle\Form\ParamType;
use Doctrine\DBAL\DBALException;

class ParamAdminController extends Controller
{
    /**
     * @return Response
     */
    public function listAction()
    {
        $paramList = $this->getDoctrine()->getRepository('PortalContentBundle:Param')
            ->findBy([] ,['id' => 'ASC']);

        return $this->render('PortalAdminBundle:ParamAdmin:list.html.twig', [
            'params' => $paramList
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $param = $em->getRepository('PortalContentBundle:Param')->findOneBy(['id' => $id]);
        if (!$param instanceof Param) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('error_page.text_404')
            );
        }

        $form = $this->createForm(ParamType::class, $param);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $flashBag = $this->get('session')->getFlashBag();
            if ($form->isValid()) {
                try {
                    $em->persist($param);
                    $em->flush();

                    $flashBag->add('message', $this->get('translator')->trans('successfully_save'));

                    return $this->redirectToRoute('admin_admin_param_edit', ['id' => $id]);

                } catch (DBALException $e) {
                    $flashBag->add('error_message', $this->get('translator')->trans('query_error'));
                }
            } else {
                $flashBag->add('error_message', $this->get('translator')->trans('wrong_data'));
            }
        }

        return $this->render('PortalAdminBundle:ParamAdmin:edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
