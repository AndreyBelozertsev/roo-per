<?php
namespace Portal\HelperBundle\Menu;

use Knp\Menu\FactoryInterface;
use Portal\HelperBundle\Helper\PortalHelper;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\Container;

class Builder
{
    const NUM_LAYER_FOR_SLICE_TITLE = 4;
    const LEN_SLICE = 150;

    /**
     *
     * @var object $menu
     *
     */
    private $menu;

    private $breadcrumb;

    private $factory;

    private $container;

    private $instanceTitle;

    private $isDepartment = false;

    /**
     * @param FactoryInterface $factory
     *
     * Add any other dependency you need
     */
    public function __construct(FactoryInterface $factory, Container $container)
    {
        $this->factory = $factory;
        $this->container = $container;
        $instanceCode = $this->container->get('kernel')->getInstance();
        if ($instanceCode !== null) {
            $this->instanceTitle = $this->container->get('instance_manager')->findOneBy(['code' => $instanceCode])->getTitle();
            $this->isDepartment = true;
        } else {
            $this->instanceTitle = $this->container->get('translator')->trans('government', [], 'messages');
        }
    }

    public function createStructureMenu(RequestStack $requestStack)
    {
        $menu = $this->menu = $this->factory->createItem('root');

        // access services from the container!
        $em = $this->container->get('doctrine')->getManager('customer');

        // findMostRecent and Blog are just imaginary examples
        $menuNodes = $em->getRepository('PortalContentBundle:MenuNode')->getMenuByCode();

        $pid = $this->getRootMenuElement($menuNodes);
        $this->buildStructure($menuNodes, $pid);

        return $menu;
    }
//TODO: REFACTORING !!!!!!!!!!
    public function breadcrumbsMenu(RequestStack $requestStack)
    {
        $menu = $this->breadcrumb = $this->factory->createItem('root');

        $request = $requestStack->getCurrentRequest();
        switch($request->get('_route')) {
            case 'article_show':
                $em = $this->container->get('doctrine')->getManager('customer');
                $article = $em->getRepository('PortalContentBundle:Article')->find($request->get('id'));
                $menuNode = $article->getMenuNode();
                $this->createMenuBreadcrumbs($menuNode);
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
                if (null === $menuNode) {
                    $menu->addChild($this->container->get('translator')->trans('news_feed', [], 'messages')
                        , array('uri' => $this->container->get('router')->generate('view_all_articles')))
                        ->setCurrent(true)
                    ;
                }
                $menu
                    ->addChild($article->getTitle())
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;

                break;

            case 'article_show_slug':
                $em = $this->container->get('doctrine')->getManager('customer');
                $article = $em->getRepository('PortalContentBundle:Article')->findOneBy(['slug' => $request->get('slug')]);
                $menuNode = $article->getMenuNode();
                $this->createMenuBreadcrumbs($menuNode);
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
                if (null === $menuNode) {
                    $menu->addChild($this->container->get('translator')->trans('news_feed', [], 'messages')
                        , array('uri' => $this->container->get('router')->generate('view_all_articles')))
                        ->setCurrent(true)
                    ;
                }
                $menu
                    ->addChild($article->getTitle())
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;

                break;

            case 'inform_page':
                $em = $this->container->get('doctrine')->getManager('customer');
                $page = $em->getRepository('PortalContentBundle:Page')->find($request->get('id'));
                $menuNode = $page->getMenuNode();
                $this->createMenuBreadcrumbs($menuNode);
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
                $menu
                    ->addChild($page->getTitle())
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;
                break;

            case 'inform_page_slug':
                $em = $this->container->get('doctrine')->getManager('customer');
                $page = $em->getRepository('PortalContentBundle:Page')->findOneBy(['slug' => $request->get('slug')]);
                $menuNode = $page->getMenuNode();
                $this->createMenuBreadcrumbs($menuNode);
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
                $menu
                    ->addChild($page->getTitle())
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;
                break;

            case 'document_show':
                $em = $this->container->get('doctrine')->getManager('customer');
                $document = $em->getRepository('PortalContentBundle:Document')->find($request->get('id'));
                $menuNode = $document->getMenuNode();
                $this->createMenuBreadcrumbs($menuNode);
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
                if (null === $menuNode) {
                    $menu->addChild($this->container->get('translator')->trans('tape_documents', [], 'messages')
                        , array('uri' => $this->container->get('router')->generate('view_all_documents')))
                        ->setCurrent(true)
                    ;
                }
                $menu
                    ->addChild($document->getTitle())
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;
                break;

            case 'document_show_slug':
                $em = $this->container->get('doctrine')->getManager('customer');
                $document = $em->getRepository('PortalContentBundle:Document')->findOneBy(['slug' => $request->get('slug')]);
                $menuNode = $document->getMenuNode();
                $this->createMenuBreadcrumbs($menuNode);
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
                if (null === $menuNode) {
                    $menu->addChild($this->container->get('translator')->trans('tape_documents', [], 'messages')
                        , array('uri' => $this->container->get('router')->generate('view_all_documents')))
                        ->setCurrent(true)
                    ;
                }
                $menu
                    ->addChild($document->getTitle())
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;
                break;

            case 'event_show':
                $em = $this->container->get('doctrine')->getManager('customer');
                $event = $em->getRepository('PortalContentBundle:Event')->find($request->get('id'));
                $menuNode = $event->getMenuNode();
                $this->createMenuBreadcrumbs($menuNode);
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
                if (null === $menuNode) {
                    $menu->addChild($this->container->get('translator')->trans('events_tape', [], 'messages')
                        , array('uri' => $this->container->get('router')->generate('event')))
                        ->setCurrent(true)
                    ;
                }
                $menu
                    ->addChild($event->getTitle())
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;
                break;

            case 'event_show_slug':
                $em = $this->container->get('doctrine')->getManager('customer');
                $event = $em->getRepository('PortalContentBundle:Event')->findOneBy(['slug' => $request->get('slug')]);
                $menuNode = $event->getMenuNode();
                $this->createMenuBreadcrumbs($menuNode);
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
                if (null === $menuNode) {
                    $menu->addChild($this->container->get('translator')->trans('events_tape', [], 'messages')
                        , array('uri' => $this->container->get('router')->generate('event')))
                        ->setCurrent(true)
                    ;
                }
                $menu
                    ->addChild($event->getTitle())
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;
                break;

            case 'photo_report_show':
                $em = $this->container->get('doctrine')->getManager('customer');
                $photoReport = $em->getRepository('PortalContentBundle:PhotoReport')->find($request->get('id'));
                $menuNode = $photoReport->getMenuNode();
                $this->createMenuBreadcrumbs($menuNode);
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
                if (null === $menuNode) {
                    $menu->addChild($this->container->get('translator')->trans('photo_reports_feed', [], 'messages')
                        , array('uri' => $this->container->get('router')->generate('photo_report')))
                        ->setCurrent(true)
                    ;
                }
                $menu
                    ->addChild($photoReport->getTitle())
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;
                break;

            case 'video_report_show':
                $em = $this->container->get('doctrine')->getManager('customer');
                $videoReport = $em->getRepository('PortalContentBundle:VideoReport')->find($request->get('id'));
                $menuNode = $videoReport->getMenuNode();
                $this->createMenuBreadcrumbs($menuNode);
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
                if (null === $menuNode) {
                    $menu->addChild($this->container->get('translator')->trans('video_report_feed', [], 'messages')
                        , array('uri' => $this->container->get('router')->generate('video_report')))
                        ->setCurrent(true)
                    ;
                }
                $menu
                    ->addChild($videoReport->getTitle())
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;
                break;

            case 'structure_slug':
                $em = $this->container->get('doctrine')->getManager('customer');
                $menuNode = $em->getRepository('PortalContentBundle:MenuNode');
                $structureSlug = $menuNode->findOneBy(['slug' => $request->get('slug')]);
                $this->createMenuBreadcrumbs($structureSlug);
                $menu
                    ->addChild($structureSlug->getId())
                    ->setLabel($structureSlug->getTitle())
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
                break;

            case 'structure':
                $em = $this->container->get('doctrine')->getManager('customer');
                $menuNode = $em->getRepository('PortalContentBundle:MenuNode');
                $structure = $menuNode->find($request->get('id'));
                $this->createMenuBreadcrumbs($structure);
                $menu
                    ->addChild($structure->getId())
                    ->setLabel($structure->getTitle())
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
                break;

            case 'view_all_articles':
                $this->createMenuBreadcrumbs(null);
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
                $menu
                    ->addChild($this->container->get('translator')->trans('news_feed', [], 'messages'))
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;
                break;

            case 'event':
                $this->createMenuBreadcrumbs(null);
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
                $menu
                    ->addChild($this->container->get('translator')->trans('events_tape', [], 'messages'))
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;
                break;

            case 'photo_report':
                $this->createMenuBreadcrumbs(null);
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
                $menu
                    ->addChild($this->container->get('translator')->trans('photo_reports_feed', [], 'messages'))
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;
                break;

            case 'video_report':
                $this->createMenuBreadcrumbs(null);
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
                $menu
                    ->addChild($this->container->get('translator')->trans('video_report_feed', [], 'messages'))
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;
                break;

            case 'view_all_documents':
                $this->createMenuBreadcrumbs(null);
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
                $menu
                    ->addChild($this->container->get('translator')->trans('tape_documents', [], 'messages'))
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;
                break;

            case 'search_index':
                $this->createMenuBreadcrumbs(null);
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
                $menu
                    ->addChild($this->container->get('translator')->trans('search', [], 'messages'))
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;
                break;

            case 'interview_show':
                $em = $this->container->get('doctrine')->getManager('customer');
                $interview = $em->getRepository('PortalContentBundle:Interview')->find($request->get('id'));
                $menuNode = $interview->getMenuNode();
                $this->createMenuBreadcrumbs($menuNode);
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
                $menu
                    ->addChild($interview->getTitle())
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;

                break;

            case 'interview_show_slug':
                $em = $this->container->get('doctrine')->getManager('customer');
                $interview = $em->getRepository('PortalContentBundle:Interview')->findOneBy(['slug' => $request->get('slug')]);
                $menuNode = $interview->getMenuNode();
                $this->createMenuBreadcrumbs($menuNode);
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
                $menu
                    ->addChild($interview->getTitle())
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;

                break;

            case 'search_documents_index':
                $this->createMenuBreadcrumbs(null);
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
                $menu
                    ->addChild($this->container->get('translator')->trans('tape_documents', [], 'messages'),
                        array('uri' => $this->container->get('router')->generate('view_all_documents'))
                    )
                    ->setCurrent(true)
                ;
                $menu
                    ->addChild($this->container->get('translator')->trans('searchDocuments', [], 'messages'))
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;
                break;

            case 'view_all_heads':
                $this->createMenuBreadcrumbs(null);
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
                $menu
                    ->addChild($this->container->get('translator')->trans('head_content.list_heads', [], 'messages'))
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;
                break;

            case 'head_show':
                $em = $this->container->get('doctrine')->getManager('customer');
                $item = $em->getRepository('PortalContentBundle:Head')->find($request->get('id'));
                $menuNode = $item->getMenuNode();
                $this->createMenuBreadcrumbs($menuNode);
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
                if (null === $menuNode) {
                    $menu->addChild($this->container->get('translator')->trans('head_content.list_heads', [], 'messages')
                        , array('uri' => $this->container->get('router')->generate('view_all_heads')))
                        ->setCurrent(true)
                    ;
                }
                $menu
                    ->addChild($item->getLastName().' '.$item->getFirstName().' '.$item->getMiddleName())
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;

                break;

            case 'head_show_slug':
                $em = $this->container->get('doctrine')->getManager('customer');
                $item = $em->getRepository('PortalContentBundle:Head')->findOneBy(['slug' => $request->get('slug')]);
                $menuNode = $item->getMenuNode();
                $this->createMenuBreadcrumbs($menuNode);
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
                if (null === $menuNode) {
                    $menu->addChild($this->container->get('translator')->trans('head_content.list_heads', [], 'messages')
                        , array('uri' => $this->container->get('router')->generate('view_all_heads')))
                        ->setCurrent(true)
                    ;
                }
                $menu
                    ->addChild($item->getFirstName().' '.$item->getLastName().' '.$item->getMiddleName())
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;

                break;

            case 'quiz_show':
                $em = $this->container->get('doctrine')->getManager('customer');
                $item = $em->getRepository('PortalContentBundle:Quiz')->find($request->get('id'));
                $menuNode = $item->getMenuNode();
                $this->createMenuBreadcrumbs($menuNode);
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
//                if (null === $menuNode) {
//                    $menu->addChild($this->container->get('translator')->trans('head_content.list_heads', [], 'messages')
//                        , array('uri' => $this->container->get('router')->generate('view_all_heads')))
//                        ->setCurrent(true)
//                    ;
//                }
                $menu
                    ->addChild($item->getTitle())
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;

                break;

            case 'quiz_result':
                $em = $this->container->get('doctrine')->getManager('customer');
                $item = $em->getRepository('PortalContentBundle:Quiz')->find($request->get('id'));
                $menuNode = $item->getMenuNode();
                $this->createMenuBreadcrumbs($menuNode);
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
//                if (null === $menuNode) {
//                    $menu->addChild($this->container->get('translator')->trans('head_content.list_heads', [], 'messages')
//                        , array('uri' => $this->container->get('router')->generate('view_all_heads')))
//                        ->setCurrent(true)
//                    ;
//                }
                $menu
                    ->addChild($item->getTitle())
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;

                break;

            case 'quiz_show_slug':
                $em = $this->container->get('doctrine')->getManager('customer');
                $item = $em->getRepository('PortalContentBundle:Quiz')->findOneBy(['slug' => $request->get('slug')]);
                $menuNode = $item->getMenuNode();
                $this->createMenuBreadcrumbs($menuNode);
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
//                if (null === $menuNode) {
//                    $menu->addChild($this->container->get('translator')->trans('head_content.list_heads', [], 'messages')
//                        , array('uri' => $this->container->get('router')->generate('view_all_heads')))
//                        ->setCurrent(true)
//                    ;
//                }
                $menu
                    ->addChild($item->getTitle())
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;

                break;
            case 'feedback_page':
                $em = $this->container->get('doctrine')->getManager('customer');
                $feedback = $em->getRepository('PortalContentBundle:FeedbackForm')->find($request->get('id'));
                $menuNode = $feedback->getMenuNode();
                $this->createMenuBreadcrumbs($menuNode);
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
//                if (null === $menuNode) {
//                    $menu->addChild($this->container->get('translator')->trans('news_feed', [], 'messages')
//                        , array('uri' => $this->container->get('router')->generate('view_all_articles')))
//                        ->setCurrent(true)
//                    ;
//                }
                $menu
                    ->addChild($feedback->getTitle())
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;

                break;

            case 'feedback_page_slug':
                $em = $this->container->get('doctrine')->getManager('customer');
                $feedback = $em->getRepository('PortalContentBundle:FeedbackForm')->findOneBy(['slug' => $request->get('slug')]);
                $menuNode = $feedback->getMenuNode();
                $this->createMenuBreadcrumbs($menuNode);
                $menu = $menu->setChildren(array_reverse($menu->getChildren()));
//                if (null === $menuNode) {
//                    $menu->addChild($this->container->get('translator')->trans('news_feed', [], 'messages')
//                        , array('uri' => $this->container->get('router')->generate('view_all_articles')))
//                        ->setCurrent(true)
//                    ;
//                }
                $menu
                    ->addChild($feedback->getTitle())
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;

                break;

            case 'portal_content_department_homepage':
                if ($this->container->get('kernel')->getEnvironment() === "dev") {
                    $dev = "/app_dev.php";
                } else {
                    $dev = "";
                }
                $this->breadcrumb
                    ->addChild($this->container->get('translator')->trans('government', [], 'messages'),
                        array('uri' => '//'.$this->container->getParameter('site_name').$dev)
                    )->setCurrent(true);
                $menu
                    ->addChild($this->instanceTitle)
                    ->setCurrent(true)
                    // setCurrent is use to add a "current" css class
                ;

                break;
        }
        $layerCount = 0;
        foreach ($menu->getChildren() as $key => $value) {
            $layerCount++;
            $value->setLabel($layerCount >= self::NUM_LAYER_FOR_SLICE_TITLE ? PortalHelper::sliceFullWord($value->getLabel(), self::LEN_SLICE) : $value->getLabel());
        }
        return $menu;
    }

