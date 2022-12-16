<?php

namespace Portal\ContentBundle\Widgets;

use Portal\ContentBundle\Entity\Menu;
use Portal\HelperBundle\Helper\PortalHelper;

/**
 * Class Menu
 * @package Portal\ContentBundle\Widgets
 */
class WidgetMenu extends AbstractWidgets
{
    function renderMainTopMenu()
    {
        $firstTopMenu = $this->container->get('menu_manager')->findOneBy(['code' => Menu::FIRST_TOP_MENU]);
        $firstTopMenuList = $this->container->get('menu_node_manager')->findBy(
            ['menu' => $firstTopMenu, 'isHidden' => false, 'isPublished' => true, 'isDeleted' => false],
            ['order' => 'ASC']
        );
        
        return $this->container->get('templating')->renderResponse('PortalContentBundle:Widgets:menu.html.twig', [
            'menuList' => $firstTopMenuList,
            'official' => TRUE
        ])->getContent();
    }
    
    function renderOfficialResourcesMenu()
    {
        $firstTopMenu = $this->container->get('menu_manager')->findOneBy(['code' => Menu::OFFICIAL_RESOURCES_MENU]);
        $firstTopMenuList = $this->container->get('menu_node_manager')->findBy(
            ['menu' => $firstTopMenu, 'isHidden' => false, 'isPublished' => true, 'isDeleted' => false],
            ['order' => 'ASC']
        );
        
        return $this->container->get('templating')->renderResponse('PortalContentBundle:Widgets:menu.html.twig', [
            'menuList' => $firstTopMenuList
        ])->getContent();
    }

    function renderSubMainTopMenu()
    {
//        $menuId = $this->container->get('menu_manager')->findOneBy(['code' => Menu::SECOND_TOP_MENU])->getId();
//        $rootMenuNodeList = $rootNodeIds = [];
//
//        // get list root nodes sorted by node_order
//        $getRootMenuNodes = $this->container->get('menu_node_manager')->getRootMenuNodes($menuId);
//        foreach ($getRootMenuNodes as $node) {
//            $rootNodeIds[] = $node['id'];
//            $rootMenuNodeList[$node['id']] = $node;
//        }
//        // get list child nodes sorted by title
//        $ChildMenuNodeList = $this->container->get('menu_node_manager')->getChildMenuNodes($rootNodeIds);
//        foreach ($ChildMenuNodeList as $node) {
//            $rootMenuNodeList[$node['parent_id']]['childs'][] = $node;
//        }
//
//        return $this->container->get('templating')->renderResponse('PortalContentBundle:Widgets:menu.html.twig', [
//            'menuList' => $rootMenuNodeList
//        ])->getContent();
    }

    function renderSubMainTopMenuMobile()
    {
//        $menuId = $this->container->get('menu_manager')->findOneBy(['code' => Menu::SECOND_TOP_MENU])->getId();
//        $rootMenuNodeList = $rootNodeIds = [];
//
//        // get list root nodes sorted by node_order
//        $getRootMenuNodes = $this->container->get('menu_node_manager')->getRootMenuNodes($menuId);
//        foreach ($getRootMenuNodes as $node) {
//            $rootNodeIds[] = $node['id'];
//            $rootMenuNodeList[$node['id']] = $node;
//        }
//        // get list child nodes sorted by title
//        $ChildMenuNodeList = $this->container->get('menu_node_manager')->getChildMenuNodes($rootNodeIds);
//        foreach ($ChildMenuNodeList as $node) {
//            $rootMenuNodeList[$node['parent_id']]['childs'][] = $node;
//        }
//
//        return $this->container->get('templating')->renderResponse('PortalContentBundle:Widgets:menu_mobile.html.twig', [
//            'menuList' => $rootMenuNodeList
//        ])->getContent();
    }

    function renderTopMenuInstance()
    {
        $topMenu = $this->container->get('customer_menu_manager')->findOneBy(['code' => Menu::TOP_MENU_INSTANCE]);
        $topMenuList = $this->container->get('customer_menu_node_manager')->findBy(
            ['menu' => $topMenu, 'parent' => null, 'isHidden' => false, 'isPublished' => true, 'isDeleted' => false],
            ['order' => 'ASC']
        );

        return $this->container->get('templating')->renderResponse('PortalContentBundle:Widgets:menu.html.twig', [
            'menuList' => $topMenuList
        ])->getContent();
    }

    function renderStructureMenu()
    {
        $structureMenuMainNodes = $this->container->get('customer_menu_node_manager')->getStructureMenuMainNodes();
        $treeMenuMainNodes = PortalHelper::generateStructureTree($structureMenuMainNodes);

        $structureMenuNodes = $this->container->get('customer_menu_node_manager')->getStructureMenuNodes();
        $treeMenuNodes = PortalHelper::generateStructureTree($structureMenuNodes);

        return $this->container->get('templating')->renderResponse('PortalContentBundle:Widgets:structure_menu.html.twig', [
            'treeMenuMainNodes' => $treeMenuMainNodes,
            'treeMenuNodes' => $treeMenuNodes
        ])->getContent();
    }

    function renderStructureMenuMobile()
    {
        $structureMenuMainNodes = $this->container->get('customer_menu_node_manager')->getStructureMenuMainNodes();
        $treeMenuMainNodes = PortalHelper::generateStructureTree($structureMenuMainNodes);

        $structureMenuNodes = $this->container->get('customer_menu_node_manager')->getStructureMenuNodes();
        $treeMenuNodes = PortalHelper::generateStructureTree($structureMenuNodes);

        return $this->container->get('templating')->renderResponse('PortalContentBundle:Widgets:structure_menu_mobile.html.twig', [
            'treeMenuMainNodes' => $treeMenuMainNodes,
            'treeMenuNodes' => $treeMenuNodes
        ])->getContent();
    }
}
