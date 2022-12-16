<?php

namespace Portal\AdminBundle\Controller;

use Portal\AdminBundle\Form\SocialNetworkType;
use Portal\ContentBundle\Entity\SocialNetwork;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SocialNetworkAdminController extends Controller
{
    /**
     * View all social Network
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $socialNetworkList = $this->get('social_network_manager')->findAll();

        return $this->render('PortalAdminBundle:SocialNetworkAdmin:list.html.twig', [
            'socialNetworkList' => $socialNetworkList
        ]);
    }

    /**
     * Create new social Network
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $socialNetwork = new SocialNetwork();
        $form = $this->createForm(SocialNetworkType::class, $socialNetwork);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($socialNetwork);
            $em->flush();

            return $this->redirectToRoute('admin_admin_socialnetwork_edit', ['id' => $socialNetwork->getId()]);
        }

        return $this->render('PortalAdminBundle:SocialNetworkAdmin:create.html.twig', [
            'socialNetwork' => $socialNetwork,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit social Network
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $socialNetwork = $this->get('social_network_manager')->findOneById($id);
        if (!$socialNetwork instanceof SocialNetwork) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('error_page.text_404')
            );
        }

        $form = $this->createForm(SocialNetworkType::class, $socialNetwork);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->merge($socialNetwork);
            $em->flush();

            return $this->redirectToRoute('admin_admin_socialnetwork_list');
        }

        return $this->render('PortalAdminBundle:SocialNetworkAdmin:edit.html.twig', [
            'socialNetwork' => $socialNetwork,
            'form' => $form->createView()
        ]);
    }

    /**
     * Delete social Network
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function deleteAction(Request $request, $id)
    {
        $socialNetwork = $this->get('social_network_manager')->findOneById($id);
        if ($socialNetwork instanceof SocialNetwork) {
                $em = $this->getDoctrine()->getManager('customer');
                $em->remove($socialNetwork);
                $em->flush();

                $response['redirectUrl'] = $this->get('router')->generate('admin_admin_socialnetwork_list');
                $response['status'] = true;
        } else {
            $response['message'] = $this->get('translator')->trans('unexpected_error');
        }

        return new JsonResponse($response);
    }

}
