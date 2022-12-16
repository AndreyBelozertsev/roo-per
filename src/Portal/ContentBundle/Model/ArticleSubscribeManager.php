<?php


namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ArticleSubscribeManager
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public $container;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager $em
     */
    private $em;

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getArticleSubscribeRepository()
    {
        return $this->em->getRepository('PortalContentBundle:ArticleSubscribe');
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $em
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ObjectManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * @return \Portal\ContentBundle\Entity\ArticleSubscribe[]
     */
    public function findAll()
    {
        return $this->getArticleSubscribeRepository()->findAll();
    }

    /**
     * @param array $array
     * @param array|null $orderBy
     * @param int|null $limit
     * @return \Portal\ContentBundle\Entity\ArticleSubscribe
     */
    public function findBy($array, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getArticleSubscribeRepository()->findBy($array, $orderBy, $limit, $offset);
    }

    public function getGroupedEmails()
    {
        return $this->getArticleSubscribeRepository()->getGroupedEmails();
    }
}