    /**
     * Get structure array
     *
     * @param object $data
     *
     * @return array
     */
    private function buildStructure($data, $pid = null, $level = 0)
    {
        foreach ($data as $menuNode) {

            if (($menuNode->getParent() ? $menuNode->getParent()->getId() : $menuNode->getParent()) === $pid) {
                if ($this->getElemChild($menuNode->getParent()->getTitle())) {

                    $this->getElemChild($menuNode->getParent()->getTitle())
                        ->addChild($menuNode->getTitle(),
                            array('uri' => $this->container->get('router')->generate('structure',array('id' => $menuNode->getId())),
                                'attributes' => array('class' => 'subdom-nav__item')
                            )
                        );

                } else {

                    $this->menu->addChild($menuNode->getTitle(),

                        array('uri' => $this->container->get('router')->generate('structure',array('id' => $menuNode->getId())),
                            'attributes' => array('class' => 'subdom-nav__item')
                        )

                    );

                }

            }
        }

//        TODO: refactore algoritm!
        foreach ($data as $menuNode) {
            if (($menuNode->getParent() ? $menuNode->getParent()->getId() : $menuNode->getParent()) === $pid) {

                self::buildStructure($data, $menuNode->getId(), $level + 1);
            }
        }
    }

    private function getElemChild($name)
    {
        $menu = &$this->menu;
        $itemIterator = new \Knp\Menu\Iterator\RecursiveItemIterator($menu);
        $iterator = new \RecursiveIteratorIterator($itemIterator, \RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $item) {
            if ($name == $item->getName()) {

                return $item;
            }
        }
    }

