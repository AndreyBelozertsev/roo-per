<?php
namespace Portal\AdminBundle\Widgets;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManager;

/**
 * Class AbstractWidgets
 * @package Portal\AdminBundle\Widgets
 */
abstract class AbstractWidgets implements WidgetsInterface
{
    protected $session;
    protected $em;
    
    /**
     * @var ContainerInterface
     *
     * @api
     */
    protected $container;
    
    /**
     * Sets the Container associated with this Controller.
     *
     * @param ContainerInterface $container A ContainerInterface instance
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * AbstractWidgets constructor.
     * @param Session $session
     * @param EntityManager $em
     */
    public function __construct(Session $session, EntityManager $em)
    {
        $this->session = $session;
        $this->em = $em;
    }
}
