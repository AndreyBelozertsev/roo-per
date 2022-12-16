<?php

namespace Portal\ContentBundle\Repository;

use Portal\ContentBundle\Entity\Document;

/**
 * DocumentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DocumentRepository extends \Doctrine\ORM\EntityRepository
{
    public function getMoreDocumentList($page = 0, $lastTime = null)
    {
        if ($lastTime === null) {
            $lastTime = new \DateTime();
            $lastTime = $lastTime->format('Y-m-d H:i:s');
        } else {
            $lastTime = new \DateTime($lastTime);
            $lastTime = $lastTime->format('Y-m-d H:i:s');
        }
        $dc = $this->getEntityManager()->getConnection();
        $sql = 'SELECT 
                d.id AS document_id, 
                d.title AS document_title, 
                dc.title AS category_name,
                d.approval_date AS document_approval_date, 
                d.published_at AS document_created_at, 
                d.published_at AS document_published_at,
                (CASE WHEN d.approval_date IS NOT NULL THEN d.approval_date ELSE d.published_at END) AS date_case,
                (SELECT regexp_replace(
                    regexp_replace(d.document_number, \'[0-9]+\', repeat(\'0\',10) || \'\&\', \'g\'), 
                      \'[0-9]*([0-9]{\' || 10 || \'})\', 
                      \'\1\', 
                      \'g\'
                    )
                ) as number_document 
                FROM document AS d
                LEFT JOIN document_category AS dc ON d.category_id = dc.id
                WHERE d.document_type = ' . Document::TYPE_PUBLIC_DOCUMENT . '
                    AND d.is_deleted IS FALSE
                    AND d.is_published IS TRUE
                    AND d.published_at <= \'' . $lastTime . '\''
                . ' ORDER BY date_case DESC, number_document DESC, d.published_at DESC, d.id DESC '
//                . ' ORDER BY d.published_at DESC, d.id DESC'
                . ' LIMIT ' . Document::DOCUMENTS_LIMIT_ON_PAGE . '
                OFFSET ' . (int)$page * Document::DOCUMENTS_LIMIT_ON_PAGE
        ;

        return $dc->fetchAll($sql) ?: [];
    }

    /**
     * @param integer $structureId
     * @param integer $page
     *
     * @return array
     */
    public function getPublishedDocumentList($structureId, $page = null)
    {
        $dc = $this->getEntityManager()->getConnection();

        $pageLimit = '';
        if ($page !== null) {
            $pageLimit = ' LIMIT ' . Document::DOCUMENTS_LIMIT_ON_PAGE . '
                OFFSET ' . (int)$page * Document::DOCUMENTS_LIMIT_ON_PAGE;
        }

        $sql = 'SELECT 
                d.id, 
                d.title, 
                d.approval_date,
                (CASE WHEN d.approval_date IS NOT NULL THEN d.approval_date ELSE d.published_at END) AS date_case,
                (SELECT regexp_replace(
                    regexp_replace(d.document_number, \'[0-9]+\', repeat(\'0\',10) || \'\&\', \'g\'), 
                      \'[0-9]*([0-9]{\' || 10 || \'})\', 
                      \'\1\', 
                      \'g\'
                    )
                ) as number_document, 
                d.published_at
                FROM document AS d
                WHERE d.document_type = ' . Document::TYPE_PUBLIC_DOCUMENT . '
                    AND d.menu_node_id = ' . (int)$structureId . '
                    AND d.is_deleted IS FALSE
                    AND d.is_published = TRUE '
                . ' ORDER BY date_case DESC, number_document DESC, d.published_at DESC, d.id DESC'
//                . ' ORDER BY d.published_at DESC, d.id DESC'
            . $pageLimit
        ;

        return $dc->executeQuery($sql)->fetchAll() ?: [];
    }

    /**
     * @param integer $structureId
     * @return integer|boolean
     */
    public function getPublishedDocumentCount($structureId)
    {
        $dc = $this->getEntityManager()->getConnection();
        $sql = 'SELECT 
                COUNT(d.id) 
                FROM document AS d
                WHERE d.document_type = ' . Document::TYPE_PUBLIC_DOCUMENT . '
                    AND d.menu_node_id = ' . (int)$structureId . '
                    AND d.is_deleted IS FALSE
                    AND d.is_published = TRUE '
        ;

        return $dc->executeQuery($sql)->fetchColumn() ?: false;
    }

    public function getDocumentListCount()
    {
        $dc = $this->getEntityManager()->getConnection();
        $date = new \DateTime();
        $currentDay = $date->format('Y-m-d H:i:s');

        $sql = 'SELECT COUNT(d.id) AS count
                FROM document AS d
                WHERE d.document_type = ' . Document::TYPE_PUBLIC_DOCUMENT . '
                    AND d.published_at <= \'' . $currentDay . '\'
                    AND d.is_deleted IS FALSE
                    AND d.is_published IS TRUE'
        ;

        return $dc->executeQuery($sql)->fetchColumn() ?: false;
    }

    public function getAllDocumentForPagination($filterParam)
    {
        $dc = $this->getEntityManager()->getConnection();

        $params = [];
        if (isset($filterParam['filterTitle']) && $filterParam['filterTitle'] !== '') {
            $params[] = 'LOWER(d.title) LIKE \'%' . mb_strtolower($filterParam['filterTitle'], 'UTF-8') . '%\'';
        }
        if (isset($filterParam['filterDocType']) && (int)$filterParam['filterDocType'] !== 0) {
            $params[] = 'd.document_type = ' . (int)$filterParam['filterDocType'];
        }
        if (isset($filterParam['filterPublishFrom']) && $filterParam['filterPublishFrom'] !== '') {
            $params[] = 'd.published_at >= \'' . date('d.m.Y', strtotime($filterParam['filterPublishFrom'])) . '\'';
        }
        if (isset($filterParam['filterPublishTo']) && $filterParam['filterPublishTo'] !== '') {
            $params[] = 'd.published_at <= \'' . date('d.m.Y', strtotime($filterParam['filterPublishTo'] . '+1 day')) . '\'';
        }
        if (isset($filterParam['filterApprovFrom']) && $filterParam['filterApprovFrom'] !== '') {
            $params[] = 'd.approval_date >= \'' . date('d.m.Y', strtotime($filterParam['filterApprovFrom'])) . '\'';
        }
        if (isset($filterParam['filterApprovTo']) && $filterParam['filterApprovTo'] !== '') {
            $params[] = 'd.approval_date <= \'' . date('d.m.Y', strtotime($filterParam['filterApprovTo'] . '+1 day')) . '\'';
        }

        $published = (isset($filterParam['filterPublished']) && $filterParam['filterPublished'] === 'true');
        $notPublished = (isset($filterParam['filterPublished']) && $filterParam['filterNotPublished'] === 'true');
        if ($published xor $notPublished) {
            $params[] = 'd.is_published IS ' . ($published ? 'true' : 'false');
        }

        $where = (count($params) !== 0) ? ' WHERE ' . implode(' AND ', $params) : '';

        $sql = 'SELECT 
                    d.id, d.title, 
                    (CASE WHEN d.approval_date IS NOT NULL THEN d.approval_date ELSE d.published_at END) AS date_case,
                    (SELECT regexp_replace(
                        regexp_replace(d.document_number, \'[0-9]+\', repeat(\'0\',10) || \'\&\', \'g\'), 
                          \'[0-9]*([0-9]{\' || 10 || \'})\', 
                          \'\1\', 
                          \'g\'
                        )
                    ) as number_document, 
                    CONCAT(
                        substr(d.content, 0, 240), 
                        CASE WHEN char_length(d.content) > 240 THEN \'...\' ELSE \'\' END
                    ) as content, 
                    d.published_at, 
                    d.approval_date, 
                    d.document_type, 
                    d.author_id, d.is_deleted'
            . ' FROM document AS d'
            . $where
            . ' ORDER BY date_case DESC, number_document DESC, d.published_at DESC, d.id DESC'
//            . ' ORDER BY d.published_at DESC, d.id DESC'
        ;

        return $dc->fetchAll($sql) ?: [];
    }

    public function getAllDocumentsForSearchGrab($minId)
    {
        $dc = $this->getEntityManager()->getConnection();
        $minId = (int) $minId;
        $sql = "SELECT 
                  d.id, 
                  d.title, 
                  d.content, 
                  d.published_at, 
                  d.document_type, 
                  d.slug, 
                  d.approval_date,
                  (CASE WHEN d.approval_date IS NOT NULL THEN d.approval_date ELSE d.published_at END) AS date_case,
                  (SELECT regexp_replace(
                    regexp_replace(d.document_number, '[0-9]+', repeat('0',10) || '\&', 'g'), 
                      '[0-9]*([0-9]{' || 10 || '})', 
                      '\1', 
                      'g'
                    )
                  ) as number_document 
                FROM document AS d WHERE d.id > {$minId} "
                . " ORDER BY date_case DESC, number_document DESC, d.published_at DESC, d.id DESC"
//                . ' ORDER BY d.published_at DESC, d.id DESC'
                        ;

        return $dc->fetchAll($sql) ?: [];
    }
    
    public function getAllDocumentsForSearchUpdate($maxId)
    {
        $dc = $this->getEntityManager()->getConnection();
        $maxId = (int) $maxId;
        $sql = "SELECT 
                  d.id, 
                  d.title, 
                  d.content, 
                  d.published_at, 
                  d.document_type, 
                  d.slug, 
                  d.approval_date,
                  (CASE WHEN d.approval_date IS NOT NULL THEN d.approval_date ELSE d.published_at END) AS date_case,
                  (SELECT regexp_replace(
                    regexp_replace(d.document_number, '[0-9]+', repeat('0',10) || '\&', 'g'), 
                      '[0-9]*([0-9]{' || 10 || '})', 
                      '\1', 
                      'g'
                    )
                  ) as number_document 
                FROM document AS d 
                WHERE d.id <= {$maxId} AND d.is_search_indexed = FALSE "
                . " ORDER BY date_case DESC, number_document DESC, d.published_at DESC, d.id DESC"
//                . ' ORDER BY d.published_at DESC, d.id DESC'
                        ;

        return $dc->fetchAll($sql) ?: [];
    }

    public function getAllDocumentsForSearchDocumentGrab($minId)
    {
        $dc = $this->getEntityManager()->getConnection();

        $minId = (int) $minId;
        $sql = "SELECT 
                  d.*, 
                  string_agg(t.tag,'|' order by t.tag) as tags,
                  (CASE WHEN d.approval_date IS NOT NULL THEN d.approval_date ELSE d.published_at END) AS date_case,
                  (SELECT regexp_replace(
                    regexp_replace(d.document_number, '[0-9]+', repeat('0',10) || '\&', 'g'), 
                      '[0-9]*([0-9]{' || 10 || '})', 
                      '\1', 
                      'g'
                    )
                  ) as number_document 
                FROM document as d 
                LEFT JOIN document_to_tag as dtt ON dtt.document_id = d.id 
                LEFT JOIN tag as t ON t.id = dtt.document_tag_id 
                WHERE d.id > {$minId} 
                GROUP BY d.id "
                 . " ORDER BY date_case DESC, number_document DESC, d.published_at DESC, d.id DESC"
//                . ' ORDER BY d.published_at DESC, d.id DESC'
        ;

        $result = $dc->fetchAll($sql);

        return  $result ?: [];
    }

    public function getAllDocumentsForSearchDocumentUpdate($maxId)
    {
        $dc = $this->getEntityManager()->getConnection();

        $maxId = (int) $maxId;
        $sql = "SELECT 
                  d.*, 
                  string_agg(t.tag,'|' order by t.tag) as tags,
                  (CASE WHEN d.approval_date IS NOT NULL THEN d.approval_date ELSE d.published_at END) AS date_case,
                  (SELECT regexp_replace(
                    regexp_replace(d.document_number, '[0-9]+', repeat('0',10) || '\&', 'g'), 
                      '[0-9]*([0-9]{' || 10 || '})', 
                      '\1', 
                      'g'
                    )
                  ) as number_document 
                FROM document as d 
                LEFT JOIN document_to_tag as dtt ON dtt.document_id = d.id 
                LEFT JOIN tag as t ON t.id = dtt.document_tag_id 
                WHERE d.id <= {$maxId} AND d.is_search_document_indexed = FALSE 
                GROUP BY d.id "
                 . " ORDER BY date_case DESC, number_document DESC, d.published_at DESC, d.id DESC"
//                . ' ORDER BY d.published_at DESC, d.id DESC'
        ;

        $result = $dc->fetchAll($sql);

        return  $result ?: [];
    }
    
    /**
     * @param array $indexedDocumentsId
     * @return bool
     */
    public function updateDocumentsIsSearchDocumentIndexedFlag($indexedDocumentsId)
    {
        $dc = $this->getEntityManager()->getConnection();
        $result = FALSE;
        
        if (!empty($indexedDocumentsId)) {
            $sql = 'UPDATE document SET is_search_document_indexed = TRUE
                    WHERE id IN (' . implode(', ', $indexedDocumentsId) . ') ';
            $result = $dc->exec($sql);
        }
        
        if($result) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * @param array $indexedDocumentsId
     * @return bool
     */
    public function updateDocumentsIsSearchIndexedFlag($indexedDocumentsId)
    {
        $dc = $this->getEntityManager()->getConnection();
        $result = FALSE;
        
        if (!empty($indexedDocumentsId)) {
            $sql = 'UPDATE document SET is_search_indexed = TRUE
                    WHERE id IN (' . implode(', ', $indexedDocumentsId) . ') ';
            $result = $dc->exec($sql);
        }
        
        if($result) {
            return true;
        } else {
            return false;
        }
    }
}