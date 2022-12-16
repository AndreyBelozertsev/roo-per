<?php

namespace Portal\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Portal\ContentBundle\Entity\Widget;
use Portal\ContentBundle\Entity\WidgetToPanel;
use Portal\ContentBundle\Entity\WidgetParam;

class WidgetParamAdminController extends Controller
{
    public function editAction(Request $request, $id, $instanceCode)
    {
        $flashBag = $this->get('session')->getFlashBag();

        $currentWidgetToPanel = $this->get('customer_widget_to_panel_manager')->find($id);
        if ($currentWidgetToPanel instanceof WidgetToPanel) {
            $currentWidget = $currentWidgetToPanel->getWidgetId();
            if ($currentWidget instanceof Widget) {
                
                $arrParams['widgetTitle'] = $currentWidgetToPanel->getTitle();

                $arrParamNames = [];
                switch ($currentWidget->getSlug()) {
                    case 'follow_us':
                        $arrParamNames = WidgetParam::WIDGET_FOLLOW_US_PARAM_LIST;
                        break;
                    case 'channel':
                        $arrParamNames = WidgetParam::WIDGET_CHANNEL_PARAM_LIST;
                        break;
                    case 'quote':
                        $arrParamNames = WidgetParam::WIDGET_QUOTE_PARAM_LIST;
                        break;
                    case 'slider':
                        $arrParamNames = WidgetParam::WIDGET_SLIDER_PARAM_LIST;
                        $sliderArray = $this->get('customer_slider_manager')->getSliderList();
                        foreach ($sliderArray as $v) {
                            $arrParamNames['slider']['options'][$v['id']] = $v['title'];
                        }
                        break;
                    case 'instagram':
                        $arrParamNames = WidgetParam::WIDGET_INSTAGRAM_PARAM_LIST;
                        break;
                }

                $wpNames = array_keys($arrParamNames);
                if (sizeof($wpNames) !== 0) {
                    $dbWidgetParams = [];
                    $widgetParams = $this->get('customer_widget_param_manager')->getParamsByNamesAndId($wpNames, $id);
                    foreach ($widgetParams as $v) {
                        $dbWidgetParams[$v['param_name']] = $v;
                    }

                    if ($request->getMethod() === "POST") {
                        $em = $this->getDoctrine()->getManager('customer');
                        foreach ($arrParamNames as $k => $v) {
                            if (isset($dbWidgetParams[$k]['id'])) {
                                $widgetParam = $this->get('customer_widget_param_manager')->find($dbWidgetParams[$k]['id']);
                            } else {
                                $widgetParam = new WidgetParam();
                                $widgetParam->setWidgetToPanelId($currentWidgetToPanel);
                                $widgetParam->setParamTitle($this->get('translator')->trans($v['name']));
                                $widgetParam->setParamName($k);
                            }
                            $value = $request->request->get($k);
                            $widgetParam->setParamType($v['type']);
                            $widgetParam->setParamValue($value);
                            $dbWidgetParams[$k]['param_value'] = $value;
                            $em->persist($widgetParam);
                        }
                        $em->flush();
                        $flashBag->add('message', $this->get('translator')->trans('successfully_save'));
                    }

                    foreach ($arrParamNames as $k => $v) {
                        $arrParams['params'][$k]['param_name'] = $k;
                        $arrParams['params'][$k]['param_title'] = $dbWidgetParams[$k]['param_title'] ?? $this->get('translator')->trans($v['name']);
                        $arrParams['params'][$k]['param_value'] = $dbWidgetParams[$k]['param_value'] ?? '';
                        $arrParams['params'][$k]['param_type'] = $v['type'] ?? '';
                        $arrParams['params'][$k]['param_options'] = $v['options'] ?? [];
                    }
                } else {
                    $arrParams['message'] = $this->get('translator')->trans('no_widget_params');
                }
            } else {
                $arrParams['message'] = $this->get('translator')->trans('no_widget');
            }
        } else {
            $arrParams['message'] = $this->get('translator')->trans('no_widget_to_panel');
        }
        $arrParams['instanceCode'] = $instanceCode;

        return $this->render('PortalAdminBundle:WidgetParamAdmin:edit.html.twig', $arrParams);
    }
}
