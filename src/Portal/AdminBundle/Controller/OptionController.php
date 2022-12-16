<?php

namespace Portal\AdminBundle\Controller;

use Portal\ContentBundle\Entity\Option;
use Portal\HelperBundle\Controller\Controller as Controller;
use Symfony\Component\HttpFoundation\Request;

class OptionController extends Controller
{

    public function viewAllAction($instanceCode)
    {
        $optionList = $this->get("option_manager")->findBy([], ['id' => 'ASC']);

        return $this->render('PortalAdminBundle:OptionAdmin:viewAll.html.twig', [
            'optionList' => $optionList,
            'instanceCode' => $instanceCode
        ]);
    }

    public function updateAction(Request $request, $instanceCode)
    {
        $options = $request->request->get("options");
        $optionList = $this->get("option_manager")->find();
        $em = $this->getDoctrine()->getManager('customer');
        foreach ($optionList as $option) {
            if (isset($options[explode('.', $option->getName())[2]])) {
                if ($option->getTypeOption() === Option::OPTION_TYPE_CHECKBOX) {
                    $option->setvalue(1);
                } else {
                    $option->setvalue($options[explode('.', $option->getName())[2]]);
                }
            } else {
                $option->setvalue(0);
            }
            $em->persist($option);
            $em->flush();
        }

        return $this->redirectToRoute('admin_instance_option_view_all', [
            'instanceCode' => $instanceCode
        ]);
    }
}
