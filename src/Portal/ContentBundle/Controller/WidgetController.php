<?php

namespace Portal\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class WidgetController extends Controller
{
    public function weatherAction(Request $request)
    {
        return new JsonResponse([
            'content' => $this->get('weather')->renderWeather($request->get('cityId'))
        ]);
    }

    public function loadWidgetAction(Request $request)
    {
        $arrParam = [
            'status' => false,
            'content' => '',
            'message' => '',
        ];
        $idWidget = $request->get('id');
        if ($idWidget) {
            $codeWidget = $this->get('customer_widget_to_panel_manager')->getCodeWidget($idWidget);
            $arrParam['content'] = $this->get((string)$codeWidget)->render($idWidget);
            $arrParam['status'] = true;
        }

        return new JsonResponse($arrParam);
    }
}
