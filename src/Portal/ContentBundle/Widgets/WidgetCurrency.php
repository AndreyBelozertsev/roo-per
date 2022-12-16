<?php

namespace Portal\ContentBundle\Widgets;

use Symfony\Component\DomCrawler\Crawler;

/**
 * Class WidgetCurrency
 * @package Portal\ContentBundle\Widgets
 */
class WidgetCurrency extends AbstractWidgets
{
    function render()
    {
        // todo store response to database
        try {
            $crawler = new Crawler(file_get_contents('http://www.cbr.ru/scripts/XML_daily.asp'));
//          https://www.cbr-xml-daily.ru/daily.xml
            $curs['usd']['char_code'] = $crawler->filter("ValCurs Valute[ID='R01235'] CharCode")->text();
            $curs['usd']['name'] = $crawler->filter("ValCurs Valute[ID='R01235'] Name")->text();
            $curs['usd']['value'] = str_replace(',', '.', $crawler->filter("ValCurs Valute[ID='R01235'] Value")->text());
            $curs['eur']['char_code'] = $crawler->filter("ValCurs Valute[ID='R01239'] CharCode")->text();
            $curs['eur']['name'] = $crawler->filter("ValCurs Valute[ID='R01239'] Name")->text();
            $curs['eur']['value'] = str_replace(',', '.', $crawler->filter("ValCurs Valute[ID='R01239'] Value")->text());

            return $this->container->get('templating')->renderResponse('PortalContentBundle:Widgets:currency.html.twig', [
                'currency' => $curs,
                'date' => $crawler->filter('ValCurs')->attr('Date')
            ])->getContent();

        } catch (\Exception $e) {
//            return 'Информер курсов валют не может быть показан';
//            return $e->getMessage();
            return false;
        }
    }
}
