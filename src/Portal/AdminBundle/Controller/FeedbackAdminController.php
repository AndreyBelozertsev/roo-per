<?php

namespace Portal\AdminBundle\Controller;

use Portal\AdminBundle\Form\FeedbackType;
use Portal\ContentBundle\Entity\Feedback;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class FeedbackAdminController extends Controller
{
    /**
     * View all social Network
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $feedbackList = $this->get('feedback_manager')->findAll();

        return $this->render('PortalAdminBundle:FeedbackAdmin:list.html.twig', [
            'feedbackList' => $feedbackList
        ]);
    }

    /**
     * Create new social Network
     *
     * @param Request $request
     * @param $instanceCode
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request, $instanceCode)
    {
        $feedback = new Feedback();
        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($feedback);
            $em->flush();

            return $this->redirectToRoute('admin_instance_feedback_edit', [
                'id' => $feedback->getId(),
                'instanceCode' => $instanceCode
            ]);
        }

        return $this->render('PortalAdminBundle:FeedbackAdmin:create.html.twig', [
            'feedback' => $feedback,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit social Network
     *
     * @param Request $request
     * @param $id
     * @param $instanceCode
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id, $instanceCode)
    {
        $em = $this->getDoctrine()->getManager();
        $feedback = $this->get('feedback_manager')->findOneById($id);
        if (!$feedback instanceof Feedback) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('error_page.text_404')
            );
        }

        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->merge($feedback);
            $em->flush();

            return $this->redirectToRoute('admin_instance_feedback_edit', [
                'id' => $feedback->getId(),
                'instanceCode' => $instanceCode]);
        }

        return $this->render('PortalAdminBundle:FeedbackAdmin:edit.html.twig', [
            'feedback' => $feedback,
            'form' => $form->createView()
        ]);
    }

}
