<?php

namespace Portal\HelperBundle\Libs;


class Pagination
{
    public $url;
    private $currentPage = 1;
    private $count = 0;
    private $limit;
    private $offset = 0;
    private $pageCount;
    private $firstPage = 1;
    private $lastPage = 1;
    private $rangeFirstPage = 1;
    private $rangeLastPage;
    private $rangeLast;

    const RANGE_COUNT = 5;
    /**
     * constructor
     * @param integer $count total number of items
     */
    public function __construct($count = 0, $limit = 20, $requestedPage = null)
    {
        $this->limit = $limit;
        $this->count = $count;
        $this->pageCount = (int) ceil($this->count / $this->limit);

        if (is_null($requestedPage)) {
            $requestedPage = (isset($_REQUEST['page'])) ? $_REQUEST['page'] : false;
        }

        $this->currentPage = ($requestedPage > $this->pageCount || $requestedPage < 1) ? $this->currentPage : $requestedPage;

        $offset = $limit * ($this->currentPage - 1);
        $this->offset = ($offset < 0) ? $this->offset : $offset;

        $this->firstPage = 1;
        $this->lastPage = $this->pageCount;

        $this->rangeFirstPage = ($this->currentPage - self::RANGE_COUNT < 1) ? 1 : $this->currentPage - self::RANGE_COUNT;
        $this->rangeLastPage = ($this->currentPage + self::RANGE_COUNT > $this->pageCount) ? $this->pageCount : $this->currentPage + self::RANGE_COUNT - 1;
        $rangeCount = self::RANGE_COUNT;
        if ($this->getLastPage() - $this->getRangeLastPage() - 1 < self::RANGE_COUNT) {
            $rangeCount = $this->getLastPage() - $this->getRangeLastPage() - 1;
        }
        $this->rangeLast = $this->getPageCount() - $rangeCount;
    }

    /**
     * @return integer the limit of the data. This may be used to set the LIMIT value for a SQL statement for fetching the current page of data.
     * This returns the same value as pageSize
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return integer the offset of the data. This may be used to set the OFFSET value for a SQL statement for fetching the current page of data.
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return integer the zero-based index of the current page. Defaults to 1.
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @return integer number of pages
     */
    public function getPageCount()
    {
        return $this->pageCount;
    }

    function getFirstPage()
    {
        return $this->firstPage;
    }

    function getLastPage()
    {
        return $this->lastPage;
    }

    function getCount()
    {
        return $this->count;
    }

    /**
     * @return bool|int|null
     */
    public function getRangeFirstPage()
    {
        return $this->rangeFirstPage;
    }

    /**
     * @return bool|int|null
     */
    public function getRangeLastPage()
    {
        return $this->rangeLastPage;
    }

    /**
     * @return int
     */
    public function getRangeLast()
    {
        return $this->rangeLast;
    }
}