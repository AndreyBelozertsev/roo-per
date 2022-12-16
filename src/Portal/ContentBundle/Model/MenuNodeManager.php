<?php

namespace Portal\ContentBundle\Model;

use Portal\ContentBundle\Entity\Menu;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MenuNodeManager
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
    private function getMenuNodeRepo()
    {
        return $this->em->getRepository('PortalContentBundle:MenuNode');
    }
    
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $em
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ObjectManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\MenuNode
     */
    public function findOneById($id)
    {
        return $this->getMenuNodeRepo()->findOneById($id);
    }

    /**
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\MenuNode
     */
    public function find($id)
    {
        return $id ? $this->getMenuNodeRepo()->find($id) : $this->getMenuNodeRepo()->findAll();
    }

    /**
     * @return \Portal\ContentBundle\Entity\MenuNode[]
     */
    public function findAll()
    {
        return $this->getMenuNodeRepo()->findAll();
    }
    
    /**
     * @param array $array
     *
     * @return \Portal\ContentBundle\Entity\MenuNode
     */
    public function findOneBy($array)
    {
        return $this->getMenuNodeRepo()->findOneBy($array);
    }

    /**
     * @param array $array
     * @param array|null $orderBy
     * @param int|null   $limit
     *
     * @return \Portal\ContentBundle\Entity\MenuNode
     */
    public function findBy($array, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getMenuNodeRepo()->findBy($array, $orderBy, $limit, $offset);
    }

    /**
     * @return \Portal\ContentBundle\Entity\MenuNode
     */
    public function getStructureMenu()
    {
        return $this->getMenuNodeRepo()->getStructureMenu();
    }
    
    /**
     * @return \Portal\ContentBundle\Entity\MenuNode
     */
    public function getMenuByCode($code = Menu::STRUCTURE_MENU)
    {
        return $this->getMenuNodeRepo()->getMenuByCode($code);
    }

    /**
     * @param array $filterParam
     *
     * @return array
     */
    public function getAllMenuNodeForPagination($filterParam)
    {
        return $this->getMenuNodeRepo()->getAllMenuNodeForPagination($filterParam);
    }
    
    /**
     * @return array
     */
    public function getRootMenu()
    {
        return $this->getMenuNodeRepo()->getRootMenu();
    }
    
    /**
     * @return array
     */
    public function getStructureMenuMainNodes()
    {
        return $this->getMenuNodeRepo()->getStructureMenuMainNodes();
    }
    
    /**
     * @return array
     */
    public function getStructureMenuNodes()
    {
        return $this->getMenuNodeRepo()->getStructureMenuNodes();
    }

    /**
     * @param array $params
     * @param \Symfony\Component\DependencyInjection\ContainerInterface|null $container
     *
     * @return mixed
     */
    public function moveCategory(array $params, $container = null)
    {
        return $this->getMenuNodeRepo()->moveCategory($params, $container);
    }

    /**
     * @param integer $nodeId
     * @return mixed
     */
    public function deleteNode($nodeId)
    {
        return $this->getMenuNodeRepo()->deleteNode($nodeId);
    }

    /**
     * @param integer $nodeId
     * @return mixed
     */
    public function restoreMenuNode($nodeId)
    {
        return $this->getMenuNodeRepo()->restoreMenuNode($nodeId);
    }

    /**
     * @param integer $nodeId
     * @return integer
     */
    public function getCountRemovedParents($nodeId)
    {
        return $this->getMenuNodeRepo()->getCountRemovedParents($nodeId);
    }

    /**
     * @param integer $menuId
     * @return array
     */
    public function getRootMenuNodes($menuId)
    {
        return $this->getMenuNodeRepo()->getRootMenuNodes($menuId);
    }

    /**
     * @param array $rootNodeIds
     * @return array
     */
    public function getChildMenuNodes($rootNodeIds)
    {
        return $this->getMenuNodeRepo()->getChildMenuNodes($rootNodeIds);
    }

    /**
     * @param integer $structureSyncLimit
     * @return array
     */
    public function getAllStructureForSearchGrab($structureSyncLimit)
    {
        return $this->getMenuNodeRepo()->getAllStructureForSearchGrab($structureSyncLimit);
    }

    /**
     * @param integer $maxId
     * @return array
     */
    public function getAllStructureForSearchUpdate($maxId)
    {
        return $this->getMenuNodeRepo()->getAllStructureForSearchUpdate($maxId);
    }
    
    /**
     * @param array $indexedMenuNodesId
     * @return bool
     */
    public function updateIsSearchIndexedFlag($indexedMenuNodesId)
    {
        return $this->getMenuNodeRepo()->updateIsSearchIndexedFlag($indexedMenuNodesId);
    }

    /**
     * @param integer|null $parentId
     * @return int|null
     */
    public function resortingStructure($parentId = null)
    {
        return $this->getMenuNodeRepo()->resortingStructure($parentId);
    }
}
