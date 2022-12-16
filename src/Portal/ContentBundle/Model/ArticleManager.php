<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ArticleManager
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
    private function getArticleRepository()
    {
        return $this->em->getRepository('PortalContentBundle:Article');
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
     * @return \Portal\ContentBundle\Entity\Article
     */
    public function findOneById($id)
    {
        return $this->getArticleRepository()->findOneById($id);
    }

    /**
     * @param $id
     *
     * @return array|null|object
     */
    public function find($id)
    {
        if ($id) {
            return $this->getArticleRepository()->find($id);
        } else {
            return $this->getArticleRepository()->findAll();
        }
    }

    /**
     * @return \Portal\ContentBundle\Entity\Article[]
     */
    public function findAll()
    {
        return $this->getArticleRepository()->findAll();
    }

    /**
     * @param array $array
     *
     * @return \Portal\ContentBundle\Entity\Article
     */
    public function findOneBy($array)
    {
        return $this->getArticleRepository()->findOneBy($array);
    }

    /**
     * @param array $array
     * @param array|null $orderBy
     * @param int|null   $limit
     *
     * @return \Portal\ContentBundle\Entity\Article
     */
    public function findBy($array, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getArticleRepository()->findBy($array, $orderBy, $limit, $offset);
    }

    /**
     * @return array
     */
    public function findLastArticle()
    {
        return $this->getArticleRepository()->getLastArticleForEachCategory();
    }

    /**
     * @param $page
     * @param $date
     *
     * @return array
     */
    public function getArticleList($page = 0, $date = null)
    {
        return $this->getArticleRepository()->getArticleList($page, $date);
    }

    /**
     * @param $offset
     * @param $ignoreTime
     * @param $date
     *
     * @return array
     */
    public function getPageArticleList($offset, $ignoreTime, $date = null)
    {
        return $this->getArticleRepository()->getPageArticleList($offset, $ignoreTime, $date);
    }

    /**
     * @param $ignoreTime
     * @param $date
     *
     * @return integer
     */
    public function getCountPageArticle($ignoreTime, $date = null)
    {
        return $this->getArticleRepository()->getCountPageArticle($ignoreTime, $date);
    }

    /**
     * @return array
     */
    public function getPopularArticleList()
    {
        return $this->getArticleRepository()->getPopularArticleList();
    }

    /**
     * @param integer $currentArticleId
     * @param integer $categoryId
     * @param string $ids
     *
     * @return array
     */
    public function getSameCategoryList($currentArticleId, $categoryId, $ids = '')
    {
        return $this->getArticleRepository()->getSameCategoryList($currentArticleId, $categoryId, $ids);
    }

    /**
     * @param integer $catId
     * @param string $shownIds
     * @param string $ignoreTime|null
     *
     * @return array
     */
    public function getNextArticle($catId, $shownIds, $ignoreTime = null)
    {
        return $this->getArticleRepository()->getNextArticle($catId, $shownIds, $ignoreTime);
    }

    /**
     * @param string $timePoint
     * @param string $date
     *
     * @return array
     */
    public function getCountArticleUntil($timePoint, $date = null)
    {
        return $this->getArticleRepository()->getCountArticleUntil($timePoint, $date);
    }

    /**
     * @param string $shownIds
     * @param string $ignoreTime
     * @param string $date
     * @param integer $startPage
     *
     * @return array
     */
    public function getNextSubdomainArticle($shownIds, $ignoreTime, $date = null, $startPage = 0)
    {
        return $this->getArticleRepository()->getNextSubdomainArticle($shownIds, $ignoreTime, $date, $startPage);
    }

    /**
     * @param array $filterParam
     *
     * @param integer $cat
     * @return array
     */
    public function getAllArticleForPagination($filterParam, $cat)
    {
        return $this->getArticleRepository()->getAllArticleForPagination($filterParam, $cat);
    }

    /**
     * @param integer $days
     *
     * @return array
     */
    public function getRssArticles($days = 1)
    {
        return $this->getArticleRepository()->getRssArticles($days);
    }

    /**
     * @return array
     */
    public function getTodayArticles()
    {
        return $this->getArticleRepository()->getTodayArticles();
    }

    /**
     * @param integer $categoryId
     * @param integer $page
     *
     * @return array
     */
    public function getNextMainArticle($categoryId, $page)
    {
        return $this->getArticleRepository()->getNextMainArticle($categoryId, $page);
    }

    /**
     * @param integer $categoryId
     *
     * @return array
     */

    public function getCountPublishedArticle($categoryId)
    {
        return $this->getArticleRepository()->getCountPublishedArticle($categoryId);
    }

    /**
     * @param integer $minId
     *
     * @return array
     */
    public function getAllArticlesForSearchGrab($minId = 0)
    {
        return $this->getArticleRepository()->getAllArticlesForSearchGrab($minId);
    }

    /**
     * @param integer $maxId
     *
     * @return array
     */
    public function getAllArticlesForSearchUpdate($maxId = 0)
    {
        return $this->getArticleRepository()->getAllArticlesForSearchUpdate($maxId);
    }

    /**
     * @param integer $minId
     *
     * @return array
     */
    public function getAllArticlesForSearchGrabMain($minId = 0)
    {
        return $this->getArticleRepository()->getAllArticlesForSearchGrabMain($minId);
    }
    
    /**
     * @param integer $maxId
     *
     * @return array
     */
    public function getAllArticlesForSearchUpdateMain($maxId = 0)
    {
        return $this->getArticleRepository()->getAllArticlesForSearchUpdateMain($maxId);
    }

    /**
     * @param array $indexedArticlesId
     * @return bool
     */
    public function updateIsSearchIndexedFlag($indexedArticlesId)
    {
        return $this->getArticleRepository()->updateIsSearchIndexedFlag($indexedArticlesId);
    }
    
    /**
     * @param string $related
     *
     * @return array
     */
    public function getRelatedArticles($related)
    {
        return $this->getArticleRepository()->getRelatedArticles($related);
    }

    /**
     * @return array
     */
    public function getSliderArticle()
    {
        return $this->getArticleRepository()->getSliderArticle();
    }

    /**
     * @param string $date
     *
     * @return array
     */
    public function getArticleListDayOnMonth($date = null)
    {
        return $this->getArticleRepository()->getArticleListDayOnMonth($date);
    }
}
