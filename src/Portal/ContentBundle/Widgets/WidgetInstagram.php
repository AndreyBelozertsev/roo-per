<?php

namespace Portal\ContentBundle\Widgets;

class WidgetInstagram extends AbstractWidgets
{
    function render($idWidget2Panel)
    {
        $account = $this->container->get('customer_widget_param_manager')->getParamByNameAndId('account_name', $idWidget2Panel);
        try {
//            $request = file_get_contents('https://www.instagram.com/' . $account . '/?__a=1');
            $request = file_get_contents('https://www.instagram.com/' . $account);
            preg_match('#<script type="text/javascript">window._sharedData = (.*);</script>#', $request, $matches);
            $request = $matches[1];

            return $this->container->get('templating')->renderResponse('PortalContentBundle:Widgets:instagram.html.twig', [
                'title' => $this->container->get('customer_widget_param_manager')->getParamByNameAndId('title', $idWidget2Panel),
                'account' => $account,
                'data' => json_decode($request)
            ])->getContent();
        } catch (\Exception $e) {

            return $this->container->get('translator')->trans('widget_fail', [], 'messages');
        }
    }
}
