<?php

namespace Portal\ContentBundle\Widgets;

use Portal\ContentBundle\Form\ArticleSubscribeFormType;
use Portal\ContentBundle\Entity\ArticleSubscribe;

/**
 * Class WidgetSubscribeForm
 * @package Portal\ContentBundle\Widgets
 */
class WidgetSubscribeForm extends AbstractWidgets  implements WidgetsPanelInterface
{
    function render()
    {
        $instanceCode = $this->container->getParameter('instance_code');
        $instance = $this->container->get('instance_manager')->findOneBy(['code' => $instanceCode]);

        $form = $this->container->get('form.factory')->create(
            ArticleSubscribeFormType::class,
            new ArticleSubscribe(),
            ['instanceId' => $instance->getId()]
        );

        return $this->container->get('templating')->renderResponse('PortalContentBundle:Widgets:subscribe_form.html.twig', [
            'form' => $form->createView()
        ])->getContent();
    }
}
