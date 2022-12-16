<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SearchManager
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
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine.orm.search_entity_manager');
    }
    
    public function getTodayArticless()
    {
        $dc = $this->em->getConnection();
        $sql = 'SELECT sv.title, sv.entity_id AS id, sv.full_text AS content, sv.instance_code AS "instanceCode" '
            . ' FROM search_view AS sv'
            . " WHERE sv.entity_type = 'article'"
            . " AND sv.published_at >= '" . date('Y-m-d') . "'"
            . " AND sv.published_at < '" . date('Y-m-d', strtotime('tomorrow')) . "'"
//                . ' AND category_id = ' . (int)$categoryId
        ;
//        echo "<pre>";
//        var_dump($sql);
//        echo "</pre>";
//        die;
        return $dc->executeQuery($sql)->fetchAll() ?: [];
    }

    /**
     * @param $params
     * @return array
     */
    public function getSearchData($params)
    {
        $dc = $this->em->getConnection();
        $limitCondition = '';
        $offsetCondition = '';
        $dateCondition = '';
        $instanceCondition = '';
        $orderDirection =  'DESC';

        if(isset($params['limit']) && $params['limit'] > 0) {
            $limitCondition = " LIMIT {$params['limit']}";
        }

        if(isset($params['offset'])) {
            $offsetCondition = " OFFSET {$params['offset']}";
        }

        if(isset($params['publishedStart']) && $params['publishedStart'] !== '') {
            $dateCondition = " AND published_at BETWEEN '{$params['publishedStart']}' AND ";
            if(isset($params['publishedEnd']) && $params['publishedEnd'] !== '') {
                $dateCondition .= "'{$params['publishedEnd']}'";
            }else{
                $dateCondition .= "NOW()";
            }
        }else{
            if(isset($params['publishedEnd']) && $params['publishedEnd'] !== '') {
                $dateCondition = " AND published_at <= '{$params['publishedEnd']}'";
            }
        }

        if(isset($params['instance']) && $params['instance'] !== '' && !empty($params['instance'])) {
            $instanceCondition = " AND instance_code = '{$params['instance']}' ";
        }

        if(isset($params['sortOrder']) && $params['sortOrder'] !== ''
            && !empty($params['sortOrder']) && $params['sortOrder'] == 1) {
            $orderDirection = " ASC";
        }

        $sql = "SELECT id, title, CONCAT(
                    substr(full_text, 0, 500), 
                    CASE WHEN char_length(full_text) > 500 THEN '...' ELSE '' END
                ) as preview_text, link, published_at, rank, entity_type,
                (SELECT COUNT(id) FROM search_view, to_tsquery('russian', '{$params['query']}') q 
                  WHERE tsv @@ q {$dateCondition} {$instanceCondition}) as total_results
                FROM (SELECT id, title, q, full_text, link, published_at, ts_rank_cd(tsv, q, 32) AS rank, entity_type
                      FROM search_view, to_tsquery('russian', '{$params['query']}') q
                      WHERE tsv @@ q
                      {$dateCondition}
                      {$instanceCondition}
                      ORDER BY published_at {$orderDirection}, rank DESC
                      {$limitCondition} {$offsetCondition}) 
                AS foo";

        $result = $dc->fetchAll($sql);

        return $result ?: false;
    }

    public function getAutocompleteWords($params)
    {

        $dc = $this->em->getConnection();
        $sql = "SELECT id, ts_headline(title, q) as title, full_text as preview_text, rank, entity_type
                FROM (SELECT id, title, q, full_text, ts_rank_cd(tsv, q, 32) AS rank, entity_type
                      FROM search_view, to_tsquery('{$params['query']}') q
                      WHERE tsv @@ q
                      ORDER BY rank DESC
                      LIMIT 10) 
                AS foo";
        $result = $dc->fetchAll($sql);

        return $result ?: [];
    }

    /**
     * @param $data
     * @return bool
     */
    public function addData($data = array())
    {
        if(!empty($data)) {
            $dc = $this->em->getConnection();
            $sql = "INSERT INTO search_view(title, full_text, published_at, entity_id, instance_code, entity_type, link) VALUES ";

            foreach($data as $row) {
		$ptn= "!<script[^>]*>(.)*</script>!Uis"; 
                $full_text =preg_replace($ptn, '',  $row['full_text']);
                $full_text = trim(rtrim(ltrim(strip_tags($full_text), '&nbsp; 0x20 0x09 0x0A 0x0D 0x00 0x0B')));
                $sql .= "('{$row['title']}', '{$full_text}', '{$row['published_at']}', {$row['id']}, 
                        '{$row['instance_code']}', '{$row['entity_type']}', '{$row['link']}'), ";
            }

            $sql = substr($sql, 0, -2);

            $result = $dc->exec($sql);
            $dc->close();

            if($result) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * @param $data
     * @return bool
     */
    public function updateData($data = array())
    {
        if(!empty($data)) {
            $dc = $this->em->getConnection();

            foreach($data as $row) {
                $sql = "UPDATE search_view SET (title, full_text, published_at, link) = ";                
		$ptn= "!<script[^>]*>(.)*</script>!Uis"; 
                $full_text =preg_replace($ptn, '',  $row['full_text']);
                $full_text = trim(rtrim(ltrim(strip_tags($full_text), '&nbsp; 0x20 0x09 0x0A 0x0D 0x00 0x0B')));
                $sql .= "('{$row['title']}', '{$full_text}', '{$row['published_at']}', '{$row['link']}')";
                $sql .= " WHERE entity_id = {$row['id']} AND instance_code = '{$row['instance_code']}' AND entity_type = '{$row['entity_type']}' ";
                
                $result = $dc->exec($sql);
            }

            $dc->close();

            if($result) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    public function addDataDocument($data = array())
    {
        if(!empty($data)) {
            $dc = $this->em->getConnection();
            $sql = "INSERT INTO search_document(title, full_text,entity_id, instance_code, document_type, document_number, tags, tags_clean, link, category_id,  published_at, approve_date, is_published, is_deleted) VALUES ";

            foreach($data as $row) {
                $full_text = trim(rtrim(ltrim(strip_tags($row['full_text']), '&nbsp; 0x20 0x09 0x0A 0x0D 0x00 0x0B')));
                $sql .= "('{$row['title']}', '{$full_text}', {$row['entity_id']}, '{$row['instance_code']}', 
                        '{$row['document_type']}', '{$row['document_number']}', '{$row['tags']}', '{$row['tags_clean']}',
                        '{$row['link']}',";
                if(is_null($row['category_id'])) {
                    $sql .= " NULL,";
                }else{
                    $sql .= " '{$row['category_id']}',";
                }
                if(is_null($row['published_at'])) {
                    $sql .= " NULL,";
                }else{
                    $sql .= " '{$row['published_at']}',";
                }
                
                if(is_null($row['approval_date'])) {
                    $sql .= " NULL, ";
                }else{
                    $sql .= " '{$row['approval_date']}', ";
                }
                $sql .= $row['is_published'] ? ' TRUE, ':' FALSE, ';
                $sql .= $row['is_deleted'] ? 'TRUE), ' : 'FALSE), ';
            }
            $sql = substr($sql, 0, -2);

            $result = $dc->exec($sql);
            $dc->close();
            if($result) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * @param $data
     * @return bool
     */
    public function updateDataDocument($data = array())
    {
        if(!empty($data)) {
            $dc = $this->em->getConnection();
            foreach($data as $row) {
                $sql = "UPDATE search_document SET (title, full_text, document_type, document_number, tags, tags_clean, link, category_id,  published_at, approve_date, is_published, is_deleted) = ";
                $full_text = trim(rtrim(ltrim(strip_tags($row['full_text']), '&nbsp; 0x20 0x09 0x0A 0x0D 0x00 0x0B')));
                    
                $sql .= "('{$row['title']}', '{$full_text}',
                        '{$row['document_type']}', '{$row['document_number']}', '{$row['tags']}', '{$row['tags_clean']}',
                        '{$row['link']}',";
                if(is_null($row['category_id'])) {
                    $sql .= " NULL,";
                }else{
                    $sql .= " '{$row['category_id']}',";
                }
                if(is_null($row['published_at'])) {
                    $sql .= " NULL,";
                }else{
                    $sql .= " '{$row['published_at']}',";
                }
                
                if(is_null($row['approval_date'])) {
                    $sql .= " NULL, ";
                }else{
                    $sql .= " '{$row['approval_date']}', ";
                }
                $sql .= $row['is_published'] ? ' TRUE, ':' FALSE, ';
                $sql .= $row['is_deleted'] ? 'TRUE)' : 'FALSE)';
                $sql .= " WHERE entity_id = {$row['entity_id']} AND instance_code = '{$row['instance_code']}' ";
                
                $result = $dc->exec($sql);
                
            }

            $dc->close();
            if($result) {
                return true;
            }
        }
        return false;
    }

    public function getSyncLimits($code)
    {
        $dc = $this->em->getConnection();
        $code = filter_var($code, FILTER_SANITIZE_STRING);
        $sql = "SELECT MAX(entity_id), entity_type FROM search_view 
                WHERE instance_code = '{$code}' GROUP BY entity_type ORDER BY entity_type";
        $result = $dc->fetchAll($sql);

        return $result ?: false;
    }


    public function getDocumentsSyncLimits($code)
    {
        $dc = $this->em->getConnection();
        $code = filter_var($code, FILTER_SANITIZE_STRING);
        $sql = "SELECT MAX(entity_id) FROM search_document
                WHERE instance_code = '{$code}' LIMIT 1";
        $result = $dc->fetchAll($sql);

        return $result ?: [];
    }

    /**
     * @param $params
     * @return array
     */
    public function getSearchDocumentData($params)
    {
        $dc = $this->em->getConnection();
        $limitCondition = '';
        $offsetCondition = '';
        $whereConditions = [];
        $tsvFromCondition = '';
        $rankField = '';
        $qField = '';
        $tsvRankField = '';
        $tsvWhereCondition = '';
        $orderByTsv = '';

        if(isset($params['limit']) && $params['limit'] > 0) {
            $limitCondition = " LIMIT {$params['limit']}";
        }

        if(isset($params['offset'])) {
            $offsetCondition = " OFFSET {$params['offset']}";
        }

        if(isset($params['documentType'])) {
            $whereConditions[] = "document_type = {$params['documentType']}";
        }

        if(isset($params['dateFrom']) && $params['dateFrom'] !== '') {
            $dateCondition = "approve_date BETWEEN '{$params['dateFrom']}' AND ";
            if(isset($params['dateTo']) && $params['dateTo'] !== '') {
                $dateCondition .= "'{$params['dateTo']}'";
            }else{
                $dateCondition .= "NOW()";
            }
            $whereConditions[] = $dateCondition;
        }else{
            if(isset($params['dateTo']) && $params['dateTo'] !== '') {
                $whereConditions[] = "approve_date <= '{$params['dateTo']}'";
            }
        }

        if(isset($params['instance'])) {
            $whereConditions[] = "instance_code = '{$params['instance']}'";
        }

        $whereConditionsString = implode(" AND ", $whereConditions);

        $totalResultsCondition = ",(SELECT COUNT(id) FROM search_document WHERE {$whereConditionsString}) as total_results";

        if(isset($params['tsv']) && !empty($params['tsv']) && $params['tsv'] !== '') {

            $tsvFromCondition = ", to_tsquery('russian', '{$params['tsv']}') q";
            $rankField = " rank,";
            $qField = "  q,";
            $tsvRankField = "  ts_rank_cd(tsv, q, 32) AS rank,";
            $tsvWhereCondition = "tsv @@ q";
            $orderByTsv = ", rank DESC";

            if(!empty($whereConditionsString) && $whereConditionsString !== '') {
                $tsvWhereCondition .= " AND ";
            }

            $totalResultsCondition = " ,(SELECT COUNT(id) FROM search_document"
                . ", to_tsquery('russian', '{$params['tsv']}') q WHERE {$tsvWhereCondition} {$whereConditionsString})"
                . " as total_results";
        }

        $sql = "SELECT id, title, CONCAT(substr(full_text, 0, 500),"
                . " CASE WHEN char_length(full_text) > 500 THEN '...' ELSE '' END) as preview_text, link, approve_date,"
                . " published_at as pub_date, {$rankField} tags_clean {$totalResultsCondition}"
                . " FROM (SELECT id, title, {$qField} full_text, link, approve_date, published_at, {$tsvRankField} tags_clean"
                      . " FROM search_document {$tsvFromCondition} WHERE {$tsvWhereCondition} {$whereConditionsString}"
                      . " ORDER BY approve_date DESC NULLS LAST, published_at DESC NULLS LAST {$orderByTsv} {$limitCondition} {$offsetCondition})"
                . " AS foo";

        $result = $dc->fetchAll($sql);

        return $result ?: [];
    }
}
