<?php

namespace Portal\ContentBundle\Widgets;

use Portal\ContentBundle\Entity\Slider;

/**
 * Class WidgetMainSlider
 * @package Portal\ContentBundle\Widgets
 */
class WidgetMainSlider extends AbstractWidgets
{
    function render($widget2PanelId)
    {
        if ($widget2PanelId !== null) {
            $slug = 'informbloksbig';
            $slider = $this->container->get('slider_manager')->findOneBy(['slug' => $slug]);
            $bannerListInSlider = $this->container->get('slider_manager')->getSliderByCode($slug);

            if ($slider instanceof Slider) {
                switch ($slug) {
                    case 'informbloksmiddle':       $divClass = 'inform-bloks inform-bloks_middle box'; break;
                    case 'informbloksbig':          $divClass = 'inform-bloks inform-bloks_big box'; break;
                    case 'informblokssmall-box':    $divClass = 'inform-bloks inform-bloks_small box'; break;
                    case 'responsive':              $divClass = 'owl-posit'; break;
                    default:                        $divClass = '';
                }

                return $this->container->get('templating')->renderResponse('PortalContentBundle:Widgets:slider.html.twig', [
                    'slider' => $slider,
                    'divClass' => $divClass,
                    'bannerListInSlider' => $bannerListInSlider
                ])->getContent();
            }
        }

        return '';
    }
}
