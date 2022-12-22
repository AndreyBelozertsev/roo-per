<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MagazineArticleManager
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
    private function getMagazineArticleRepository()
    {
        return $this->em->getRepository('PortalContentBundle:MagazineArticle');
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
     * @param integer $id
     *
     * @return \Portal\ContentBundle\Entity\MagazineArticle
     */
    public function findOneById($id)
    {
        return $this->getMagazineArticleRepository()->findOneById($id);
    }

    /**
     * @param $id
     *
     * @return array|null|object
     */
    public function find($id)
    {
        if ($id) {
            return $this->getMagazineArticleRepository()->find($id);
        } else {
            return $this->getMagazineArticleRepository()->findAll();
        }
    }

    /**
     * @return \Portal\ContentBundle\Entity\MagazineArticle[]
     */
    public function findAll()
    {
        return $this->getMagazineArticleRepository()->findAll();
    }

    /**
     * @param array $array
     *
     * @return \Portal\ContentBundle\Entity\MagazineArticle
     */
    public function findOneBy($array)
    {
        return $this->getMagazineArticleRepository()->findOneBy($array);
    }

    /**
     * @param array $array
     * @param array|null $orderBy
     * @param int|null   $limit
     *
     * @return \Portal\ContentBundle\Entity\MagazineArticle
     */
    public function findBy($array, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getMagazineArticleRepository()->findBy($array, $orderBy, $limit, $offset);
    }

    /**
     * @return array
     */
    public function findLastMagazineArticle()
    {
        return $this->getMagazineArticleRepository()->getLastMagazineArticleForEachMagazine();
    }

    /**
     * @param $page
     * @param $date
     *
     * @return array
     */
    public function getMagazineArticleList($page = 0, $date = null)
    {
        return $this->getMagazineArticleRepository()->getMagazineArticleList($page, $date);
    }

    /**
     * @param $offset
     * @param $ignoreTime
     * @param $date
     *
     * @return array
     */
    public function getPageMagazineArticleList($offset, $ignoreTime, $date = null)
    {
        return $this->getMagazineArticleRepository()->getPageMagazineArticleList($offset, $ignoreTime, $date);
    }

    /**
     * @param $ignoreTime
     * @param $date
     *
     * @return integer
     */
    public function getCountPageMagazineArticle($ignoreTime, $date = null)
    {
        return $this->getMagazineArticleRepository()->getCountPageMagazineArticle($ignoreTime, $date);
    }

    /**
     * @return array
     */
    public function getPopularMagazineArticleList()
    {
        return $this->getMagazineArticleRepository()->getPopularMagazineArticleList();
    }

    /**
     * @param integer $currentMagazineArticleId
     * @param integer $magazineId
     * @param string $ids
     *
     * @return array
     */
    public function getSameMagazineList($currentMagazineArticleId, $magazineId, $ids = '')
    {
        return $this->getMagazineArticleRepository()->getSameMagazineList($currentMagazineArticleId, $magazineId, $ids);
    }

    /**
     * @param integer $magazineId
     * @param string $shownIds
     * @param string $ignoreTime|null
     *
     * @return array
     */
    public function getNextMagazineArticle($magazineId, $shownIds, $ignoreTime = null)
    {
        return $this->getMagazineArticleRepository()->getNextMagazineArticle($magazineId, $shownIds, $ignoreTime);
    }

    /**
     * @param string $timePoint
     * @param string $date
     *
     * @return array
     */
    public function getCountMagazineArticleUntil($timePoint, $date = null)
    {
        return $this->getMagazineArticleRepository()->getCountMagazineArticleUntil($timePoint, $date);
    }

    /**
     * @param string $shownIds
     * @param string $ignoreTime
     * @param string $date
     * @param integer $startPage
     *
     * @return array
     */
    public function getNextSubdomainMagazineArticle($shownIds, $ignoreTime, $date = null, $startPage = 0)
    {
        return $this->getMagazineArticleRepository()->getNextSubdomainMagazineArticle($shownIds, $ignoreTime, $date, $startPage);
    }

    /**
     * @param array $filterParam
     *
     * @param integer $magazine
     * @return array
     */
    public function getAllMagazineArticleForPagination($filterParam, $magazine)
    {
        return $this->getMagazineArticleRepository()->getAllMagazineArticleForPagination($filterParam, $magazine);
    }

    /**
     * @param integer $days
     *
     * @return array
     */
    public function getRssMagazineArticles($days = 1)
    {
        return $this->getMagazineArticleRepository()->getRssMagazineArticles($days);
    }

    /**
     * @return array
     */
    public function getTodayMagazineArticles()
    {
        return $this->getMagazineArticleRepository()->getTodayMagazineArticles();
    }

    /**
     * @param integer $magazineId
     * @param integer $page
     *
     * @return array
     */
    public function getNextMainMagazineArticle($magazineId, $page)
    {
        return $this->getMagazineArticleRepository()->getNextMainMagazineArticle($magazineId, $page);
    }

    /**
     * @param integer $magazineId
     *
     * @return array
     */

    public function getCountPublishedMagazineArticle($magazineId)
    {
        return $this->getMagazineArticleRepository()->getCountPublishedMagazineArticle($magazineId);
    }

    /**
     * @param integer $minId
     *
     * @return array
     */
    public function getAllMagazineArticlesForSearchGrab($minId = 0)
    {
        return $this->getMagazineArticleRepository()->getAllMagazineArticlesForSearchGrab($minId);
    }

    /**
     * @param integer $maxId
     *
     * @return array
     */
    public function getAllMagazineArticlesForSearchUpdate($maxId = 0)
    {
        return $this->getMagazineArticleRepository()->getAllMagazineArticlesForSearchUpdate($maxId);
    }

    /**
     * @param integer $minId
     *
     * @return array
     */
    public function getAllMagazineArticlesForSearchGrabMain($minId = 0)
    {
        return $this->getMagazineArticleRepository()->getAllMagazineArticlesForSearchGrabMain($minId);
    }
    
    /**
     * @param integer $maxId
     *
     * @return array
     */
    public function getAllMagazineArticlesForSearchUpdateMain($maxId = 0)
    {
        return $this->getMagazineArticleRepository()->getAllMagazineArticlesForSearchUpdateMain($maxId);
    }

    /**
     * @param array $indexedMagazineArticlesId
     * @return bool
     */
    public function updateIsSearchIndexedFlag($indexedMagazineArticlesId)
    {
        return $this->getMagazineArticleRepository()->updateIsSearchIndexedFlag($indexedMagazineArticlesId);
    }
    
    /**
     * @param string $related
     *
     * @return array
     */
    public function getRelatedMagazineArticles($related)
    {
        return $this->getMagazineArticleRepository()->getRelatedMagazineArticles($related);
    }

    /**
     * @param string $date
     *
     * @return array
     */
    public function getMagazineArticleListDayOnMonth($date = null)
    {
        return $this->getMagazineArticleRepository()->getMagazineArticleListDayOnMonth($date);
    }
}
