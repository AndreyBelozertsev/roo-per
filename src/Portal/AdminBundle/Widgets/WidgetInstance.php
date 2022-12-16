<?php
namespace Portal\AdminBundle\Widgets;

use Portal\AdminBundle\Entity\Instance;

/**
 * Class Menu
 * @package Portal\AdminBundle\Widgets
 */
class WidgetInstance extends AbstractWidgets
{
    function showInstanceTitle($instanceCode)
    {
        $instance = $this->container->get('instance_manager')->findOneBy(['code' => $instanceCode]);
        if ($instance instanceof Instance) {
            $instanceTitle = $instance->getTitle();
        }

        return $instanceTitle ?? '';
    }
}
