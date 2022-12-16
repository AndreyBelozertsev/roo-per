<?php

namespace Portal\ContentBundle\Widgets;

/**
 * Class Weather
 * @package Portal\ContentBundle\Widgets
 */
class WidgetWeather extends AbstractWidgets implements WidgetsPanelInterface
{
    private $_token = '59a3d6ac407482.74412743';
    private $_city = [
        '4996' => 'Алушта',
        '11364' => 'Бахчисарай',
        '11379' => 'Белогорск',
        '4994' => 'Джанкой',
        '4992' => 'Евпатория',
        '5001' => 'Керчь',
        '4993' => 'Саки',
        '4995' => 'Симферополь',
        '4998' => 'Судак',
        '4999' => 'Феодосия',
        '5002'=> 'Ялта'
    ];
//    private $_wind = [
//        1 => 'Северный',
//        2 => 'Северо-восточный',
//        3 => 'Восточный',
//        4 => 'Юго-восточный',
//        5 => 'Южный',
//        6 => 'Юго-западный',
//        7 => 'Западный',
//        8 => 'Северо-западный'
//    ];

    function renderWeather($cityId = null)
    {
//        $arrParams['weather']['wind'] = $this->_wind;
        $arrParams['weather']['cities'] = $this->_city;
        $cityId = (array_key_exists($cityId, $this->_city)) ? $cityId : '4995';
        $options = [
            'http' => [
                'method' => 'GET',
                'header' => 'X-Gismeteo-Token: ' . $this->_token . "\r\n"
            ]
        ];
        try {
//            https://api.gismeteo.ru/v2/weather/current/$cityId/
            $url = 'https://api.gismeteo.ru/v2/weather/forecast/aggregate/' . $cityId . '/?days=5';
            $request = file_get_contents($url, false, stream_context_create($options));
            $arrParams['weather']['data'] = json_decode($request, 1)['response'];

            return $this->container->get('templating')
                ->renderResponse('PortalContentBundle:Widgets:weather.html.twig', $arrParams)->getContent();
        } catch (\Exception $e) {
//            return 'Информер погоды не может быть показан';
            return false;
        }
    }

    function render()
    {
        return $this->renderWeather();
    }
}