    private function getRootMenuElement($menu)
    {
        foreach ($menu as $item) {
            if($item->getParent() === null) {

                return $item->getId();
            }
        }
    }

    private function createMenuBreadcrumbs($data)
    {
        if (is_object($data)) {
            $this->breadcrumb
                ->addChild($data->getId(),
                    array('uri' => $this->container->get('router')->generate('structure',array('id' => $data->getId())))
                )
                ->setLabel($data->getTitle())
            ;
            if ($data->getParent()) {
                $child = $data->getParent();
                self::createMenuBreadcrumbs($child);
            }
        }
        if ($this->isDepartment) {
            if ($this->container->get('kernel')->getEnvironment() === "dev") {
                $dev = "/app_dev.php";
            } else {
                $dev = "";
            }
            $this->breadcrumb
                ->addChild($this->instanceTitle, array('uri' => $this->container->get('router')->generate('portal_content_homepage')))
                ->setLabel($this->instanceTitle)
                ->setCurrent(true);
            $this->breadcrumb
                ->addChild($this->container->get('translator')->trans('government', [], 'messages'),
                    array('uri' => '//'.$this->container->getParameter('site_name').$dev)
                )->setCurrent(true);
        } else {
            $this->breadcrumb->addChild($this->instanceTitle, array('uri' => $this->container->get('router')->generate('portal_content_homepage')))
                ->setLabel($this->instanceTitle)
                ->setCurrent(true);
        }
    }
}
