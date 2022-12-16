<?php

namespace Portal\HelperBundle\Helper;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class Pagination
{
    const SHOWN_PAGES = 7;

    /**
     * @var Container
     */
    private $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param $currentPage
     * @param $totalPages
     * @param null $routeName
     * @param null $routeParams
     * @return string
     * @throws \Twig_Error
     */
    public function render($currentPage, $totalPages, $routeName = null, $routeParams = null)
    {
        // code from internet
        if ($totalPages > 1) {
            $left = $currentPage - 1;
            if ($left < floor(self::SHOWN_PAGES / 2)) {
                $start = 1;
            } else {
                $start = $currentPage - floor(self::SHOWN_PAGES / 2);
            }
            $end = $start + self::SHOWN_PAGES - 1;
            if ($end > $totalPages) {
                $start -= ($end - $totalPages);
                $end = $totalPages;
                if ($start < 1) $start = 1;
            }

            $pagination = $this->container->get('templating')->render('@PortalContent/Pagination/pagination.html.twig', [
                'currentPage' => $currentPage,
                'totalPages' => $totalPages,
                'start' => $start,
                'end' => $end,
                'route' => $routeName,
                'routeParams' => $routeParams
            ]);
        }

        return $pagination ?? '';
    }
}
